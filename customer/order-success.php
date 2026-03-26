 <?php
require_once '../includes/config.php';
if (!isLoggedIn() || !isCustomer()) redirect('../login.php');

if (!isset($_SESSION['last_order'])) {
    redirect('orders.php');
}

$order = $_SESSION['last_order'];
unset($_SESSION['last_order']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation - AgroNexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header bg-success text-white text-center">
                <h2>🎉 Order Confirmed!</h2>
            </div>
            <div class="card-body">
                <p class="lead text-center">Thank you for your purchase. Your order has been placed successfully.</p>
                <hr>
                <h4>Order Summary</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                        <tr>
                            <td><?php echo $item['name']; ?></td>
                            <td>₹<?php echo $item['price']; ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>₹<?php echo $item['price'] * $item['quantity']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="text-end">
                    <p><strong>Subtotal:</strong> ₹<?php echo number_format($order['total'], 2); ?></p>
                    <?php if ($order['discount'] > 0): ?>
                        <p class="text-success"><strong>🎉 Bulk Discount (10%):</strong> -₹<?php echo number_format($order['discount'], 2); ?></p>
                    <?php endif; ?>
                    <h3>Total to Pay on Delivery: ₹<?php echo number_format($order['final_total'], 2); ?></h3>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['address']); ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?>
                    </div>
                </div>
                <hr>
                <div class="text-center mt-4">
                    <a href="orders.php" class="btn btn-success">View All Orders</a>
                    <a href="products.php" class="btn btn-outline-success">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>