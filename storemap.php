<?php
include 'dbconnect.php';
include 'cart_count.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['userID'])) {
    header('Location: index.php');
    exit();
}

function getCoordinates($address) {
    $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($address);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, '2DMapApp/1.0 (your-email@example.com)');
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    if (isset($data[0])) {
        return [
            'lat' => $data[0]['lat'],
            'lon' => $data[0]['lon']
        ];
    } else {
        return null;
    }
}

// Updated query to fetch merchant details from the merchant_applications table
$select = "
    SELECT 
        ma.shop_street, ma.shop_barangay, ma.shop_municipality, 
        ma.shop_name, ma.application_id 
    FROM merchant_applications AS ma
    JOIN crafthub_users AS cu ON ma.user_id = cu.user_id
";

$result = $connection->query($select);
$locations = [];

while ($row = mysqli_fetch_assoc($result)) {
    $address = $row['shop_street'] . ', ' . $row['shop_barangay'] . ', ' . $row['shop_municipality'] . ', Bataan, Philippines';
    $coordinates = getCoordinates($address);
    if ($coordinates) {
        $locations[] = [
            'lat' => $coordinates['lat'],
            'lng' => $coordinates['lon'],
            'shopName' => $row['shop_name'],
            'description' => $address,
            'application_id' => $row['application_id']
        ];
    }
}

$locationsJson = json_encode($locations);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CraftHub: An Online Marketplace</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="css/storemap.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        const initialZoom = window.innerWidth < 768 ? 12 : 13;
const bounds = L.latLngBounds([14.4, 120.3], [15.0, 121.0]);

// Initialize map with bounds and without inertia/bounce for mobile stability
const map = L.map('map', {
    inertia: false,
    bounceAtZoomLimits: false,
}).setView([14.6417, 120.52260], initialZoom);

// Set tile layer
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
}).addTo(map);

// Icons for user and shops
const redIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});
const blueIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

// Dynamically adjust map height on mobile
function adjustMapHeight() {
    const mapContainer = document.getElementById("map");
    mapContainer.style.height = (window.innerHeight - document.body.scrollTop) + "px";
}
window.addEventListener("resize", adjustMapHeight);
adjustMapHeight(); // Initial call to set height

// Shop locations from PHP
const locations = <?php echo $locationsJson; ?>;
let userMarker, circle, nearestLine;

// Calculate distance between two points
function getDistance(lat1, lon1, lat2, lon2) {
    const R = 6371e3;
    const φ1 = lat1 * Math.PI / 180;
    const φ2 = lat2 * Math.PI / 180;
    const Δφ = (lat2 - lat1) * Math.PI / 180;
    const Δλ = (lon2 - lon1) * Math.PI / 180;

    const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

// Update shop popups with distance
function updatePopups(lat, lon) {
    locations.forEach(location => {
        const distance = (getDistance(lat, lon, location.lat, location.lng) / 1000).toFixed(2);
        location.popup = L.marker([location.lat, location.lng], { icon: redIcon })
            .addTo(map)
            .bindPopup(`
                <b class="shop-name">${location.shopName}</b><br>
                <span class="shop-description">${location.description}</span><br>
                <span class="shop-distance">Distance: ${distance} km</span><br>
                <a href="#" class="view-shop-link" onclick="viewShop(${location.application_id})">View Shop</a>
            `);
    });
}

// Geolocation tracking
navigator.geolocation.watchPosition(success, error);

function success(pos) {
    const lat = pos.coords.latitude;
    const lng = pos.coords.longitude;
    const accuracy = pos.coords.accuracy;

    if (userMarker) {
        map.removeLayer(userMarker);
        map.removeLayer(circle);
        if (nearestLine) {
            map.removeLayer(nearestLine);
        }
    }

    userMarker = L.marker([lat, lng], { icon: blueIcon }).addTo(map);
    circle = L.circle([lat, lng], { radius: accuracy }).addTo(map);
    map.setView([lat, lng]);

    updatePopups(lat, lng);

    const nearestShop = findNearestShop(lat, lng);
    if (nearestShop) {
        nearestLine = L.polyline([[lat, lng], [nearestShop.lat, nearestShop.lng]], { color: 'red' }).addTo(map);
        nearestShop.popup.openPopup();
    }
}

// Find nearest shop to user's location
function findNearestShop(lat, lon) {
    let nearestShop = null;
    let minDistance = Infinity;
    locations.forEach(location => {
        const distance = getDistance(lat, lon, location.lat, location.lng);
        if (distance < minDistance) {
            minDistance = distance;
            nearestShop = location;
        }
    });
    return nearestShop;
}

function error(err) {
    if (err.code === 1) {
        alert("Please allow geolocation access");
    } else {
        alert("Cannot get current location");
    }
}

// Set max bounds to limit map panning on mobile
map.setMaxBounds(bounds);
map.on('drag', function() {
    map.panInsideBounds(bounds, { animate: true });
});

// Redirect to shop page
function viewShop(application_id) {
    fetch('set_merchant_session.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `application_id=${application_id}`
    })
    .then(response => response.text())
    .then(data => {
        window.location.href = 'viewshop.php';
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

    </script>
    <script src="cart_count.js"></script>
</body>
</html>
