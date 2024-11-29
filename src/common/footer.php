</div>
</div>
</div>
</div>

<?php if (isset($_SESSION['user_id'])): ?>
    <?php if ($_SERVER['REQUEST_URI'] !== '/elms/usage'): ?>
        <footer class="sticky-bottom">
            <a href="/elms/usage" class="btn btn-primary mb-3 mx-3"
                data-bs-toggle="tooltip" data-bs-placement="top"
                data-bs-title="របៀបប្រើប្រាស់ប្រព័ន្ធសុំច្បាប់ឌីជីថល | ជំនាន់ ១.០">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle me-0 mx-0">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="16" x2="12" y2="12" />
                    <line x1="12" y1="8" x2="12.01" y2="8" />
                </svg>
                <span class="d-none d-md-inline ms-2">របៀបប្រើប្រាស់ប្រព័ន្ធសុំច្បាប់ឌីជីថល | ជំនាន់ ១.០</span>
            </a>
        </footer>
    <?php endif; ?>
<?php endif; ?>

<?php require_once 'modals.php'; ?>

<!-- Tabler Core -->
<script src="public/dist/js/tabler.min.js?1668287865" defer></script>
<script src="public/dist/js/demo.min.js?1668287865" defer></script>

<script src="public/dist/libs/apexcharts/dist/apexcharts.min.js?1668287865" defer></script>
<script src="public/dist/libs/jsvectormap/dist/js/jsvectormap.min.js?1668287865" defer></script>
<script src="public/dist/libs/jsvectormap/dist/maps/world.js?1668287865" defer></script>
<script src="public/dist/libs/jsvectormap/dist/maps/world-merc.js?1668287865" defer></script>
<script src="public/dist/libs/tom-select/dist/js/tom-select.base.js?1668287865" defer></script>

<script src="public/dist/libs/nouislider/dist/nouislider.min.js?1668287865" defer></script>
<script src="public/dist/libs/litepicker/dist/litepicker.js?1668287865" defer></script>
<script src="public/dist/libs/tom-select/dist/js/tom-select.base.min.js?1668287865" defer></script>
<!-- other link  -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- datatables  -->
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<!-- Script to hide the loader with delay -->
<script>
    window.addEventListener('load', function () {
        // Add a delay before removing the loader
        setTimeout(function () {
            // Get the loader wrapper element
            var loader = document.getElementById('loader-wrapper');

            // Remove the 'loading' class from the body to unblur the content
            document.body.classList.remove('loading');

            // Hide the loader wrapper after the delay
            loader.style.display = 'none';
        }, 500); // Delay for 0.5 second (500 milliseconds)
    });

    // Optionally, apply the 'loading' class when the page starts loading to blur the page content
    document.body.classList.add('loading');
</script>
<!-- end  -->

