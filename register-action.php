 <?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('register.php');
}

// Store posted values in session to repopulate form on error
$_SESSION['reg_name'] = trim($_POST['name']);
$_SESSION['reg_phone'] = trim($_POST['phone']);
$_SESSION['reg_email'] = trim($_POST['email']);
$_SESSION['reg_location'] = trim($_POST['user_location']);
$_SESSION['reg_role'] = $_POST['role'];

$name = $_SESSION['reg_name'];
$phone = $_SESSION['reg_phone'];
$email = $_SESSION['reg_email'] ?: null;
$location = $_SESSION['reg_location'];
$role = $_SESSION['reg_role'];
$password = $_POST['password'];

// Validate
if (strlen($name) < 2) {
    $_SESSION['error'] = "Name must be at least 2 characters.";
    redirect('register.php');
}
if (!preg_match('/^[0-9]{10}$/', $phone)) {
    $_SESSION['error'] = "Phone number must be 10 digits.";
    redirect('register.php');
}
if (strlen($password) < 4) {
    $_SESSION['error'] = "Password must be at least 4 characters.";
    redirect('register.php');
}
if (empty($location)) {
    $_SESSION['error'] = "Please enter your location.";
    redirect('register.php');
}

// Check if phone already exists
$check = $conn->prepare("SELECT id FROM users WHERE phone = ?");
$check->bind_param('s', $phone);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    $_SESSION['error'] = "Phone number already registered.";
    redirect('register.php');
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("INSERT INTO users (name, phone, email, user_location, password, role, is_verified) VALUES (?, ?, ?, ?, ?, ?, 1)");
$stmt->bind_param('ssssss', $name, $phone, $email, $location, $hashed_password, $role);
if (!$stmt->execute()) {
    $_SESSION['error'] = "Registration failed: " . $stmt->error;
    redirect('register.php');
}
$user_id = $stmt->insert_id;

// Log the user in immediately
$_SESSION['user_id'] = $user_id;
$_SESSION['role'] = $role;

// If farmer, redirect to complete profile
if ($role === 'farmer') {
    $check = $conn->query("SELECT user_id FROM farmer_profiles WHERE user_id = $user_id");
    if ($check->num_rows === 0) {
        redirect('farmer/complete-profile.php');
    }
}

redirect($role === 'farmer' ? 'farmer/dashboard.php' : 'public/products.php');
?>