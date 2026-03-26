 <?php
require_once '../includes/config.php';
if (!isLoggedIn() || !isFarmer()) redirect('../login.php');

$user_id = $_SESSION['user_id'];
$farm_name = $_POST['farm_name'];
$farm_area = $_POST['farm_area'];
$location = $_POST['location'];
$min_order_value = $_POST['min_order_value'] ?? 0;
$crop_types = $_POST['crop_types'];
$transportation = $_POST['transportation'];
$num_workers = $_POST['num_workers'] ?: 0;
$pesticide_usage = $_POST['pesticide_usage'];
$fertilizer_usage = $_POST['fertilizer_usage'];
$artificial_materials = $_POST['artificial_materials'];

$stmt = $conn->prepare("INSERT INTO farmer_profiles (user_id, farm_name, farm_area, location, min_order_value, crop_types, transportation, num_workers, pesticide_usage, fertilizer_usage, artificial_materials) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('isdsssissss', $user_id, $farm_name, $farm_area, $location, $min_order_value, $crop_types, $transportation, $num_workers, $pesticide_usage, $fertilizer_usage, $artificial_materials);
$stmt->execute();

redirect('dashboard.php');
?>