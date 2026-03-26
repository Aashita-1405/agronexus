<?php
require_once '../includes/config.php';
header('Content-Type: application/json');
$search = $_GET['q'] ?? '';
$search = $conn->real_escape_string($search);
$result = $conn->query("SELECT p.*, u.name AS farmer_name FROM products p JOIN users u ON p.farmer_id = u.id WHERE p.name LIKE '%$search%' OR u.name LIKE '%$search%'");
$products = [];
while($row = $result->fetch_assoc()) $products[] = $row;
echo json_encode($products);
?>