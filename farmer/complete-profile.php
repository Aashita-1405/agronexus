 <?php
require_once '../includes/config.php';
if (!isLoggedIn() || !isFarmer()) redirect('../login.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Complete Profile - AgroNexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h2>Complete Your Farm Profile</h2>
        <form action="save-profile.php" method="POST">
            <div class="mb-3">
                <label>Farm Name</label>
                <input type="text" name="farm_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Farm Area (acres)</label>
                <input type="number" step="0.01" name="farm_area" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Location (City/District)</label>
                <textarea name="location" class="form-control" rows="2" required></textarea>
            </div>
            <div class="mb-3">
                <label>Minimum Order Value (₹)</label>
                <input type="number" step="0.01" name="min_order_value" class="form-control" value="0">
                <small class="text-muted">Leave 0 for no minimum. Orders below this amount will not be accepted.</small>
            </div>
            <div class="mb-3">
                <label>Crop Types (comma separated)</label>
                <input type="text" name="crop_types" class="form-control">
            </div>
            <div class="mb-3">
                <label>Transportation Facility</label>
                <select name="transportation" class="form-control">
                    <option value="own">Own vehicle</option>
                    <option value="rental">Rental</option>
                    <option value="none">None</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Number of Workers</label>
                <input type="number" name="num_workers" class="form-control">
            </div>
            <div class="mb-3">
                <label>Pesticide Usage</label>
                <textarea name="pesticide_usage" class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <label>Fertilizer Usage</label>
                <textarea name="fertilizer_usage" class="form-control" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <label>Other Artificial Materials</label>
                <textarea name="artificial_materials" class="form-control" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Profile</button>
        </form>
    </div>
</body>
</html>