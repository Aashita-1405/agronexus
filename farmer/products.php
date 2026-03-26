 <?php
require_once '../includes/config.php';

// Only logged-in farmers
if (!isLoggedIn() || !isFarmer()) {
    redirect('../login.php');
}

$user_id = $_SESSION['user_id'];
$products = $conn->query("SELECT * FROM products WHERE farmer_id = $user_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Products - AgroNexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <div class="d-flex justify-content-between">
            <h2>My Products</h2>
            <a href="add-product.php" class="btn btn-success">Add New Product</a>
        </div>
        <?php if ($products->num_rows == 0): ?>
            <div class="alert alert-info mt-3">You haven't added any products yet.</div>
        <?php else: ?>
            <table class="table table-bordered mt-3">
                <thead><tr><th>Name</th><th>Price</th><th>Unit</th><th>Available</th><th>Action</th></tr></thead>
                <tbody>
                <?php while($p = $products->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $p['name']; ?></td>
                    <td>₹<?php echo $p['price']; ?></td>
                    <td><?php echo $p['unit']; ?></td>
                    <td><?php echo $p['quantity_available']; ?></td>
                    <td>
                        <a href="edit-product.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete-product.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>