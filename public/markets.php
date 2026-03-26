 <?php
require_once '../includes/config.php';
$markets = $conn->query("SELECT * FROM markets");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nearby Markets - AgroNexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map { height: 500px; width: 100%; border-radius: 15px; margin-bottom: 30px; }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-5">
        <h2 class="text-center mb-5">Farmers Markets Near You</h2>
        
        <div id="map"></div>
        
        <div class="row">
            <?php while($market = $markets->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $market['name']; ?></h5>
                        <p class="card-text">
                            <strong>Address:</strong> <?php echo $market['address']; ?><br>
                            <strong>Days:</strong> <?php echo $market['operating_days']; ?><br>
                            <?php if ($market['description']): ?>
                                <em><?php echo $market['description']; ?></em>
                            <?php endif; ?>
                        </p>
                        <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($market['address']); ?>" target="_blank" class="btn btn-outline-success">View on Google Maps</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        // Initialize the map
        var map = L.map('map').setView([20.5937, 78.9629], 5); // Center of India
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Add markers from database
        <?php 
        $markets->data_seek(0);
        while($market = $markets->fetch_assoc()): 
            $lat = $market['latitude'];
            $lng = $market['longitude'];
            if ($lat && $lng):
        ?>
            var marker = L.marker([<?php echo $lat; ?>, <?php echo $lng; ?>]).addTo(map);
            marker.bindPopup("<b><?php echo addslashes($market['name']); ?></b><br><?php echo addslashes($market['address']); ?>");
        <?php 
            endif;
        endwhile; 
        ?>
        
        // Optionally, if no markers, show a default message
        <?php if ($markets->num_rows == 0): ?>
            map.setView([20.5937, 78.9629], 5);
        <?php endif; ?>
    </script>
</body>
</html>