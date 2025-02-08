<?php
// Database connection
$servername = 'localhost';
$username = 'root';
$password = 'password';
$database = 'test_shop';

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Get the JSON input
$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'] ?? 0;
$product_id = $data['product_id'] ?? 0;
$action = $data['action'] ?? '';

if ($action === 'wishlist') {
    $quantity = 1; // Default quantity for wishlist
    // Fetch the price from the Products table
    $stmt = $conn->prepare("SELECT price FROM Products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $price = $product['price'] ?? null;
    $stmt->close();
} else {
    $quantity = $data['quantity'] ?? null;
    $price = $data['price'] ?? null;
}

if ($user_id > 0 && $product_id > 0 && $action) {
    $stmt = $conn->prepare("INSERT INTO History (user_id, product_id, action, quantity, price) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisid", $user_id, $product_id, $action, $quantity, $price);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
}

$conn->close();
?>
