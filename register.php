 <?php require_once 'includes/config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - AgroNexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container mt-5" style="max-width: 500px;">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h3 class="mb-0">Create Account</h3>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <form action="register-action.php" method="POST" id="registerForm">
                    <div class="mb-3">
                        <label>Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required minlength="2" maxlength="100" value="<?php echo htmlspecialchars($_SESSION['reg_name'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label>Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control" required pattern="[0-9]{10}" maxlength="10" title="10-digit mobile number" value="<?php echo htmlspecialchars($_SESSION['reg_phone'] ?? ''); ?>">
                        <small class="text-muted">10-digit mobile number (e.g., 9876543210)</small>
                    </div>

                    <div class="mb-3">
                        <label>Email (optional)</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['reg_email'] ?? ''); ?>">
                        <small class="text-muted">Optional, used for notifications</small>
                    </div>

                    <div class="mb-3">
                        <label>Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="4">
                        <small class="text-muted">At least 4 characters</small>
                    </div>

                    <div class="mb-3">
                        <label>Your Location (City/District) <span class="text-danger">*</span></label>
                        <input type="text" name="user_location" class="form-control" required placeholder="e.g., Chennai, Coimbatore, Madurai" value="<?php echo htmlspecialchars($_SESSION['reg_location'] ?? ''); ?>">
                        <small class="text-muted">We'll show farmers near you</small>
                    </div>

                    <div class="mb-3">
                        <label>I am a <span class="text-danger">*</span></label>
                        <select name="role" class="form-control" required>
                            <option value="customer" <?php echo (($_SESSION['reg_role'] ?? '') == 'customer') ? 'selected' : ''; ?>>Customer</option>
                            <option value="farmer" <?php echo (($_SESSION['reg_role'] ?? '') == 'farmer') ? 'selected' : ''; ?>>Farmer</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Register</button>
                </form>

                <hr>
                <p class="text-center">Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            let phone = document.querySelector('input[name="phone"]').value;
            if (!/^\d{10}$/.test(phone)) {
                alert('Please enter a valid 10-digit phone number.');
                e.preventDefault();
                return false;
            }
            let pwd = document.querySelector('input[name="password"]').value;
            if (pwd.length < 4) {
                alert('Password must be at least 4 characters.');
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>