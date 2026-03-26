 <?php
require_once '../includes/config.php';

// Load PHPMailer if not already loaded (adjust path if needed)
require_once '../vendor/phpmailer/src/Exception.php';
require_once '../vendor/phpmailer/src/PHPMailer.php';
require_once '../vendor/phpmailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isLoggedIn() || !isCustomer()) redirect('../login.php');

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) redirect('products.php');

$customer_id = $_SESSION['user_id'];
$address = $_POST['address'];
$payment_method = $_POST['payment_method'];
$discount = floatval($_POST['discount']);

// Group items by farmer
$farmer_groups = [];
foreach ($cart as $item) {
    $farmer_groups[$item['farmer_id']][] = $item;
}

// Get customer email
$cust = $conn->query("SELECT email, name FROM users WHERE id = $customer_id")->fetch_assoc();
$customer_email = $cust['email'];
$customer_name = $cust['name'];

$conn->begin_transaction();
$all_order_ids = [];
$all_items_list = [];
try {
    $total_all = 0;
    foreach ($cart as $item) {
        $total_all += $item['price'] * $item['quantity'];
        $all_items_list[] = $item['name'] . " x" . $item['quantity'];
    }

    foreach ($farmer_groups as $farmer_id => $items) {
        $farmer_total = 0;
        foreach ($items as $item) $farmer_total += $item['price'] * $item['quantity'];
        $farmer_discount = $discount * ($farmer_total / $total_all);
        $final_farmer_total = $farmer_total - $farmer_discount;

        $stmt = $conn->prepare("INSERT INTO orders (customer_id, farmer_id, total_amount, delivery_address, payment_method, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param('iidss', $customer_id, $farmer_id, $final_farmer_total, $address, $payment_method);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $all_order_ids[] = $order_id;

        foreach ($items as $item) {
            $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param('iiid', $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt2->execute();
            // Reduce stock
            $conn->query("UPDATE products SET quantity_available = quantity_available - {$item['quantity']} WHERE id = {$item['product_id']}");
        }
    }
    $conn->commit();

    // Clear cart
    unset($_SESSION['cart']);

    // Send email confirmation
    if (!empty($customer_email)) {
        sendOrderConfirmationEmail($customer_email, $customer_name, $all_order_ids, $cart, $total_all, $discount, $address, $payment_method);
    }

    $_SESSION['success'] = "Order placed successfully!";
    redirect('orders.php');
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Order failed: " . $e->getMessage();
    redirect('checkout.php');
}

// ---------- Email function ----------
function sendOrderConfirmationEmail($email, $name, $order_ids, $items, $subtotal, $discount, $address, $payment_method) {
    $mail = new PHPMailer(true);
    try {
        // SMTP settings – replace with your credentials
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sravinfotech@gmail.com';   // YOUR GMAIL
        $mail->Password   = 'eaiemknphrgwqoqe';       // APP PASSWORD
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));

        $mail->setFrom('your-email@gmail.com', 'AgroNexus');
        $mail->addAddress($email, $name);
        $mail->isHTML(true);
        $mail->Subject = 'Order Confirmation - AgroNexus';

        // Build product list
        $product_list = '<ul>';
        foreach ($items as $item) {
            $product_list .= "<li>{$item['name']} – ₹{$item['price']} x {$item['quantity']} = ₹" . ($item['price'] * $item['quantity']) . "</li>";
        }
        $product_list .= '</ul>';

        $discount_text = $discount > 0 ? "You saved ₹" . number_format($discount, 2) . " with our bulk offer!" : "";
        $body = "<h2>Order Placed Successfully!</h2>
                 <p>Hello $name,</p>
                 <p>Thank you for shopping with AgroNexus. Your order(s) #" . implode(', ', $order_ids) . " have been received.</p>
                 <h3>Order Details</h3>
                 $product_list
                 <p><strong>Subtotal:</strong> ₹$subtotal<br>
                 <strong>Discount:</strong> ₹$discount<br>
                 <strong>Total to Pay on Delivery:</strong> ₹" . ($subtotal - $discount) . "<br>
                 <strong>Delivery Address:</strong> $address<br>
                 <strong>Payment Method:</strong> $payment_method</p>
                 <p>$discount_text</p>
                 <p>We'll notify you once your order is processed. You can track your order in your dashboard.</p>";

        $mail->Body = $body;
        $mail->send();
    } catch (Exception $e) {
        error_log("Email failed: " . $mail->ErrorInfo);
        // Optionally store a message in session to inform user
        $_SESSION['notice'] = "Order placed, but we couldn't send a confirmation email.";
    }
}
?>