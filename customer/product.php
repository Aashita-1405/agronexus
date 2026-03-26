 <?php
require_once '../includes/config.php';
$id = intval($_GET['id']);
$prod = $conn->query("SELECT p.*, u.name AS farmer_name, u.phone AS farmer_phone, fp.farm_name, fp.location 
                       FROM products p 
                       JOIN users u ON p.farmer_id = u.id 
                       LEFT JOIN farmer_profiles fp ON u.id = fp.user_id 
                       WHERE p.id = $id")->fetch_assoc();
if (!$prod) die('Product not found');

// Other farmers selling same product
$others = $conn->query("SELECT p.*, u.name AS farmer_name, u.phone AS farmer_phone, fp.farm_name, fp.location 
                         FROM products p 
                         JOIN users u ON p.farmer_id = u.id 
                         LEFT JOIN farmer_profiles fp ON u.id = fp.user_id 
                         WHERE p.name = '{$prod['name']}' AND p.farmer_id != {$prod['farmer_id']}");
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $prod['name']; ?> - AgroNexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h2><?php echo $prod['name']; ?></h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <?php if ($prod['image_url']): ?>
                            <img src="../assets/images/products/<?php echo $prod['image_url']; ?>" class="img-fluid rounded" alt="<?php echo $prod['name']; ?>">
                        <?php else: ?>
                            <div class="bg-light text-center py-5">🌾 No Image</div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <p><strong><?php echo getTranslation('farmer'); ?>:</strong> <?php echo $prod['farm_name'] ?: $prod['farmer_name']; ?></p>
                        <p><strong>📍 Location:</strong> <?php echo $prod['location']; ?></p>
                        <p><strong>📞 Contact:</strong> <a href="tel:<?php echo $prod['farmer_phone']; ?>"><?php echo $prod['farmer_phone']; ?></a></p>
                        <p><strong>💰 <?php echo getTranslation('price'); ?>:</strong> ₹<?php echo $prod['price']; ?>/<?php echo $prod['unit']; ?></p>
                        <p><strong>📦 <?php echo getTranslation('available'); ?>:</strong> <?php echo $prod['quantity_available']; ?> <?php echo $prod['unit']; ?></p>
                        <p><strong>📝 <?php echo getTranslation('description'); ?>:</strong> <?php echo $prod['description']; ?></p>
                        <div class="alert alert-info">
                            <strong>💡 <?php echo getTranslation('importance'); ?> (Nutritional Value):</strong> 
                            <?php echo $prod['importance']; ?>
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <a href="https://wa.me/91<?php echo $prod['farmer_phone']; ?>?text=Hi, I want to order <?php echo urlencode($prod['name']); ?> (<?php echo $prod['price']; ?>/<?php echo $prod['unit']; ?>)" target="_blank" class="btn btn-success btn-lg">💬 Order via WhatsApp</a>
                        </div>
                    </div>
                </div>
                
                <?php if ($others->num_rows > 0): ?>
                    <hr>
                    <h4><?php echo getTranslation('other_farmers'); ?></h4>
                    <div class="row">
                        <?php while($o = $others->fetch_assoc()): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6><?php echo $o['farm_name'] ?: $o['farmer_name']; ?></h6>
                                        <p>📍 Location: <?php echo $o['location']; ?><br>
                                        📞 <a href="tel:<?php echo $o['farmer_phone']; ?>"><?php echo $o['farmer_phone']; ?></a><br>
                                        Price: ₹<?php echo $o['price']; ?>/<?php echo $o['unit']; ?><br>
                                        Available: <?php echo $o['quantity_available']; ?> <?php echo $o['unit']; ?></p>
                                        <a href="https://wa.me/91<?php echo $o['farmer_phone']; ?>?text=Hi, I want to order <?php echo urlencode($o['name']); ?> (<?php echo $o['price']; ?>/<?php echo $o['unit']; ?>)" target="_blank" class="btn btn-sm btn-success w-100">Order via WhatsApp</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>