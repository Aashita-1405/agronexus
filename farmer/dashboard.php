 <?php
require_once '../includes/config.php';
if (!isLoggedIn() || !isFarmer()) redirect('../login.php');

$user_id = $_SESSION['user_id'];
$orders = $conn->query("SELECT o.*, u.name AS customer_name FROM orders o JOIN users u ON o.customer_id = u.id WHERE o.farmer_id = $user_id ORDER BY o.order_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Farmer Dashboard - AgroNexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="text-center mb-5">Incoming Orders</h2>
        <?php if ($orders->num_rows == 0): ?>
            <div class="alert alert-info text-center">No orders yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered bg-white">
                    <thead class="table-success">
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo $row['customer_name']; ?></td>
                            <td>₹<?php echo $row['total_amount']; ?></td>
                            <td><?php echo date('d M Y', strtotime($row['order_date'])); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $row['status']=='pending'?'warning':($row['status']=='confirmed'?'success':'secondary'); ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'pending'): ?>
                                    <a href="update-order.php?id=<?php echo $row['id']; ?>&action=accept" class="btn btn-sm btn-success">Accept</a>
                                    <a href="update-order.php?id=<?php echo $row['id']; ?>&action=reject" class="btn btn-sm btn-danger">Reject</a>
                                <?php elseif ($row['status'] == 'confirmed'): ?>
                                    <a href="update-order.php?id=<?php echo $row['id']; ?>&action=out_for_delivery" class="btn btn-sm btn-primary">Out for Delivery</a>
                                <?php elseif ($row['status'] == 'out_for_delivery'): ?>
                                    <a href="update-order.php?id=<?php echo $row['id']; ?>&action=delivered" class="btn btn-sm btn-success">Mark Delivered</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>