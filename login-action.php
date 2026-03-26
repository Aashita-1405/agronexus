<?php
require_once 'includes/config.php';

$login = trim($_POST['login']);
$password = $_POST['password'];

// Check if login is phone or email
if (preg_match('/^[0-9]{10}$/', $login)) {
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE phone = ?");
    $stmt->bind_param('s', $login);
} else {
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->bind_param('s', $login);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] === 'farmer') {
            redirect('farmer/dashboard.php');
        } else {
            redirect('public/products.php');
        }
    } else {
        $_SESSION['error'] = "Invalid password.";
        redirect('login.php');
    }
} else {
    $_SESSION['error'] = "User not found.";
    redirect('login.php');
}
?>