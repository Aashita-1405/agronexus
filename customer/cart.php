 <?php
require_once '../includes/config.php';
if (!isLoggedIn() || !isCustomer()) redirect('../login.php');

$cart = $_SESSION['cart'] ?? [];
$total = 0;
$total_items = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
    $total_items += $item['quantity'];
}
$discount = 0;
if ($total_items > 10) {
    $discount = $total * 0.10;
}
$final_total = $total - $discount;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cart - AgroNexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2>Your Cart</h2>
        <?php if (empty($cart)): ?>
            <div class="alert alert-info">Your cart is empty. <a href="products.php">Start shopping!</a></div>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th>Action</th></tr>
                </thead>
                <tbody>
                <?php foreach ($cart as $index => $item): ?>
                    <tr>
                        <td><?php echo $item['name']; ?></td>
                        <td>₹<?php echo $item['price']; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>₹<?php echo $item['price'] * $item['quantity']; ?></td>
                        <td><a href="remove-from-cart.php?index=<?php echo $index; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove?')">Remove</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="card">
                <div class="card-body">
                    <p><strong>Total items:</strong> <?php echo $total_items; ?></p>
                    <p><strong>Subtotal:</strong> ₹<?php echo $total; ?></p>
                    <?php if ($discount > 0): ?>
                        <p class="text-success"><strong>🎉 10% Discount applied!</strong> - ₹<?php echo number_format($discount, 2); ?></p>
                    <?php endif; ?>
                    <h4>Total: ₹<?php echo number_format($final_total, 2); ?></h4>
                    <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>