<?php
require_once '../includes/config.php';

// Only logged-in customers can view orders
if (!isLoggedIn() || !isCustomer()) {
    redirect('../login.php');
}

$user_id = $_SESSION['user_id'];
$orders = $conn->query("SELECT o.*, COUNT(oi.id) as item_count FROM orders o LEFT JOIN order_items oi ON o.id = oi.order_id WHERE o.customer_id = $user_id GROUP BY o.id ORDER BY o.order_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo getTranslation('order_history'); ?> - AgroNexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2><?php echo getTranslation('order_history'); ?></h2>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if ($orders->num_rows == 0): ?>
            <div class="alert alert-info">No orders yet. <a href="products.php">Start shopping!</a></div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th><?php echo getTranslation('date'); ?></th>
                            <th><?php echo getTranslation('total'); ?></th>
                            <th><?php echo getTranslation('status'); ?></th>
                            <th><?php echo getTranslation('items'); ?></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></td>
                        <td>₹<?php echo $order['total_amount']; ?></td>
                        <td>
                            <span class="badge bg-<?php echo $order['status']=='pending'?'warning':($order['status']=='confirmed'?'success':($order['status']=='rejected'?'danger':'info')); ?>">
                                <?php echo getTranslation($order['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $order['item_count']; ?></td>
                        <td>
                            <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>