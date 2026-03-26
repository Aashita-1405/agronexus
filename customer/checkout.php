 <?php
require_once '../includes/config.php';
if (!isLoggedIn() || !isCustomer()) redirect('../login.php');

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) redirect('products.php');

$total = 0;
$total_items = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
    $total_items += $item['quantity'];
}
$discount = 0;
if ($total_items > 10) $discount = $total * 0.10;
$final_total = $total - $discount;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout - AgroNexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4">Checkout</h2>
        <form action="place-order.php" method="POST">
            <input type="hidden" name="discount" value="<?php echo $discount; ?>">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($cart as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td class="text-center">₹<?php echo number_format($item['price'], 2); ?></td>
                                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                                        <td class="text-center">₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="text-end">
                                <p><strong>Total items:</strong> <?php echo $total_items; ?></p>
                                <p><strong>Subtotal:</strong> ₹<?php echo number_format($total, 2); ?></p>
                                <?php if ($discount > 0): ?>
                                    <p class="text-success"><strong>10% Discount:</strong> -₹<?php echo number_format($discount, 2); ?></p>
                                <?php endif; ?>
                                <h4 class="mt-3">Final Total: ₹<?php echo number_format($final_total, 2); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Delivery Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label>Delivery Address</label>
                                <textarea name="address" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label>Payment Method</label>
                                <select name="payment_method" class="form-control" required>
                                    <option value="Cash on Delivery">Cash on Delivery (COD)</option>
                                </select>
                                <small class="text-muted">Pay directly to the farmer when you receive the order.</small>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Place Order</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>