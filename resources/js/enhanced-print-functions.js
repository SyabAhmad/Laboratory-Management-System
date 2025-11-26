// Enhanced Print Function with Better Error Handling
// Add this to your Blade templates to improve print reliability

// Enhanced printTest function with error handling and URL encoding
function printTest(e, url) {
    if (e && e.preventDefault) e.preventDefault();

    console.log("Print request URL:", url);

    // Validate URL
    try {
        new URL(url, window.location.origin);
    } catch (error) {
        console.error("Invalid print URL:", url);
        alert("Invalid print URL generated. Please contact support.");
        return;
    }

    fetch(url, {
        credentials: "include",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
        },
    })
        .then((response) => {
            console.log("Print response status:", response.status);

            if (!response.ok) {
                // Try to parse error response
                return response.text().then((errorText) => {
                    let errorMessage = `Print failed with status ${response.status}`;

                    try {
                        const errorJson = JSON.parse(errorText);
                        if (errorJson.message) {
                            errorMessage = errorJson.message;
                        }
                    } catch (parseError) {
                        // If not JSON, might be HTML error page
                        if (errorText.includes("404")) {
                            errorMessage =
                                "Test report not found (404). Please check if the test data exists.";
                        } else if (errorText.includes("500")) {
                            errorMessage =
                                "Server error occurred while generating print view.";
                        }
                    }

                    throw new Error(errorMessage);
                });
            }

            const type = (
                response.headers.get("content-type") || ""
            ).toLowerCase();
            if (type.indexOf("application/pdf") !== -1) {
                return response.blob().then((b) => ({ pdf: b }));
            }
            return response.text().then((t) => ({ html: t }));
        })
        .then((result) => {
            if (result.pdf) {
                const blobUrl = URL.createObjectURL(result.pdf);
                const iframe = document.createElement("iframe");
                iframe.style.position = "fixed";
                iframe.style.right = "0";
                iframe.style.bottom = "0";
                iframe.style.width = "0";
                iframe.style.height = "0";
                iframe.style.border = "0";
                iframe.style.visibility = "hidden";
                iframe.src = blobUrl;
                document.body.appendChild(iframe);
                iframe.onload = function () {
                    try {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    } catch (err) {
                        console.error("Print failed", err);
                        alert(
                            "Print function failed. Please try using your browser's print button (Ctrl+P)."
                        );
                    }
                    setTimeout(() => {
                        URL.revokeObjectURL(blobUrl);
                        document.body.removeChild(iframe);
                    }, 1500);
                };
                return;
            }

            // Handle HTML response
            const sanitized = result.html.replace(
                /window\.print\s*\(\s*\)\s*;?/g,
                ""
            );
            const iframe = document.createElement("iframe");
            iframe.style.position = "fixed";
            iframe.style.right = "0";
            iframe.style.bottom = "0";
            iframe.style.width = "0";
            iframe.style.height = "0";
            iframe.style.border = "0";
            iframe.style.visibility = "hidden";
            document.body.appendChild(iframe);

            const idoc = iframe.contentWindow.document;
            idoc.open();
            idoc.write(sanitized);
            idoc.close();

            iframe.onload = function () {
                try {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                } catch (err) {
                    console.error("Print failed", err);
                    alert(
                        "Print function failed. Please try using your browser's print button (Ctrl+P)."
                    );
                }
                setTimeout(() => {
                    document.body.removeChild(iframe);
                }, 1500);
            };
        })
        .catch((err) => {
            console.error("Failed to load print view", err);

            // Show user-friendly error message
            let userMessage = "Unable to generate print view. ";

            if (err.message.includes("404")) {
                userMessage +=
                    "The test report was not found. Please check if the test data exists and try again.";
            } else if (err.message.includes("500")) {
                userMessage +=
                    "A server error occurred. Please try again later or contact support.";
            } else {
                userMessage +=
                    err.message + " Please try again or contact support.";
            }

            alert(userMessage);

            // Optionally redirect to a debug page
            if (typeof openPrintModal === "function") {
                console.log(
                    "Consider using openPrintModal for better error handling"
                );
            }
        });
}

// Enhanced openPrintModal function with better error handling
function openPrintModal(event, link) {
    event.preventDefault();
    const href = link ? link.href : event.currentTarget.href;

    console.log("Print modal URL:", href);

    // Validate URL
    try {
        new URL(href, window.location.origin);
    } catch (error) {
        console.error("Invalid print URL:", href);
        alert("Invalid print URL generated. Please contact support.");
        return false;
    }

    // Create modal if not exists
    if (!$("#printModal").length) {
        $("body").append(`
            <div class="modal fade" id="printModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Print Preview - Test Report</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                            <div id="printLoading" style="text-align: center; padding: 20px;">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Loading print preview...</p>
                            </div>
                            <iframe id="printFrame" style="width: 100%; height: 600px; border: none; display: none;"></iframe>
                            <div id="printError" style="display: none; color: red; text-align: center; padding: 20px;">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p>Failed to load print preview. Please try again.</p>
                                <button type="button" class="btn btn-primary" onclick="retryPrint()">
                                    <i class="fas fa-redo"></i> Retry
                                </button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="printIframeContent()">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    // Remove old listener if exists
    window.removeEventListener("message", handleCloseMessage);

    // Add listener for close message from iframe
    window.addEventListener("message", handleCloseMessage);

    // Show loading state
    $("#printLoading").show();
    $("#printFrame").hide();
    $("#printError").hide();

    // Load content in iframe
    const iframe = document.getElementById("printFrame");
    iframe.onload = function () {
        $("#printLoading").hide();
        $("#printFrame").show();
    };
    iframe.onerror = function () {
        $("#printLoading").hide();
        $("#printError").show();
    };

    iframe.src = href;

    // Show modal
    new bootstrap.Modal(document.getElementById("printModal")).show();

    return false;
}

// Helper functions for enhanced print modal
function printIframeContent() {
    const iframe = document.getElementById("printFrame");
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    }
}

function retryPrint() {
    const iframe = document.getElementById("printFrame");
    if (iframe && iframe.src) {
        $("#printLoading").show();
        $("#printError").hide();
        iframe.src = iframe.src; // Reload
    }
}

function handleCloseMessage(event) {
    if (event.data && event.data.action === "closeModal") {
        var modalElement = document.getElementById("printModal");
        if (modalElement) {
            var modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        }
    }
}

// Enhanced test name encoding for URL generation
function encodeTestName(testName) {
    return encodeURIComponent(testName)
        .replace(/'/g, "%27")
        .replace(/"/g, "%22")
        .replace(/\(/g, "%28")
        .replace(/\)/g, "%29")
        .replace(/\+/g, "%2B");
}

// Override existing printTest function globally
if (typeof window.printTest === "function") {
    window.originalPrintTest = window.printTest;
    window.printTest = function (e, url) {
        // Add timestamp to prevent caching issues
        const separator = url.includes("?") ? "&" : "?";
        const cacheBuster = separator + "_t=" + Date.now();
        return printTest(e, url + cacheBuster);
    };
}
