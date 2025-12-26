<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Page Redesign - Laravel Blade</title>
    
    <!-- Using a nice system font stack -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* 
         * RESET & BASE STYLES 
         * Scoped to the main container to avoid conflicts if copied into a larger stylesheet.
         */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #2c3e50;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* 
         * MAIN LAYOUT 
         */
        .main-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
            background: radial-gradient(circle at top right, #fff0f1, #f8f9fa);
        }

        .error-card {
            background: white;
            width: 100%;
            max-width: 900px;
            min-height: 500px;
            border-radius: 24px;
            box-shadow: 0 20px 50px -10px rgba(141, 45, 54, 0.15);
            display: flex;
            flex-direction: row;
            overflow: hidden;
            position: relative;
            z-index: 10;
        }

        /* 
         * LEFT SIDE: ILLUSTRATION 
         */
        .error-visual {
            flex: 1;
            background: #fdf2f3;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* The CSS Art Ghost Animation */
        .ghost-container {
            font-size: 12vw; /* Responsive text size */
            font-weight: 800;
            color: #8d2d36;
            line-height: 1;
            display: flex;
            align-items: center;
            position: relative;
            z-index: 2;
            animation: float 6s ease-in-out infinite;
        }

        /* Specific styling for the '0' which acts as the ghost */
        .ghost-zero {
            position: relative;
            color: #8d2d36;
            width: 0.8em;
            height: 1em;
            background: #8d2d36;
            border-radius: 50%;
            margin: 0 0.05em;
            /* Creating the ghost shape */
            border-top-left-radius: 50%;
            border-top-right-radius: 50%;
            border-bottom-left-radius: 5% 20%;
            border-bottom-right-radius: 5% 20%;
            box-shadow: 0 0 0 0.05em #8d2d36; /* Outline adjustment */
        }

        /* Ghost Face */
        .ghost-face {
            position: absolute;
            top: 35%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60%;
            height: 30%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
        }

        .eye {
            width: 12px;
            height: 18px;
            background: white;
            border-radius: 50%;
            animation: blink 4s infinite;
        }

        .eye.left { animation-delay: 0.2s; }
        .eye.right { animation-delay: 0.1s; }

        /* Decorative background blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.6;
            z-index: 1;
        }
        .blob-1 {
            width: 200px;
            height: 200px;
            background: #ffccd0;
            top: -20px;
            left: -20px;
            animation: moveBlob 10s infinite alternate;
        }
        .blob-2 {
            width: 150px;
            height: 150px;
            background: #e6e6e6;
            bottom: -20px;
            right: -20px;
            animation: moveBlob 8s infinite alternate-reverse;
        }

        /* 
         * RIGHT SIDE: CONTENT 
         */
        .error-content {
            flex: 1;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
        }

        .error-tag {
            background: #fff0f1;
            color: #8d2d36;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }

        .error-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #2c3e50;
            margin: 0 0 15px 0;
            line-height: 1.2;
        }

        .error-desc {
            font-size: 1.1rem;
            color: #6c757d;
            line-height: 1.7;
            margin-bottom: 35px;
            max-width: 90%;
        }

        /* Action Buttons */
        .action-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            cursor: pointer;
        }

        .btn-primary {
            background-color: #8d2d36;
            color: white;
            border: 2px solid #8d2d36;
            box-shadow: 0 4px 14px rgba(141, 45, 54, 0.3);
        }

        .btn-primary:hover {
            background-color: #a84a54;
            border-color: #a84a54;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(141, 45, 54, 0.4);
        }

        .btn-outline {
            background-color: transparent;
            color: #8d2d36;
            border: 2px solid #e9ecef;
        }

        .btn-outline:hover {
            border-color: #8d2d36;
            color: #8d2d36;
            background-color: #fff0f1;
        }

        .btn i {
            margin-right: 8px;
        }

        /* 
         * CODE PREVIEW BOX (For the user to copy the source)
         */
        .code-reveal {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #2c3e50;
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            z-index: 100;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .code-reveal:hover {
            transform: translateY(-2px);
        }

        .code-modal-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px);
            z-index: 200;
            display: none;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .code-modal {
            background: #1e1e1e;
            width: 90%;
            max-width: 800px;
            height: 80vh;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            transform: scale(0.95);
            transition: transform 0.3s;
        }

        .code-modal.active {
            transform: scale(1);
        }

        .code-header {
            background: #2d2d2d;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #404040;
        }

        .code-title {
            color: #fff;
            font-family: monospace;
            font-size: 0.9rem;
        }

        .close-btn {
            background: none;
            border: none;
            color: #aaa;
            font-size: 1.2rem;
            cursor: pointer;
        }
        .close-btn:hover { color: white; }

        .code-body {
            flex: 1;
            overflow: auto;
            padding: 20px;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.9rem;
            color: #d4d4d4;
            white-space: pre;
        }

        /* Syntax highlighting mockup */
        .blade-directive { color: #569cd6; } /* blue */
        .blade-bracket { color: #808080; } /* grey */
        .html-tag { color: #569cd6; } /* blue */
        .html-attr { color: #9cdcfe; } /* light blue */
        .html-string { color: #ce9178; } /* orange */
        .css-prop { color: #9cdcfe; } 
        .css-val { color: #ce9178; }

        /* 
         * ANIMATIONS 
         */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        @keyframes blink {
            0%, 96%, 100% { transform: scaleY(1); }
            98% { transform: scaleY(0.1); }
        }

        @keyframes moveBlob {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(20px, -20px) scale(1.1); }
        }

        /* 
         * RESPONSIVE 
         */
        @media (max-width: 768px) {
            .error-card {
                flex-direction: column;
                min-height: auto;
            }
            .error-visual {
                padding: 60px 20px;
            }
            .ghost-container {
                font-size: 20vw;
            }
            .error-content {
                padding: 40px 20px;
                align-items: center;
                text-align: center;
            }
            .action-group {
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <!-- MAIN CONTENT WRAPPER -->
    <main class="main-wrapper">
        
        <div class="error-card">
            
            <!-- Visual Area -->
            <div class="error-visual">
                <div class="blob blob-1"></div>
                <div class="blob blob-2"></div>
                
                <div class="ghost-container">
                    <span>4</span>
                    
                    <!-- CSS Ghost Construction -->
                    <div class="ghost-zero">
                        <div class="ghost-face">
                            <div class="eye left"></div>
                            <div class="eye right"></div>
                        </div>
                    </div>
                    
                    <span>4</span>
                </div>
            </div>

            <!-- Text Area -->
            <div class="error-content">
                <span class="error-tag">Error 404</span>
                <h1 class="error-title">Looks like you're lost.</h1>
                
                <p class="error-desc">
                    The page you are looking for doesn't exist or has been moved. 
                    Don't worry, you can easily find your way back.
                </p>
                
                <div class="action-group">
                    <!-- In real Blade file, this uses {{ route('dashboard') }} -->
                    <a href="dashboard" class="btn btn-primary">
                        <i class="fas fa-home"></i> Go to Dashboard
                    </a>
                    
                    <!-- In real Blade file, this uses {{ url()->previous() }} -->
                    <a href="dashboard" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Go Back
                    </a>
                </div>
            </div>

        </div>

    </main>

    <!-- UI ELEMENT TO REVEAL BLADE CODE -->
    <div class="code-reveal" id="revealBtn">
        <i class="fas fa-code"></i> Get Blade Code
    </div>

    <!-- MODAL FOR CODE DISPLAY -->
    <div class="code-modal-overlay" id="modalOverlay">
        <div class="code-modal" id="modalContent">
            <div class="code-header">
                <span class="code-title">@section('content') ... @endsection</span>
                <button class="close-btn" id="closeModal">&times;</button>
            </div>
            <div class="code-body" id="codeSource"></div>
        </div>
    </div>

    <!-- HIDDEN SOURCE FOR MODAL (The actual content to be copied) -->
    <script type="text/plain" id="blade-raw-source">
@section('content')
<style>
    /* Modern 404 Styles */
    .custom-404-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 70vh;
        padding: 20px;
        background-color: #f8f9fa;
        font-family: 'Inter', sans-serif;
    }

    .custom-404-card {
        background: white;
        width: 100%;
        max-width: 900px;
        border-radius: 24px;
        box-shadow: 0 20px 50px -10px rgba(141, 45, 54, 0.15);
        display: flex;
        overflow: hidden;
        flex-direction: row;
    }

    /* Visual Side */
    .custom-404-visual {
        flex: 1;
        background: #fdf2f3;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        padding: 40px;
    }

    .custom-ghost-text {
        font-size: 10vw;
        font-weight: 800;
        color: #8d2d36;
        display: flex;
        align-items: center;
        line-height: 1;
        animation: float 6s ease-in-out infinite;
    }

    .custom-ghost-circle {
        position: relative;
        width: 0.8em;
        height: 1em;
        background: #8d2d36;
        margin: 0 0.05em;
        border-radius: 40% 40% 50% 50% / 50% 50% 10% 10%;
    }

    .custom-eyes {
        position: absolute;
        top: 35%;
        left: 50%;
        transform: translateX(-50%);
        width: 70%;
        display: flex;
        justify-content: space-around;
    }

    .custom-eye {
        width: 15%;
        height: 25%;
        background: white;
        border-radius: 50%;
        animation: blink 4s infinite;
    }

    /* Content Side */
    .custom-404-content {
        flex: 1;
        padding: 60px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .custom-badge {
        background: #fff0f1;
        color: #8d2d36;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        align-self: flex-start;
        margin-bottom: 15px;
    }

    .custom-title {
        font-size: 2.5rem;
        color: #2c3e50;
        font-weight: 800;
        margin-bottom: 15px;
    }

    .custom-text {
        font-size: 1.1rem;
        color: #6c757d;
        line-height: 1.6;
        margin-bottom: 30px;
    }

    .custom-actions {
        display: flex;
        gap: 15px;
    }

    .custom-btn {
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s;
    }

    .custom-btn i { margin-right: 8px; }

    .btn-primary-custom {
        background-color: #8d2d36;
        color: white;
    }

    .btn-primary-custom:hover {
        background-color: #a84a54;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(141, 45, 54, 0.3);
    }

    .btn-secondary-custom {
        background-color: transparent;
        border: 1px solid #ced4da;
        color: #2c3e50;
    }

    .btn-secondary-custom:hover {
        background-color: #f8f9fa;
        border-color: #adb5bd;
    }

    /* Animations */
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    @keyframes blink {
        0%, 96%, 100% { transform: scaleY(1); }
        98% { transform: scaleY(0.1); }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .custom-404-card { flex-direction: column; }
        .custom-404-content { padding: 40px 20px; text-align: center; }
        .custom-badge { align-self: center; }
        .custom-actions { justify-content: center; }
    }
</style>

<div class="custom-404-wrapper">
    <div class="custom-404-card">
        <!-- Left: Visual -->
        <div class="custom-404-visual">
            <div class="custom-ghost-text">
                <span>4</span>
                <div class="custom-ghost-circle">
                    <div class="custom-eyes">
                        <div class="custom-eye"></div>
                        <div class="custom-eye"></div>
                    </div>
                </div>
                <span>4</span>
            </div>
        </div>

        <!-- Right: Content -->
        <div class="custom-404-content">
            <span class="custom-badge">Error 404</span>
            <h1 class="custom-title">Looks like you're lost.</h1>
            <p class="custom-text">
                The page you are looking for might have been removed, had its name changed, 
                or is temporarily unavailable.
            </p>
            
            <div class="custom-actions">
                <a href="{{ route('dashboard') }}" class="custom-btn btn-primary-custom">
                    <i class="fas fa-home"></i> Go to Dashboard
                </a>
                
                <a href="{{ url()->previous() }}" class="custom-btn btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i> Go Back
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
    </script>

    <script>
        // Logic for the Modal
        const revealBtn = document.getElementById('revealBtn');
        const modalOverlay = document.getElementById('modalOverlay');
        const modalContent = document.getElementById('modalContent');
        const closeModal = document.getElementById('closeModal');
        const codeSource = document.getElementById('codeSource');
        const rawSource = document.getElementById('blade-raw-source').textContent;

        // Simple Syntax Highlighting for the modal display
        function highlightSyntax(code) {
            return code
                .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;") // Escape HTML
                .replace(/(@\w+)/g, '<span class="blade-directive">$1</span>') // Blade directives
                .replace(/(\{\{.*?\}\})/g, '<span class="blade-directive">$1</span>') // Blade echo
                .replace(/(&lt;\/?)(\w+)(.*?)(\/?&gt;)/g, '<span class="blade-bracket">$1</span><span class="html-tag">$2</span>$3<span class="blade-bracket">$4</span>'); // HTML Tags
        }

        revealBtn.addEventListener('click', () => {
            codeSource.innerHTML = highlightSyntax(rawSource);
            modalOverlay.style.display = 'flex';
            // Slight delay to allow display flex to apply before opacity transition
            setTimeout(() => {
                modalOverlay.style.opacity = '1';
                modalContent.classList.add('active');
            }, 10);
        });

        const hideModal = () => {
            modalOverlay.style.opacity = '0';
            modalContent.classList.remove('active');
            setTimeout(() => {
                modalOverlay.style.display = 'none';
            }, 300);
        };

        closeModal.addEventListener('click', hideModal);
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) hideModal();
        });
    </script>
</body>
</html>