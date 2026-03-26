 <?php
require_once '../includes/config.php';
if (!isLoggedIn() || !isFarmer()) redirect('../login.php');

$order_id = intval($_GET['id']);
$action = $_GET['action'];

$allowed = ['accept', 'reject', 'out_for_delivery', 'delivered'];
if (!in_array($action, $allowed)) redirect('dashboard.php');

$status_map = [
    'accept' => 'confirmed',
    'reject' => 'rejected',
    'out_for_delivery' => 'out_for_delivery',
    'delivered' => 'delivered'
];
$new_status = $status_map[$action];

$user_id = $_SESSION['user_id'];
$check = $conn->query("SELECT id FROM orders WHERE id = $order_id AND farmer_id = $user_id");
if ($check->num_rows == 0) redirect('dashboard.php');

$conn->query("UPDATE orders SET status = '$new_status' WHERE id = $order_id");
redirect('dashboard.php');
?>