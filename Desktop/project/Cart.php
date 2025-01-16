<?php
// Database connection
$servername = 'localhost';
$username = 'root';
$password = 'password';
$database = 'test_shop';

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// User ID (static for now, replace with dynamic logic)
$user_id = 1;

// Fetch wishlist items from History table
$stmt = $conn->prepare("SELECT p.name AS product_name, p.image_url, p.price, b.name AS brand_name FROM History h 
    JOIN Products p ON h.product_id = p.product_id 
    JOIN Brands b ON p.brand_id = b.brand_id 
    WHERE h.user_id = ? AND h.action = 'wishlist'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$wishlist_items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }
        header {
            background-color: #716aca;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        header h1 {
            margin: 0;
            font-size: 24px;
        }
        main {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .cart-header {
            font-size: 24px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .total {
            font-weight: bold;
            text-align: right;
        }
        .checkout {
            display: block;
            text-align: center;
            padding: 10px 20px;
            background-color: #716aca;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .checkout:hover {
            background-color: #594aad;
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #716aca;
            color: #fff;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Your Cart</h1>
    </header>
    <main>
        <h2 class="cart-header">Your Cart [<?php echo count($wishlist_items); ?> items]</h2>
        <table>
            <thead>
                <tr>
                    <th>Items</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $subtotal = 0;
                foreach ($wishlist_items as $item): 
                    $subtotal += $item['price'];
                ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center;">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width: 50px; height: 50px; margin-right: 10px;">
                            <div>
                                <strong><?php echo htmlspecialchars($item['product_name']); ?></strong><br>
                                <small>Brand: <?php echo htmlspecialchars($item['brand_name']); ?></small>
                            </div>
                        </div>
                    </td>
                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="totals">
            <p class="total">Sub Total: $<?php echo number_format($subtotal, 2); ?></p>
            <p class="total">Total Tax: $<?php echo number_format($subtotal * 0.15, 2); ?></p>
            <p class="total">Grand Total: $<?php echo number_format($subtotal * 1.15, 2); ?></p>
        </div>
        <a href="#" class="checkout">Check Out</a>
    </main>
    <footer>
        &copy; 2025 Judah Shop. All rights reserved.
    </footer>
</body>
</html>
