 <?php require_once 'includes/config.php';
if (!isset($_SESSION['otp_user_id'])) redirect('login.php');

// Get user email from database to display
$user_id = $_SESSION['otp_user_id'];
$user = $conn->query("SELECT email, phone FROM users WHERE id = $user_id")->fetch_assoc();
$email = $user['email'] ?? 'your email';
$phone = $user['phone'] ?? $_SESSION['otp_phone'] ?? 'your phone';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP - AgroNexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container mt-5" style="max-width: 450px;">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white text-center">
                <h4>Verify OTP</h4>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['debug_otp'])): ?>
                    <div class="alert alert-success text-center">
                        <strong>🔐 <?php echo $_SESSION['debug_otp']; ?></strong>
                    </div>
                    <?php unset($_SESSION['debug_otp']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info text-center">
                        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <p class="text-center">Enter the 6-digit OTP sent to <strong><?php echo htmlspecialchars($email); ?></strong> (check spam folder).</p>
                <p class="text-center text-muted small">If you didn't receive it, check spam or contact support.</p>

                <form action="verify-otp-action.php" method="POST">
                    <div class="mb-3">
                        <input type="text" name="otp" class="form-control form-control-lg text-center" maxlength="6" placeholder="000000" required autofocus>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Verify OTP</button>
                </form>

                <hr>
                <p class="text-center text-muted small">Didn't receive OTP? <a href="send-otp.php?resend=<?php echo $phone; ?>">Resend</a></p>
            </div>
        </div>
    </div>
</body>
</html>