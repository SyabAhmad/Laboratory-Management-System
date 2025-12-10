// Debug script for patient registration form
(function() {
    'use strict';

    console.log('Debug script loaded');

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM content loaded');

        var form = document.getElementById('patientRegistrationForm');
        var checkboxes = document.querySelectorAll('input.test-checkbox');
        var container = document.getElementById('test-category-container');

        console.log('Form found:', !!form);
        console.log('Container found:', !!container);
        console.log('Checkboxes found:', checkboxes.length);

        if (!form || !container) {
            console.error('Form or container not found');
            return;
        }

        // Function to combine age fields into a single string
        function combineAgeFields() {
            var years = document.getElementById('age_years').value || '0';
            var months = document.getElementById('age_months').value || '0';
            var days = document.getElementById('age_days').value || '0';

            var ageParts = [];
            if (years && years !== '0') ageParts.push(years + 'Y');
            if (months && months !== '0') ageParts.push(months + 'M');
            if (days && days !== '0') ageParts.push(days + 'D');

            var ageString = ageParts.length > 0 ? ageParts.join(' ') : '0Y';
            document.getElementById('age').value = ageString;

            console.log('Combined age:', ageString);
        }

        // Function to update hidden inputs with selected test values
        function updateTestCategory() {
            console.log('updateTestCategory called');
            console.log('Total checkboxes found:', checkboxes.length);

            container.innerHTML = ''; // Clear previous hidden inputs
            console.log('Container cleared');

            var selected = [];
            for (var i = 0; i < checkboxes.length; i++) {
                console.log('Checkbox', i, 'checked:', checkboxes[i].checked, 'value:', checkboxes[i].value);

                if (checkboxes[i].checked) {
                    selected.push(checkboxes[i].value);
                    console.log('Selected test:', checkboxes[i].value);

                    // Create a hidden input for each selected test
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'test_category[]'; // Note the [] — this makes it an array in PHP
                    input.value = checkboxes[i].value;
                    container.appendChild(input);
                    console.log('Added hidden input:', input.name, '=', input.value);

                    // Also send the price
                    var priceInput = document.createElement('input');
                    priceInput.type = 'hidden';
                    priceInput.name = 'test_prices[]';
                    var priceValue = checkboxes[i].getAttribute('data-price') || '0';
                    priceInput.value = priceValue;
                    container.appendChild(priceInput);
                    console.log('Added price input:', priceInput.name, '=', priceInput.value);

                    console.log('Added test:', checkboxes[i].value, 'with price:', priceValue);
                }
            }

            console.log('Selected tests:', selected);
            console.log('Hidden inputs created:', selected.length);
            console.log('Container children after update:', container.children.length);

            return selected;
        }

        // Add change event listener to each checkbox - update immediately
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].addEventListener('change', function() {
                console.log('Checkbox changed:', this.id, this.checked);
                updateTestCategory();
            });
        }

        // Handle form submission
        form.addEventListener('submit', function(e) {
            console.log('Form submission started');

            // Combine age fields before validation
            combineAgeFields();

            // Validate that at least one age field is filled
            var years = document.getElementById('age_years').value || '0';
            var months = document.getElementById('age_months').value || '0';
            var days = document.getElementById('age_days').value || '0';

            console.log('Age validation - Years:', years, 'Months:', months, 'Days:', days);

            if (years === '0' && months === '0' && days === '0') {
                console.log('Age validation failed');
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Age Required',
                        text: 'Please enter patient age (years, months, or days)',
                        confirmButtonColor: '#3085d6',
                    });
                } else {
                    alert('Please enter patient age');
                }
                return false;
            }

            // Ensure hidden fields are up to date
            console.log('Calling updateTestCategory before setTimeout');
            updateTestCategory();

            // Give DOM time to update and check actual hidden inputs
            setTimeout(function() {
                console.log('setTimeout callback executed');
                var hiddenInputs = document.querySelectorAll('input[name="test_category[]"]');
                console.log('Form submit - Hidden inputs found:', hiddenInputs.length);
                console.log('Container children:', document.getElementById('test-category-container').children.length);

                if (hiddenInputs.length === 0) {
                    console.log('No hidden inputs found - preventing form submission');
                    e.preventDefault(); // Stop submission

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Tests Selected',
                            text: 'Please select at least one test',
                            confirmButtonColor: '#3085d6',
                        });
                    } else {
                        alert('Please select at least one test');
                    }
                    return false;
                }

                // Otherwise, let the form submit normally
                console.log('Submitting form with', hiddenInputs.length, 'tests selected');
            }, 10);
        });

        // Initialize on page load
        console.log('Initializing on page load');
        updateTestCategory();
    });
})();
