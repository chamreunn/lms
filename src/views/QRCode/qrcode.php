<?php
$pretitle = "ទំព័រដើម";
$title = "QR Code";
include('src/common/header.php');
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<!-- <div class="container-xl mb-3">
    <a href="/elms/attendanceCheck" type="button" class="btn btn-primary d-none d-sm-inline-block">
        <span class="mx-2">check in</span>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="icon icon-tabler icons-tabler-outline icon-tabler-qrcode">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
            <path d="M7 17l0 .01" />
            <path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
            <path d="M7 7l0 .01" />
            <path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
            <path d="M17 7l0 .01" />
            <path d="M14 14l3 0" />
            <path d="M20 14l0 .01" />
            <path d="M14 14l0 3" />
            <path d="M14 20l3 0" />
            <path d="M17 17l3 0" />
            <path d="M20 17l0 3" />
        </svg>
    </a>
</div> -->

<?php if (empty($qrCodeFound)): ?>
    <div class="page page-center">
        <div class="container py-4">
            <div class="card card-md animate__animated animate__slideInUpShort">
                <div class="empty">
                    <div class="text-center">
                        <img src="public/img/icons/svgs/qrcode.svg" class="w-50 mb-3" alt="">
                        <h3 class="mb-0">សូមចុចប៊ូតុង <span class="text-danger">បង្កើត QR Code</span> ខាងក្រោមដើម្បីទាញយក
                            <span class="text-primary">QR Code</span> សម្រាប់ស្កេនវត្តមានប្រចាំថ្ងៃរបស់អ្នក។
                        </h3>
                        <form action="/elms/generateQR" method="post" enctype="multipart/form-data">
                            <div class="modal-body" hidden>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label" for="name">ឈ្មោះ QR Code</label>
                                        <input type="text" class="form-control" id="name" name="name" autocomplete="off"
                                            value="ប្រព័ន្ធស្នើសុំច្បាប់ឌីជីថល" required>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label" for="size">ទំហំ</label>
                                        <input type="number" class="form-control" id="size" name="size" value="400"
                                            required>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Select Location on Map:</label>
                                        <div class="map rounded" style="height: 400px; width: 100%;"></div>

                                        <label class="form-label" for="name">QR Code URLs:</label>
                                        <input type="text" class="form-control" id="name" name="url" autocomplete="off"
                                            value="<?= $link ?? 'link not found' ?>" required>

                                        <!-- Hidden fields to store selected latitude and longitude -->
                                        <input type="text" id="latitude" class="latitude" name="latitude">
                                        <input type="text" id="longitude" class="longitude" name="longitude">
                                        <input type="hidden" name="userId" value="<?= $_SESSION['user_id'] ?>">
                                    </div>

                                    <div class="col-12">
                                        <label for="logo" class="form-label">រូបភាពឡូហ្គោ</label>
                                        <div class="text-center mb-3">
                                            <img src="public/img/icons/brands/logo2.png" alt="Default Logo"
                                                class="img-fluid rounded" style="max-width: 100px;">
                                        </div>
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*"
                                            onchange="this.nextElementSibling.src = window.URL.createObjectURL(this.files[0])">
                                    </div>

                                    <!-- Hidden field to store the device ID -->
                                    <input type="text" id="device_id" name="device_id" value="">
                                </div>
                            </div>
                            <div class="justify-content-center mt-3">
                                <button class="btn btn-primary" type="submit">បង្កើត QR Code</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Leaflet.js -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        class LocationPicker {
            constructor(mapClass, latFieldId, lngFieldId, defaultLocation, maxDistanceMeters) {
                this.mapClass = mapClass; // Map container class
                this.latFieldId = latFieldId; // Latitude input field id
                this.lngFieldId = lngFieldId; // Longitude input field id
                this.defaultLocation = defaultLocation; // Default location (latitude, longitude)
                this.maxDistanceMeters = maxDistanceMeters; // Maximum allowed distance in meters
                this.map = null;
                this.marker = null;

                // Initialize the map
                this.initMap();
            }

            // Initialize map
            initMap() {
                this.createMap();
                // Set initial values in input fields
                document.getElementById(this.latFieldId).value = this.defaultLocation[0];
                document.getElementById(this.lngFieldId).value = this.defaultLocation[1];
            }

            // Create and set up the map
            createMap() {
                const mapContainer = document.querySelector(`.${this.mapClass}`);
                if (mapContainer) {
                    this.map = L.map(mapContainer).setView(this.defaultLocation, 16); // Set zoom level

                    // Add standard tile layer (OpenStreetMap)
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(this.map);

                    // Add a draggable marker at the default location
                    this.marker = L.marker(this.defaultLocation, { draggable: true }).addTo(this.map);

                    // Update hidden input fields when marker is dragged
                    this.marker.on('dragend', (e) => this.handleMarkerDrag(e));

                    // Handle map clicks to place marker
                    this.map.on('click', (e) => this.onMapClick(e));
                } else {
                    alert("Map container not found.");
                }
            }

            // Handle map clicks to place the marker
            onMapClick(e) {
                const { lat, lng } = e.latlng;
                if (this.isWithinAllowedDistance(lat, lng)) {
                    this.marker.setLatLng([lat, lng]);
                    this.updateLatLngFields({ target: this.marker });
                } else {
                    alert(`Selected location is outside the allowed 100-meter radius.`);
                }
            }

            // Handle marker drag and check distance
            handleMarkerDrag(e) {
                const position = e.target.getLatLng();
                if (this.isWithinAllowedDistance(position.lat, position.lng)) {
                    this.updateLatLngFields(e);
                } else {
                    alert(`Selected location is outside the allowed 100-meter radius. Reverting to previous position.`);
                    // Reset marker to the default location
                    this.marker.setLatLng(this.defaultLocation);
                    this.updateLatLngFields({ target: this.marker });
                }
            }

            // Update hidden fields with the new latitude and longitude
            updateLatLngFields(e) {
                const position = e.target.getLatLng();
                document.getElementById(this.latFieldId).value = position.lat;
                document.getElementById(this.lngFieldId).value = position.lng;
            }

            // Check if the selected location is within the allowed distance
            isWithinAllowedDistance(lat, lng) {
                const R = 6371000; // Radius of Earth in meters
                const toRad = (value) => (value * Math.PI) / 180;

                const lat1 = this.defaultLocation[0];
                const lng1 = this.defaultLocation[1];
                const lat2 = lat;
                const lng2 = lng;

                const dLat = toRad(lat2 - lat1);
                const dLng = toRad(lng2 - lng1);
                const a =
                    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(toRad(lat1)) *
                    Math.cos(toRad(lat2)) *
                    Math.sin(dLng / 2) *
                    Math.sin(dLng / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

                const distance = R * c; // Distance in meters
                return distance <= this.maxDistanceMeters;
            }
        }

        // Instantiate the LocationPicker class for the map
        window.onload = () => {
            const defaultLocation = [11.632825042495787, 104.88334294171813]; // Replace with your office coordinates (latitude, longitude)
            const maxDistanceMeters = 100; // 100 meters
            const locationPicker = new LocationPicker('map', 'latitude', 'longitude', defaultLocation, maxDistanceMeters);

            // Handle device ID
            function generateUUID() {
                return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
                    (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
                );
            }

            function setCookie(name, value, days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000)); // Days to expire
                document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/;SameSite=Strict`;
            }

            function getCookie(name) {
                const match = document.cookie.match(new RegExp(`(^| )${name}=([^;]+)`));
                return match ? match[2] : null;
            }

            let deviceId = getCookie("deviceId");
            if (!deviceId) {
                deviceId = generateUUID();
                setCookie("deviceId", deviceId, 365); // 1 year
            }

            const deviceIdField = document.getElementById("device_id");
            deviceIdField.value = deviceId;
        };
    </script>
<?php else: ?>
    <div class="page page-center">
        <div class="container-tight py-3">
            <div class="card animate__animated animate__slideInUpShort p-0">
                <div id="poster" class="card">
                    <div class="card-status-top bg-primary h-10"></div>
                    <div class="card-body">
                        <div class="card-stamp">
                            <div class="card-stamp-icon bg-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-qrcode">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                    <path d="M7 17l0 .01" />
                                    <path
                                        d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                    <path d="M7 7l0 .01" />
                                    <path
                                        d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                    <path d="M17 7l0 .01" />
                                    <path d="M14 14l3 0" />
                                    <path d="M20 14l0 .01" />
                                    <path d="M14 14l0 3" />
                                    <path d="M14 20l3 0" />
                                    <path d="M17 17l3 0" />
                                    <path d="M20 17l0 3" />
                                </svg>
                            </div>
                        </div>
                        <div class="text-center">
                            <img src="public/img/icons/brands/logo3.png" width="200" alt="">
                        </div>
                        <div class="empty">
                            <div class="mb-3">
                                <p class="text-primary fw-bolder">សម្រាប់ស្កេនវត្តមានប្រចាំថ្ងៃរបស់អ្នក</p>
                            </div>
                            <div class="mb-3 container">
                                <img src="<?= $qrCodeBase64s; ?>" alt="QR Code" class="rounded shadow-sm mb-3">
                            </div>
                            <h3 class="text-primary"><?= $_SESSION['user_khmer_name'] ?></h3>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="w-100 text-center">
                        <div class="row g-3">
                            <div class="col">
                                <button id="downloadPoster" class="btn btn-outline-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                        <path d="M7 11l5 5l5 -5" />
                                        <path d="M12 4l0 12" />
                                    </svg>
                                    <span>ទាញយក</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    document.getElementById('downloadPoster').addEventListener('click', async function () {
        const { jsPDF } = window.jspdf;
        const posterElement = document.getElementById('poster');

        html2canvas(posterElement, {
            scale: 3, // Increase the scale for HD quality
            useCORS: true // Enables cross-origin resource sharing if needed
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a5'
            });

            pdf.addImage(imgData, 'PNG', 0, 0, 148, 210); // Add image to fit A5
            pdf.save('QR_Code_Poster_HD.pdf');
        });
    });
</script>

<?php include('src/common/footer.php'); ?>