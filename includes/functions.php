 <?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
function isFarmer() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'farmer';
}
function isCustomer() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'customer';
}
function redirect($url) {
    header("Location: $url");
    exit;
}

// ----------------- EMAIL OTP using PHPMailer -----------------
function sendOtpEmail($email, $otp) {
    $phpmailer_path = __DIR__ . '/../vendor/phpmailer/src/Exception.php';
    if (!file_exists($phpmailer_path)) {
        $_SESSION['debug_otp'] = "PHPMailer missing. Your OTP is: $otp";
        return false;
    }
    require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
    require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sravinfotech@gmail.com';   // 👈 YOUR GMAIL
        $mail->Password   = 'eaiemknphrgwqoqe'; // 👈 NO SPACES
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('your-email@gmail.com', 'AgroNexus');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your AgroNexus OTP';
        $mail->Body    = "
            <h2>Welcome to AgroNexus!</h2>
            <p>Your OTP for login is: <b>$otp</b>. It expires in 5 minutes.</p>
            <hr>
            <p><strong>🌾 What can you do after logging in?</strong><br>
            - Browse fresh produce from local farmers<br>
            - Compare prices and view nutritional values<br>
            - Place orders directly with farmers via WhatsApp<br>
            - Track your orders and support local agriculture<br>
            </p>
            <p>Thank you for choosing direct farm‑to‑consumer!</p>
        ";

        $mail->send();
        return true;
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

// ----------------- Main OTP function -----------------
function sendOtp($phone, $email, $otp) {
    if (!empty($email)) {
        $result = sendOtpEmail($email, $otp);
        if ($result) {
            $_SESSION['message'] = "OTP sent to your email.";
        } else {
            $_SESSION['debug_otp'] = "Email failed. Your OTP is: $otp";
        }
    } else {
        $_SESSION['debug_otp'] = "Your OTP is: $otp";
    }
}

// Translation function
function getTranslation($key) {
    global $lang;
    return isset($lang[$key]) ? $lang[$key] : $key;
}
// Send discount notification email
function sendDiscountNotification($email, $discount_amount, $total_items) {
    $subject = "🎉 You've earned a 10% discount on AgroNexus!";
    $message = "
        <h2>Congratulations!</h2>
        <p>You have added <strong>$total_items items</strong> to your cart.</p>
        <p>As a reward, you have received a <strong>10% discount</strong> of <strong>₹$discount_amount</strong> on your current order.</p>
        <p>Visit your cart to see the updated total.</p>
        <p>Thank you for shopping with us!<br>AgroNexus Team</p>
    ";
    return sendSimpleEmail($email, $subject, $message);
}

// Generic email sender
function sendSimpleEmail($to, $subject, $body) {
    $phpmailer_path = __DIR__ . '/../vendor/phpmailer/src/Exception.php';
    if (!file_exists($phpmailer_path)) return false;
    require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
    require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sravinfotech.com';
        $mail->Password   = 'eaiemknphrgwqoqe';
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('your-email@gmail.com', 'AgroNexus');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->send();
        return true;
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>