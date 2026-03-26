 <?php
require_once '../includes/config.php';
if (!isLoggedIn() || !isFarmer()) redirect('../login.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $farmer_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $unit = $_POST['unit'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];
    $importance = $_POST['importance'];
    $shelf_life = $_POST['shelf_life'];
    $image_url = '';

    $stmt = $conn->prepare("INSERT INTO products (farmer_id, name, category, price, unit, quantity_available, description, importance, shelf_life, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issdsissss', $farmer_id, $name, $category, $price, $unit, $quantity, $description, $importance, $shelf_life, $image_url);
    $stmt->execute();
    redirect('products.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Product - AgroNexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2>Add New Product</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Product Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Category</label>
                <select name="category" class="form-control">
                    <option>Vegetables</option>
                    <option>Fruits</option>
                    <option>Millets</option>
                    <option>Dairy</option>
                    <option>Pulses</option>
                    <option>Grains</option>
                    <option>Greens</option>
                </select>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label>Unit</label>
                    <select name="unit" class="form-control">
                        <option>kg</option><option>dozen</option><option>bunch</option><option>piece</option><option>litre</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Quantity Available</label>
                    <input type="number" name="quantity" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <label>Nutritional Value / Importance</label>
                <textarea name="importance" class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <label>Shelf Life</label>
                <input type="text" name="shelf_life" class="form-control" placeholder="e.g., 5 days, 6 months" required>
            </div>
            <button type="submit" class="btn btn-success">Save Product</button>
        </form>
    </div>
</body>
</html>