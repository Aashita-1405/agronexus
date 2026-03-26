 <?php
require_once 'includes/config.php';

if (!isset($_SESSION['otp_user_id'])) {
    redirect('login.php');
}

$user_id = $_SESSION['otp_user_id'];
$otp = trim($_POST['otp']);

// First, fetch the user's stored OTP and expiry
$stmt = $conn->prepare("SELECT otp_code, otp_expiry FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "User not found.";
    redirect('verify-otp.php');
}

$row = $result->fetch_assoc();
$stored_otp = $row['otp_code'];
$expiry = $row['otp_expiry'];

// Debug: log values (optional)
error_log("OTP entered: $otp, stored: $stored_otp, expiry: $expiry, now: " . date('Y-m-d H:i:s'));

if ($stored_otp === $otp && strtotime($expiry) > time()) {
    // OTP is correct and not expired
    $conn->query("UPDATE users SET otp_code = NULL, otp_expiry = NULL, is_verified = 1 WHERE id = $user_id");
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role'] = $conn->query("SELECT role FROM users WHERE id = $user_id")->fetch_assoc()['role'];

    // If farmer, check profile completion
    if ($_SESSION['role'] === 'farmer') {
        $check = $conn->query("SELECT user_id FROM farmer_profiles WHERE user_id = $user_id");
        if ($check->num_rows === 0) {
            redirect('farmer/complete-profile.php');
        }
    }

    redirect($_SESSION['role'] === 'farmer' ? 'farmer/dashboard.php' : 'public/products.php');
} else {
    // OTP invalid or expired
    $_SESSION['error'] = "Invalid or expired OTP.";
    redirect('verify-otp.php');
}
?>