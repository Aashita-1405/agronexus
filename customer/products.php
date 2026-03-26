 <?php
require_once '../includes/config.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sql = "SELECT p.*, u.name AS farmer_name, u.phone AS farmer_phone, fp.farm_name 
        FROM products p 
        JOIN users u ON p.farmer_id = u.id 
        LEFT JOIN farmer_profiles fp ON u.id = fp.user_id";
if ($search) {
    $sql .= " WHERE p.name LIKE '%$search%' 
              OR u.name LIKE '%$search%' 
              OR fp.farm_name LIKE '%$search%'";
}
$sql .= " ORDER BY p.created_at DESC";
$products = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo getTranslation('products'); ?> - AgroNexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h1 class="text-center mb-5"><?php echo getTranslation('products'); ?></h1>

        <?php if ($search): ?>
            <div class="alert alert-info">Search results for: <strong><?php echo htmlspecialchars($search); ?></strong></div>
        <?php endif; ?>

        <div class="row">
            <?php if ($products->num_rows == 0): ?>
                <div class="col-12 text-center">
                    <p>No products found. <?php if(isFarmer()) echo '<a href="../farmer/add-product.php">Add new products</a>'; ?></p>
                </div>
            <?php else: ?>
                <?php while ($p = $products->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if ($p['image_url']): ?>
                            <img src="../assets/images/products/<?php echo $p['image_url']; ?>" class="card-img-top" style="height:200px; object-fit:cover;">
                        <?php else: ?>
                            <div class="bg-light text-center py-5">🌾</div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $p['name']; ?></h5>
                            <p class="card-text">
                                <strong><?php echo getTranslation('farmer'); ?>:</strong> <?php echo $p['farm_name'] ?: $p['farmer_name']; ?><br>
                                <?php if (isLoggedIn() && $p['farmer_phone']): ?>
                                    <strong>📞 Contact:</strong> <a href="tel:<?php echo $p['farmer_phone']; ?>"><?php echo $p['farmer_phone']; ?></a><br>
                                <?php endif; ?>
                                <strong><?php echo getTranslation('price'); ?>:</strong> ₹<?php echo $p['price']; ?>/<?php echo $p['unit']; ?><br>
                                <strong><?php echo getTranslation('available'); ?>:</strong> <?php echo $p['quantity_available']; ?> <?php echo $p['unit']; ?>
                            </p>
                            <?php if (isCustomer()): ?>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-success add-to-cart" data-id="<?php echo $p['id']; ?>"><?php echo getTranslation('add_to_cart'); ?></button>
                                    <a href="https://wa.me/91<?php echo $p['farmer_phone']; ?>?text=Hi, I want to order <?php echo urlencode($p['name']); ?> (<?php echo $p['price']; ?>/<?php echo $p['unit']; ?>)" target="_blank" class="btn btn-outline-success">💬 Order via WhatsApp</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>

    <button id="voiceSearchBtn" class="btn btn-warning position-fixed bottom-0 end-0 m-4 rounded-pill" style="z-index: 1000;">🎤</button>
    <script src="../assets/js/voice.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.add-to-cart').forEach(btn => {
            btn.addEventListener('click', function() {
                let productId = this.dataset.id;
                fetch('../customer/add-to-cart.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'product_id='+productId+'&quantity=1'
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) alert('<?php echo getTranslation('added_to_cart'); ?>');
                });
            });
        });
    </script>
</body>
</html>