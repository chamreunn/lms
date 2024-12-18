</div>
</div>
</div>
</div>

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

<!-- Include Leaflet.js -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<!-- AOS JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: true,
    });
</script>

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

        // Check if elements with class 'report-date-picker' exist before initializing Litepicker
        const reportDateInputs = document.querySelectorAll('.report-date-picker');
        reportDateInputs.forEach(input => {
            new Litepicker({
                element: input,
                singleMode: true,
                format: "YYYY-MM-DD",
                lang: 'kh',
                maxDate : new Date(),
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

<!-- download qr code  -->
<script>
    document.querySelectorAll('.downloadPoster').forEach(button => {
        button.addEventListener('click', async function () {
            const { jsPDF } = window.jspdf;
            const posterElement = this.closest('.card').querySelector('.poster');

            // Temporarily set fixed dimensions for PDF generation
            const originalStyles = {
                width: posterElement.style.width,
                height: posterElement.style.height
            };
            posterElement.style.width = '148mm';
            posterElement.style.height = '210mm';

            // Generate PDF from the element
            html2canvas(posterElement, {
                scale: 3, // Higher scale for HD quality
                useCORS: true // Enables cross-origin resource sharing if needed
            }).then(canvas => {
                // Revert to original styles after capturing the canvas
                posterElement.style.width = originalStyles.width;
                posterElement.style.height = originalStyles.height;

                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a5' // A5 size: 148mm x 210mm
                });

                pdf.addImage(imgData, 'PNG', 0, 0, 148, 210); // Fit image to A5 size
                pdf.save('QR-Code-Attendance-Scan.pdf');
            }).catch(error => {
                console.error("Error generating PDF: ", error);
            });
        });
    });
</script>

<!-- admin download qr code for user  -->
<script>
    // Attach the download functionality to each button
    document.querySelectorAll('.downloadPosterQR').forEach(button => {
        button.addEventListener('click', async function () {
            const { jsPDF } = window.jspdf;

            // Locate the closest modal's poster element
            const modal = this.closest('.modal');
            const posterElement = modal.querySelector('.poster');

            if (!posterElement) {
                console.error("Poster element not found for this modal");
                return;
            }

            try {
                // Save original styles and adjust dimensions for rendering
                const originalStyles = {
                    width: posterElement.style.width,
                    height: posterElement.style.height
                };

                posterElement.style.width = '148mm'; // Set width to A5 dimensions
                posterElement.style.height = '210mm'; // Set height to A5 dimensions

                // Capture poster as a canvas using html2canvas
                const canvas = await html2canvas(posterElement, {
                    scale: 3, // Enhance resolution
                    useCORS: true, // Ensure CORS compliance for external resources
                });

                // Restore original styles
                posterElement.style.width = originalStyles.width;
                posterElement.style.height = originalStyles.height;

                // Convert canvas to image and generate a PDF
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a5', // A5 dimensions
                });

                pdf.addImage(imgData, 'PNG', 0, 0, 148, 210); // Add image to fit A5 size
                const userName = posterElement.querySelector('h1.text-primary')?.textContent || 'QR-Code';
                pdf.save(`${userName}-QR-Code.pdf`); // Save the file as user-specific QR code
            } catch (error) {
                console.error("Error generating PDF: ", error);
            }
        });
    });
</script>

