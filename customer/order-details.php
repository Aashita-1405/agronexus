<?php
require_once '../includes/config.php';

if (!isLoggedIn() || !isCustomer()) {
    redirect('../login.php');
}

$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Fetch order
$order = $conn->query("SELECT o.*, u.name AS farmer_name FROM orders o JOIN users u ON o.farmer_id = u.id WHERE o.id = $order_id AND o.customer_id = $user_id")->fetch_assoc();
if (!$order) {
    redirect('orders.php');
}

// Fetch order items
$items = $conn->query("SELECT oi.*, p.name, p.unit FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order #<?php echo $order_id; ?> - AgroNexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2>Order #<?php echo $order_id; ?></h2>
        <div class="card mb-4">
            <div class="card-header">Order Details</div>
            <div class="card-body">
                <p><strong>Date:</strong> <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></p>
                <p><strong>Farmer:</strong> <?php echo $order['farmer_name']; ?></p>
                <p><strong>Total:</strong> ₹<?php echo $order['total_amount']; ?></p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-<?php echo $order['status']=='pending'?'warning':($order['status']=='confirmed'?'success':($order['status']=='rejected'?'danger':'info')); ?>">
                        <?php echo getTranslation($order['status']); ?>
                    </span>
                </p>
                <p><strong>Delivery Address:</strong> <?php echo $order['delivery_address']; ?></p>
                <p><strong>Payment Method:</strong> <?php echo $order['payment_method']; ?></p>
            </div>
        </div>
        <h4>Items</h4>
        <table class="table table-bordered">
            <thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th></tr></thead>
            <tbody>
            <?php while($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $item['name']; ?></td>
                    <td>₹<?php echo $item['price']; ?>/<?php echo $item['unit']; ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>₹<?php echo $item['price'] * $item['quantity']; ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <a href="orders.php" class="btn btn-secondary">Back to Orders</a>
    </div>
</body>
</html>