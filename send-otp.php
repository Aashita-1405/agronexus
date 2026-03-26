 <?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone']);
    // Validate phone format (10 digits)
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $_SESSION['error'] = "Invalid phone number format. Please enter a 10-digit number.";
        redirect('login.php');
    }

    $stmt = $conn->prepare("SELECT id, email FROM users WHERE phone = ?");
    $stmt->bind_param('s', $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['reg_phone'] = $phone;
        redirect('register.php');
    }

    $user = $result->fetch_assoc();
    $otp = rand(100000, 999999);
    $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    $update = $conn->prepare("UPDATE users SET otp_code = ?, otp_expiry = ? WHERE id = ?");
    $update->bind_param('ssi', $otp, $expiry, $user['id']);
    $update->execute();

    sendOtp($phone, $user['email'], $otp);

    $_SESSION['otp_user_id'] = $user['id'];
    $_SESSION['otp_phone'] = $phone;
    redirect('verify-otp.php');
}
?>