<script>
    class LocationPicker {
        constructor(mapClass, latFieldId, lngFieldId, maxDistance) {
            this.mapClass = mapClass;
            this.latFieldId = latFieldId;
            this.lngFieldId = lngFieldId;
            this.maxDistance = maxDistance;
            this.map = null;
            this.marker = null;
            this.userLocation = null;
            this.locationNameElement = document.getElementById('locationName'); // Element to display location name
            this.locationNameLink = document.getElementById('locationName'); // The link element for location

            // Initialize the map
            this.initMap();
        }

        // Initialize map
        initMap() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.userLocation = [
                            position.coords.latitude,
                            position.coords.longitude
                        ];

                        // Update the latitude and longitude in the hidden input fields
                        document.getElementById(this.latFieldId).value = this.userLocation[0];
                        document.getElementById(this.lngFieldId).value = this.userLocation[1];

                        this.getLocationName(this.userLocation[0], this.userLocation[1]);
                        this.createMap();
                    },
                    (error) => {
                        console.error("Geolocation Error: ", error);
                        alert("Unable to retrieve your location.");
                    }
                );
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        // Update fields when the marker is dragged or map is clicked
        updateLatLngFields(e) {
            const position = e.target.getLatLng();
            document.getElementById(this.latFieldId).value = position.lat;
            document.getElementById(this.lngFieldId).value = position.lng;
        }

        // Get location name using reverse geocoding
        getLocationName(lat, lng) {
            const apiUrl = `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&addressdetails=1`;

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    if (data && data.address) {
                        const address = data.address;
                        let locationName = '';

                        if (address.city) {
                            locationName = address.city;
                        } else if (address.town) {
                            locationName = address.town;
                        } else if (address.village) {
                            locationName = address.village;
                        } else {
                            locationName = 'Unknown Location';
                        }

                        // Update the location name display
                        this.locationNameElement.textContent = `ទីតាំងបច្ចុប្បន្ន: ${locationName}`;

                        // Create Google Maps URL dynamically
                        const googleMapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;

                        // Make the location name clickable to open Google Maps
                        this.locationNameLink.href = googleMapsUrl;
                    } else {
                        this.locationNameElement.textContent = 'Unable to determine location name.';
                        this.locationNameLink.href = '#'; // No valid location, prevent the link
                    }
                })
                .catch(error => {
                    console.error('Geocoding error:', error);
                    this.locationNameElement.textContent = 'Error fetching location name.';
                    this.locationNameLink.href = '#'; // No valid location, prevent the link
                });
        }

        // Create and set up the map
        createMap() {
            const mapContainer = document.querySelector(`.${this.mapClass}`);
            if (mapContainer) {
                // Create the map centered on the user's location
                this.map = L.map(mapContainer).setView(this.userLocation, 16);

                // Add standard tile layer (OpenStreetMap)
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(this.map);

                // Create a circle representing the 100 meter range
                L.circle(this.userLocation, {
                    color: 'blue',
                    fillColor: '#30a7d7',
                    fillOpacity: 0.3,
                    radius: this.maxDistance
                }).addTo(this.map);

                // Add a draggable marker at the user's location
                this.marker = L.marker(this.userLocation, { draggable: true }).addTo(this.map);

                // Restrict the marker to stay within the 100m range
                this.marker.on('drag', (e) => this.onMarkerDrag(e));

                // Update hidden input fields when marker is dragged
                this.marker.on('dragend', (e) => this.updateLatLngFields(e));

                // Handle map clicks to place the marker within 100m range
                this.map.on('click', (e) => this.onMapClick(e));
            } else {
                alert("Map container not found.");
            }
        }

        // Handle dragging the marker
        onMarkerDrag(e) {
            const markerLatLng = e.target.getLatLng();
            const distance = this.map.distance(markerLatLng, this.userLocation);

            if (distance > this.maxDistance) {
                const latLng = this.userLocation; // Reset to original position if out of range
                this.marker.setLatLng(latLng);
            }
        }

        // Handle map clicks to place the marker within 100m range
        onMapClick(e) {
            const { lat, lng } = e.latlng;
            const distance = this.map.distance(e.latlng, this.userLocation);

            if (distance <= this.maxDistance) {
                this.marker.setLatLng([lat, lng]);
                this.updateLatLngFields({ target: this.marker });
            } else {
                alert("Please select a point within 100 meters.");
            }
        }

        // Update hidden fields with the new latitude and longitude
        updateLatLngFields(e) {
            const position = e.target.getLatLng();
            document.getElementById(this.latFieldId).value = position.lat;
            document.getElementById(this.lngFieldId).value = position.lng;
        }
    }

    window.onload = async () => {
        const defaultLocation = [11.632825042495787, 104.88334294171813];
        const maxDistance = 100; // in meters
        const checkInButton = document.getElementById('checkInButton');

        // Function to calculate distance between two coordinates (Haversine formula)
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371e3; // Radius of the Earth in meters
            const toRadians = (degrees) => degrees * (Math.PI / 180);
            const dLat = toRadians(lat2 - lat1);
            const dLon = toRadians(lon2 - lon1);

            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(toRadians(lat1)) *
                Math.cos(toRadians(lat2)) *
                Math.sin(dLon / 2) *
                Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c; // Distance in meters
        }

        const locationPicker = new LocationPicker('map', 'latitude', 'longitude', maxDistance);

        // Generate a unique UUID for the device
        function generateUUID() {
            return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
                (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
            );
        }

        // Get public IP using an external API
        async function getPublicIP() {
            try {
                const response = await fetch('https://api64.ipify.org?format=json');
                const data = await response.json();
                return data.ip;
            } catch (error) {
                console.error('Unable to fetch IP address:', error);
                return 'Unknown';
            }
        }

        // UUID Management
        let deviceId = localStorage.getItem('deviceId');
        if (!deviceId) {
            deviceId = generateUUID();
            localStorage.setItem('deviceId', deviceId); // Store UUID indefinitely
        }

        // Set UUID and IP in hidden form fields
        document.getElementById('deviceId').value = deviceId;

        const ipAddress = await getPublicIP();
        document.getElementById('ipAddress').value = ipAddress;

        // Check location and update button behavior
        setTimeout(() => {
            const latitude = parseFloat(document.getElementById('latitude').value);
            const longitude = parseFloat(document.getElementById('longitude').value);

            if (!isNaN(latitude) && !isNaN(longitude)) {
                const distance = calculateDistance(
                    defaultLocation[0],
                    defaultLocation[1],
                    latitude,
                    longitude
                );

                if (distance <= maxDistance) {
                    checkInButton.textContent = "កត់ត្រាវត្តមាន";
                    checkInButton.disabled = false;
                } else {
                    checkInButton.textContent =
                        "សូមអភ័យទោសអ្នកមិនស្ថិតនៅទីតាំងដែលអាចកត់ត្រាវត្តមានបានទេ ។";
                    checkInButton.disabled = true;
                }
            } else {
                checkInButton.textContent = "Unable to get location. Please try again.";
                checkInButton.disabled = true;
            }
        }, 3000); // Delay for 3 seconds to allow geolocation to fetch coordinates
    };
</script>

<!-- text area auto height  -->
<script>
    document.addEventListener('input', function (event) {
        if (event.target.tagName === 'TEXTAREA') {
            event.target.style.height = 'auto';
            event.target.style.height = (event.target.scrollHeight) + 'px';
        }
    });
</script>

</body>

</html>