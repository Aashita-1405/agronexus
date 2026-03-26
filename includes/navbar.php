 <?php
require_once __DIR__ . '/language.php';

$cart_count = 0;
if (isCustomer() && isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) $cart_count += $item['quantity'];
}

// Get user name if logged in
$user_name = '';
if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $user = $conn->query("SELECT name FROM users WHERE id = $user_id")->fetch_assoc();
    $user_name = $user['name'] ?? '';
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand" href="../public/index.php">🌾 AgroNexus</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="../public/products.php"><?php echo getTranslation('products'); ?></a></li>
                <li class="nav-item"><a class="nav-link" href="../public/markets.php"><?php echo getTranslation('markets'); ?></a></li>

                <?php if (isLoggedIn()): ?>
                    <?php if (isFarmer()): ?>
                        <li class="nav-item"><a class="nav-link" href="../farmer/dashboard.php"><?php echo getTranslation('dashboard'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="../farmer/products.php"><?php echo getTranslation('my_products'); ?></a></li>
                    <?php elseif (isCustomer()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../customer/cart.php">
                                🛒 <?php echo getTranslation('cart'); ?>
                                <?php if ($cart_count > 0): ?>
                                    <span class="badge bg-warning text-dark ms-1"><?php echo $cart_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="../customer/orders.php">📦 <?php echo getTranslation('orders'); ?></a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="../logout.php"><?php echo getTranslation('logout'); ?></a></li>
                    <li class="nav-item"><span class="nav-link text-warning">👋 <?php echo htmlspecialchars($user_name); ?></span></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="../login.php"><?php echo getTranslation('login'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="../register.php"><?php echo getTranslation('register'); ?></a></li>
                <?php endif; ?>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="langDropdown" role="button" data-bs-toggle="dropdown">
                        <?php echo strtoupper($lang_code); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?lang=en">🇬🇧 English</a></li>
                        <li><a class="dropdown-item" href="?lang=ta">🇮🇳 தமிழ்</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>