<script>
    document.addEventListener("DOMContentLoaded", function () {
        window.Litepicker && new Litepicker({
            element: document.getElementById("datepicker-inline"),
            buttonText: {
                previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <polyline points="15 6 9 12 15 18"/>
                </svg>`,
                nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <polyline points="9 6 15 12 9 18"/>
                </svg>`,
            },
            inlineMode: true,
            startDate: new Date(), // Set the active date to today
            autoApply: true,
            singleMode: true,
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Check if element exists before initializing TomSelect
        const leaveTypeElement = document.getElementById("leave_type");
        if (leaveTypeElement) {
            new TomSelect(leaveTypeElement, {
                copyClassesToDropdown: false,
                dropdownClass: "dropdown-menu ts-dropdown",
                optionClass: "dropdown-item",
                controlInput: "<input>",
                render: {
                    item: function (data, escape) {
                        return data.customProperties ?
                            `<div><span class="dropdown-item-indicator">${data.customProperties}</span>${escape(data.text)}</div>` :
                            `<div>${escape(data.text)}</div>`;
                    },
                    option: function (data, escape) {
                        return data.customProperties ?
                            `<div><span class="dropdown-item-indicator">${data.customProperties}</span>${escape(data.text)}</div>` :
                            `<div>${escape(data.text)}</div>`;
                    },
                },
            });

            leaveTypeElement.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const leaveTypeName = selectedOption.getAttribute('data-leave-name');
                document.getElementById('leave_type_name').value = leaveTypeName;
            });
        }

        // Check if elements with class 'date-picker' exist before initializing Litepicker
        const dateInputs = document.querySelectorAll('.date-picker');
        dateInputs.forEach(input => {
            new Litepicker({
                element: input,
                singleMode: true,
                format: "YYYY-MM-DD",
                lang: 'kh',
                buttonText: {
                    previousMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
                    nextMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
                }
            });
        });

        // Check if elements with class 'leave-picker' exist before initializing Litepicker
        const leaveDateInputs = document.querySelectorAll('.leave-picker');
        leaveDateInputs.forEach(input => {
            new Litepicker({
                element: input,
                singleMode: true,
                format: "YYYY-MM-DD",
                lang: 'kh',
                minDate: new Date(), // Disables past dates by setting the min date to today
                buttonText: {
                    previousMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
                    nextMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
                }
            });
        });


        // Initialize Flatpickr for elements with the 'time-picker' class
        const timeInputs = document.querySelectorAll(".time-picker");
        timeInputs.forEach(timeInput => {
            flatpickr(timeInput, {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: false,
                defaultHour: 12,
                defaultMinute: 0,
                locale: 'km',
                allowInput: true, // Allow users to type directly into the input field
                minuteIncrement: 1 // Set minute intervals to 1 (instead of 5)
            });
        });

        // Initial setup to ensure signature file input is visible if checkbox is checked
        const signatureCheckbox = document.getElementById('signature');
        if (signatureCheckbox && signatureCheckbox.checked) {
            const signatureFileInput = document.getElementById('signatureFile');
            if (signatureFileInput) {
                signatureFileInput.style.display = 'block';
            }
        }
    });

    function toggleFileInput(checkbox, fileInputId) {
        const fileInput = document.getElementById(fileInputId);
        if (fileInput) {
            fileInput.style.display = checkbox.checked ? 'block' : 'none';
        }
    }

    function displayFileName(inputId, labelId) {
        const input = document.getElementById(inputId);
        const fileNameLabel = document.getElementById(labelId);
        if (input && fileNameLabel) {
            fileNameLabel.textContent = input.files[0] ? input.files[0].name : '';
        }
    }
</script>

<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
        var el;
        window.TomSelect && (new TomSelect(el = document.getElementById('select-status'), {
            copyClassesToDropdown: false,
            dropdownClass: 'dropdown-menu ts-dropdown',
            optionClass: 'dropdown-item',
            controlInput: '<input>',
            render: {
                item: function (data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function (data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
            },
        }));
    });
    // @formatter:on
</script>

<!-- ts select  -->
<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
        var elements = document.querySelectorAll(".ts-select"); // Select all elements with the class ts-select
        elements.forEach(function (el) {
            window.TomSelect &&
                new TomSelect(el, {
                    copyClassesToDropdown: false,
                    dropdownClass: "dropdown-menu ts-dropdown",
                    optionClass: "dropdown-item",
                    controlInput: "<input>",
                    render: {
                        item: function (data, escape) {
                            if (data.customProperties) {
                                return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                            }
                            return "<div>" + escape(data.text) + "</div>";
                        },
                        option: function (data, escape) {
                            if (data.customProperties) {
                                return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                            }
                            return "<div>" + escape(data.text) + "</div>";
                        },
                    },
                });
        });
    });
    // @formatter:on
</script>

<!-- datatable sort  -->
<script>
    document.querySelectorAll('.table-sort').forEach(function (header) {
        header.addEventListener('click', function () {
            let table = header.closest('.sortable-table');
            let columnIndex = [...header.parentNode.children].indexOf(header);
            let ascending = !header.classList.contains('asc');
            sortTable(table, columnIndex, ascending);
            header.classList.toggle('asc', ascending);
        });
    });

    function sortTable(table, columnIndex, ascending) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
            const cellA = a.children[columnIndex].innerText.trim();
            const cellB = b.children[columnIndex].innerText.trim();
            return ascending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
        });
        rows.forEach(row => tbody.appendChild(row));
    }
</script>

