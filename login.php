 <?php require_once 'includes/config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - AgroNexus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container mt-5" style="max-width: 400px;">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h3 class="mb-0">Login</h3>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <form action="login-action.php" method="POST">
                    <div class="mb-3">
                        <label>Phone Number or Email</label>
                        <input type="text" name="login" class="form-control" required placeholder="Enter phone or email">
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Login</button>
                </form>

                <hr>
                <p class="text-center">Don't have an account? <a href="register.php">Register</a></p>
            </div>
        </div>
    </div>
</body>
</html>