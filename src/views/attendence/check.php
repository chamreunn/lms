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
        <div class="empty">
            <div class="empty-img">
                <img src="<?= $_SESSION['user_profile'] ?>" class="avatar avatar-md" height="128" alt="">
            </div>
            <p class="empty-title"><?= $_SESSION['user_khmer_name'] ?></p>
            <h1 class="empty-subtitle text-muted">
                <?= date('Y-m-d | H:i A') ?>
            </h1>

            <!-- Location Name Display (Clickable link) -->
            <a href="#" target="_blank" id="locationName" class="h4 mb-4 text-center">Loading location...</a>

            <div class="map" hidden style="height: 400px; width: 100%;"></div>
            <div class="empty-action">
                <form action="/elms/actionCheck" method="POST">
                    <div>
                        <input type="text" id="latitude" name="latitude" value="">
                        <input type="text" id="longitude" name="longitude" value="">
                        <input type="text" name="userId" value="<?= $_SESSION['user_id'] ?? 'No User Id Found' ?>">
                        <input type="text" name="date" value="<?= date('Y-m-d') ?>">
                        <input type="text" name="check" value="<?= date('H:i') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        Check
                    </button>
                </form>
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
                        this.locationNameElement.textContent = `Current Location: ${locationName}`;

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

    // Instantiate the LocationPicker class for the map
    window.onload = () => {
        const locationPicker = new LocationPicker('map', 'latitude', 'longitude', 100);
    };
</script>