<!-- for select people dropdown  -->
<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
        // Select all elements with the class 'select-people'
        const selectElements = document.querySelectorAll('.select-people');

        selectElements.forEach(el => {
            window.TomSelect && (new TomSelect(el, {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function (data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function (data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });
    });
    // @formatter:on
</script>

<!-- display attach file  -->
<script>
    document.querySelector('.file-input').addEventListener('change', function () {
        const fileList = this.files;
        const selectedFilesList = document.querySelector('.selected-files');
        selectedFilesList.innerHTML = ''; // Clear previous file names

        if (fileList.length > 0) {
            Array.from(fileList).forEach(file => {
                const listItem = document.createElement('li');
                listItem.textContent = file.name;
                selectedFilesList.appendChild(listItem);
            });
        } else {
            selectedFilesList.innerHTML = '<li>No files selected</li>';
        }
    });
</script>

<!-- camera scanner for attendance  -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const scanQrButton = document.getElementById("scanQrButton");
        const stopScanButton = document.getElementById("stopScanButton");
        const qrResult = document.getElementById("qrResult");
        const cameraWrapper = document.getElementById("cameraWrapper");
        const reader = document.getElementById("reader");

        let html5QrCode;

        const handleDecodedResult = (decodedText) => {
            qrResult.textContent = `ស្កេនបានជោគជ័យ! កំពុងដំណើរការបន្ត...`;
            if (isValidUrl(decodedText)) {
                setTimeout(() => {
                    window.location.href = decodedText; // Redirect to the URL in the QR code
                }, 1000);
            } else {
                qrResult.textContent = `Invalid QR Code Content: ${decodedText}`;
            }
        };

        const isValidUrl = (url) => {
            try {
                new URL(url);
                return true;
            } catch (_) {
                return false;
            }
        };

        // Open camera and start scanning
        scanQrButton.addEventListener("click", () => {
            qrResult.textContent = ""; // Clear previous result
            cameraWrapper.style.display = "flex";

            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }

            html5QrCode.start(
                { facingMode: "environment" }, // Rear camera
                { fps: 10, qrbox: 250 },
                (decodedText) => {
                    html5QrCode.stop();
                    cameraWrapper.style.display = "none";
                    handleDecodedResult(decodedText);
                },
                (error) => {
                    console.error("QR Code Scanning Error:", error);
                }
            ).catch((err) => {
                console.error("Error starting the camera:", err);
            });

            // Show the stop button when scanning starts
            stopScanButton.style.display = "block";
        });

        // Stop scanning and close camera
        stopScanButton.addEventListener("click", () => {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    cameraWrapper.style.display = "none";
                    stopScanButton.style.display = "none";
                }).catch((err) => {
                    console.error("Error stopping the camera:", err);
                });
            }
        });
    });
</script>

<!-- display signature  -->
<style>
    .custom-file-label {
        cursor: pointer;
        position: relative;
        display: inline-block;
    }

    .custom-file-label:hover {
        background-color: rgba(0, 0, 0, 0.1);
    }

    .visually-hidden {
        position: absolute;
        clip: rect(0, 0, 0, 0);
        height: 1px;
        width: 1px;
        overflow: hidden;
        white-space: nowrap;
    }

    .signature-list-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .signature-card {
        width: calc(33.333% - 10px);
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        text-align: center;
        background: #f9f9f9;
    }

    .signature-card img {
        width: 100%;
        height: 100px;
        object-fit: cover;
    }

    .signature-card p {
        margin: 0;
        padding: 5px;
        font-size: 14px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    .signature-card button {
        position: absolute;
        top: 5px;
        right: 5px;
        background: red;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 12px;
        cursor: pointer;
    }

    .signature-card button:hover {
        background: darkred;
    }
</style>

<script>
    document.addEventListener('change', function (event) {
        if (event.target.matches('.signature-input')) {
            const input = event.target;
            const container = input.closest('.row').querySelector('.signature-list-container');
            container.innerHTML = ''; // Clear existing items

            if (input.files && input.files.length > 0) {
                Array.from(input.files).forEach((file, index) => {
                    const card = document.createElement('div');
                    card.className = 'signature-card';

                    // Create an image preview
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = file.name;
                        card.appendChild(img);
                    };

                    // File name
                    const fileName = document.createElement('p');
                    fileName.textContent = file.name;
                    card.appendChild(fileName);

                    // Delete button
                    const deleteButton = document.createElement('button');
                    deleteButton.textContent = 'x';
                    deleteButton.setAttribute('data-index', index);
                    deleteButton.addEventListener('click', function () {
                        card.remove();
                        // Update file input to exclude removed files
                        const newFiles = Array.from(input.files).filter((_, i) => i !== index);
                        const dataTransfer = new DataTransfer();
                        newFiles.forEach(file => dataTransfer.items.add(file));
                        input.files = dataTransfer.files;
                    });
                    card.appendChild(deleteButton);

                    reader.readAsDataURL(file);
                    container.appendChild(card);
                });
            }
        }
    });
</script>
<!-- end display signature  -->

</body>

</html>