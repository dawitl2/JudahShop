<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "password";
$database = "test_shop";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection m
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// Get user_id from query parameters
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if (!$user_id) {
    die(json_encode(['error' => 'User ID is required']));
}

// Fetch user info
$sql = "SELECT name, email, username FROM Users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode($user);
} else {
    echo json_encode(['error' => 'User not found']);
}

$stmt->close();
$conn->close();
?>