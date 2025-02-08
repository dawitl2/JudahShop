<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "password";
$database = "test_shop";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check if connection is successful m
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch brands
$brands = [];
$result = $conn->query("SELECT name FROM Brands");
while ($row = $result->fetch_assoc()) {
    $brands[] = $row;
}

// Fetch categories
$categories = [];
$result = $conn->query("SELECT name FROM Categories");
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

// Return JSON response
echo json_encode([
    'brands' => $brands,
    'categories' => $categories
]);

$conn->close();

?>