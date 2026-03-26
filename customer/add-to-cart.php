 <?php
session_start();
require_once '../includes/config.php';
if (!isCustomer()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity'] ?: 1);
$prod = $conn->query("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();
if (!$prod) {
    echo json_encode(['error' => 'Product not found']);
    exit;
}
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['product_id'] == $product_id) {
        $item['quantity'] += $quantity;
        $found = true;
        break;
    }
}
if (!$found) {
    $_SESSION['cart'][] = [
        'product_id' => $product_id,
        'name' => $prod['name'],
        'price' => $prod['price'],
        'farmer_id' => $prod['farmer_id'],
        'quantity' => $quantity
    ];
}
echo json_encode(['success' => true, 'cart_count' => count($_SESSION['cart'])]);
?>