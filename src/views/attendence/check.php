<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /elms/login");
    exit();
}

$title = "បញ្ជីវត្តមាន";
require_once 'src/common/head.php';
date_default_timezone_set('Asia/Phnom_Penh');
?>

<div class="page page-center">
    <div class="container-tight py-4">
        <div class="card animate__animated animate__slideInUpShort">
            <div class="empty">
                <div class="empty-img">
                    <img src="<?= $_SESSION['user_profile'] ?>" class="avatar avatar-md" style="object-fit: cover;"
                        alt="">
                </div>
                <p class="empty-title"><?= $_SESSION['user_khmer_name'] ?></p>
                <h1 class="empty-subtitle text-muted">
                    <?= date('Y-m-d | H:i A') ?>
                </h1>

                <!-- Location Name Display (Clickable link) -->
                <a href="#" target="_blank" id="locationName" class="mb-4 text-center">កំពុងពិនិត្យទីតាំង...</a>

                <div class="map" hidden style="height: 400px; width: 100%;"></div>

                <!-- Updated button logic in the form -->
                <div class="empty-action">
                    <form action="/elms/actionCheck" method="POST">
                        <div hidden>
                            <input type="text" id="latitude" name="latitude" value="">
                            <input type="text" id="longitude" name="longitude" value="">
                            <input type="text" name="userId" value="<?= $_SESSION['user_id'] ?? 'No User Id Found' ?>">
                            <input type="text" name="date" value="<?= date('Y-m-d') ?>">
                            <input type="text" name="check" value="<?= date('H:i:s') ?>">
                            <input type="text" id="deviceId" name="device_id" value="">
                            <input type="text" id="ipAddress" name="ip_address" value="">
                        </div>
                        <button type="submit" id="checkInButton" class="btn btn-primary w-100" disabled>
                            កំពុងពិនិត្យ...
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<?php require_once 'src/common/footer.php'; ?>

<!-- Include Leaflet.js -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

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
        // Inside the LocationPicker class
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

        // Set a cookie
        function setCookie(name, value, days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/;SameSite=Strict`;
        }

        // Get a cookie by name
        function getCookie(name) {
            const match = document.cookie.match(new RegExp(`(^| )${name}=([^;]+)`));
            return match ? match[2] : null;
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
        let deviceId = getCookie('deviceId');
        if (!deviceId) {
            deviceId = generateUUID();
            setCookie('deviceId', deviceId, 365); // Store UUID for 1 year
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
                    checkInButton.textContent = "ចុចទីនេះ";
                    checkInButton.disabled = false;
                } else {
                    checkInButton.textContent =
                        "សូមទៅពិនិត្យតាំងអោយបានត្រឹមត្រូវ! សូមអរគុណ។";
                    checkInButton.disabled = true;
                }
            } else {
                checkInButton.textContent = "Unable to get location. Please try again.";
                checkInButton.disabled = true;
            }
        }, 3000); // Delay for 3 seconds to allow geolocation to fetch coordinates
    };
</script>