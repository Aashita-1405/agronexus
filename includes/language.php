 <?php
$available_langs = ['en', 'ta'];
if (isset($_GET['lang']) && in_array($_GET['lang'], $available_langs)) {
    $_SESSION['lang'] = $_GET['lang'];
    // Remove query string to avoid duplication
    $url = strtok($_SERVER["REQUEST_URI"], '?');
    header("Location: $url");
    exit;
}
$lang_code = $_SESSION['lang'] ?? 'en';
require_once __DIR__ . "/../lang/$lang_code.php";
?>