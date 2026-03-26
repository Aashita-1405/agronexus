 <?php
session_start();
require_once '../includes/config.php';
if (!isLoggedIn() || !isCustomer()) redirect('../login.php');
$index = intval($_GET['index']);
if (isset($_SESSION['cart'][$index])) unset($_SESSION['cart'][$index]);
$_SESSION['cart'] = array_values($_SESSION['cart']);
redirect('cart.php');
?>