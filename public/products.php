 <?php
require_once '../includes/config.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

$user_location = '';
if (isCustomer()) {
    $uid = $_SESSION['user_id'];
    $loc = $conn->query("SELECT user_location FROM users WHERE id = $uid")->fetch_assoc();
    $user_location = $loc['user_location'] ?? '';
}

// Main product query
$sql = "SELECT p.*, u.name AS farmer_name, u.phone AS farmer_phone, fp.farm_name, fp.location 
        FROM products p 
        JOIN users u ON p.farmer_id = u.id 
        LEFT JOIN farmer_profiles fp ON u.id = fp.user_id 
        WHERE 1";

if ($search) {
    $sql .= " AND (p.name LIKE '%$search%' 
               OR p.tamil_name LIKE '%$search%' 
               OR u.name LIKE '%$search%' 
               OR fp.farm_name LIKE '%$search%'
               OR p.category LIKE '%$search%')";
}
if ($category) {
    $sql .= " AND p.category = '$category'";
}
if ($user_location) {
    $sql .= " AND fp.location LIKE '%$user_location%'";
}
$sql .= " ORDER BY p.created_at DESC";
$products = $conn->query($sql);

$user_name = '';
if (isLoggedIn()) {
    $uid = $_SESSION['user_id'];
    $user = $conn->query("SELECT name FROM users WHERE id = $uid")->fetch_assoc();
    $user_name = $user['name'] ?? '';
}

// Get all categories for filter buttons
$cats = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Products - AgroNexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <?php if (isCustomer() && !empty($user_name)): ?>
            <div class="alert alert-success text-center">
                <h4>Welcome back, <?php echo htmlspecialchars($user_name); ?>! 🌾</h4>
                <?php if ($user_location): ?>
                    <p>Showing products near <strong><?php echo htmlspecialchars($user_location); ?></strong></p>
                <?php endif; ?>
                <p>Browse categories below, or use the search bar.</p>
            </div>
        <?php endif; ?>

        <!-- Search Bar -->
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <form method="GET" action="products.php" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-lg" placeholder="Search for products, farmers, or location..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-success">🔍 Search</button>
                </form>
                <p class="text-muted mt-2 small text-center">Or click the microphone button at the bottom right to search by voice.</p>
            </div>
        </div>

        <!-- Category Filter Buttons -->
        <div class="mb-4 text-center">
            <div class="btn-group flex-wrap" role="group">
                <a href="products.php" class="btn btn-outline-success <?php echo empty($category) ? 'active' : ''; ?>">All</a>
                <?php while($cat = $cats->fetch_assoc()): ?>
                    <a href="products.php?category=<?php echo urlencode($cat['category']); ?>" class="btn btn-outline-success <?php echo $category == $cat['category'] ? 'active' : ''; ?>"><?php echo htmlspecialchars($cat['category']); ?></a>
                <?php endwhile; ?>
            </div>
        </div>

        <h1 class="text-center mb-5"><?php echo getTranslation('products'); ?></h1>

        <?php if ($search): ?>
            <div class="alert alert-info">Search results for: <strong><?php echo htmlspecialchars($search); ?></strong></div>
        <?php endif; ?>
        <?php if ($category): ?>
            <div class="alert alert-info">Category: <strong><?php echo htmlspecialchars($category); ?></strong></div>
        <?php endif; ?>

        <div class="row">
            <?php if ($products->num_rows == 0): ?>
                <div class="col-12 text-center">
                    <p>No products found. <?php if(isFarmer()) echo '<a href="../farmer/add-product.php">Add new products</a>'; ?></p>
                </div>
            <?php else: ?>
                <?php while ($p = $products->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 product-card" data-product='<?php echo json_encode($p); ?>' style="cursor:pointer;">
                        <?php if ($p['image_url']): ?>
                            <img src="../assets/images/products/<?php echo $p['image_url']; ?>" class="card-img-top" style="height:200px; object-fit:cover;">
                        <?php else: ?>
                            <div class="bg-light text-center py-5">🌾</div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $p['name']; ?></h5>
                            <p class="card-text">
                                <strong><?php echo getTranslation('farmer'); ?>:</strong> <?php echo $p['farm_name'] ?: $p['farmer_name']; ?><br>
                                <?php if ($p['location']): ?>
                                    <strong>📍 Location:</strong> <?php echo $p['location']; ?><br>
                                <?php endif; ?>
                                <strong>📞 Phone:</strong> <a href="tel:<?php echo $p['farmer_phone']; ?>"><?php echo $p['farmer_phone']; ?></a><br>
                                <strong>💰 Price:</strong> ₹<?php echo $p['price']; ?>/<?php echo $p['unit']; ?><br>
                                <strong>📦 Available:</strong> <?php echo $p['quantity_available']; ?> <?php echo $p['unit']; ?>
                            </p>
                            <?php if (isCustomer()): ?>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success add-to-cart" data-id="<?php echo $p['id']; ?>">🛒 Add to Cart</button>
                                    <button class="btn btn-outline-success view-details" data-id="<?php echo $p['id']; ?>">📋 View Details</button>
                                    <a href="https://wa.me/91<?php echo $p['farmer_phone']; ?>?text=Hi, I want to order <?php echo urlencode($p['name']); ?> (₹<?php echo $p['price']; ?>/<?php echo $p['unit']; ?>)" target="_blank" class="btn btn-outline-success">💬 Order via WhatsApp</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Popup for Product Details -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalTitle">Product Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    Loading...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="#" id="whatsappLink" class="btn btn-success" target="_blank">💬 Order via WhatsApp</a>
                </div>
            </div>
        </div>
    </div>

    <button id="voiceSearchBtn" class="btn btn-warning position-fixed bottom-0 end-0 m-4 rounded-pill" style="z-index: 1000;">🎤</button>
    <script src="../assets/js/voice.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add to Cart
        document.querySelectorAll('.add-to-cart').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                let productId = this.dataset.id;
                fetch('../customer/add-to-cart.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'product_id='+productId+'&quantity=1'
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) alert('Added to cart!');
                });
            });
        });

        // Modal popup
        const productModal = new bootstrap.Modal(document.getElementById('productModal'));
        document.querySelectorAll('.view-details').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const card = this.closest('.product-card');
                const product = JSON.parse(card.dataset.product);
                document.getElementById('modalTitle').innerText = product.name;
                document.getElementById('modalBody').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            ${product.image_url ? `<img src="../assets/images/products/${product.image_url}" class="img-fluid rounded">` : '<div class="bg-light text-center py-5">🌾</div>'}
                        </div>
                        <div class="col-md-6">
                            <p><strong>Farmer:</strong> ${product.farm_name || product.farmer_name}</p>
                            <p><strong>Location:</strong> ${product.location || 'Not specified'}</p>
                            <p><strong>Phone:</strong> <a href="tel:${product.farmer_phone}">${product.farmer_phone}</a></p>
                            <p><strong>Price:</strong> ₹${product.price}/${product.unit}</p>
                            <p><strong>Available:</strong> ${product.quantity_available} ${product.unit}</p>
                            <p><strong>Shelf Life:</strong> ${product.shelf_life || 'Not specified'}</p>
                            <p><strong>Nutritional Value:</strong> ${product.importance || 'Not available'}</p>
                            <p><strong>Description:</strong> ${product.description || 'No description'}</p>
                        </div>
                    </div>
                `;
                document.getElementById('whatsappLink').href = `https://wa.me/91${product.farmer_phone}?text=Hi, I want to order ${encodeURIComponent(product.name)} (₹${product.price}/${product.unit})`;
                productModal.show();
            });
        });
    </script>
</body>
</html>