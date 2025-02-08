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

// User ID from query parameter
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($user_id === 0) {
    die("Invalid user ID.");
}

// Handle deletion request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['product_id'], $input['action'])) {
        $product_id = (int)$input['product_id'];
        $action = $input['action'];

        if ($action === 'remove') {
            // Remove the product from the wishlist
            $delete_stmt = $conn->prepare("DELETE FROM History WHERE user_id = ? AND product_id = ? AND action = 'wishlist' LIMIT 1");
            $delete_stmt->bind_param("ii", $user_id, $product_id);
            $delete_stmt->execute();
            $delete_stmt->close();
        } elseif ($action === 'increase') {
            // Add another instance of the same item to the wishlist
            $insert_stmt = $conn->prepare("INSERT INTO History (user_id, product_id, action) VALUES (?, ?, 'wishlist')");
            $insert_stmt->bind_param("ii", $user_id, $product_id);
            $insert_stmt->execute();
            $insert_stmt->close();
        } elseif ($action === 'decrease') {
            // Remove one instance of the item from the wishlist
            $delete_stmt = $conn->prepare("DELETE FROM History WHERE user_id = ? AND product_id = ? AND action = 'wishlist' LIMIT 1");
            $delete_stmt->bind_param("ii", $user_id, $product_id);
            $delete_stmt->execute();
            $delete_stmt->close();
        }

        echo json_encode(["success" => true, "product_id" => $product_id, "action" => $action]);
        exit;
    }
}

// Fetch wishlist items from History table and group by product
$stmt = $conn->prepare("SELECT p.product_id, p.name AS product_name, p.image_url, p.price, b.name AS brand_name, COUNT(*) AS quantity 
    FROM History h 
    JOIN Products p ON h.product_id = p.product_id 
    JOIN Brands b ON p.brand_id = b.brand_id 
    WHERE h.user_id = ? AND h.action = 'wishlist' 
    GROUP BY h.product_id");
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
            background-color: #919DF1;
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
        tr:hover {
            background-color: #f9eaea;
        }
        .remove-btn {
            background-color: #f44336;
            color: #fff;
            border: none;
            border-radius: 3px;
            padding: 5px 10px;
            cursor: pointer;
        }
        .quantity-btn {
            background-color: #919DF1;
            color: #fff;
            border: none;
            border-radius: 3px;
            padding: 5px 10px;
            cursor: pointer;
        }
        .quantity-btn:hover, .remove-btn:hover {
            opacity: 0.8;
        }
        .total {
            font-weight: bold;
            text-align: right;
        }
        .checkout {
            display: block;
            text-align: center;
            padding: 10px 20px;
            background-color: #919DF1;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .checkout:hover {
            background-color: #919DF1;
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #919DF1;
            color: #fff;
            margin-top: 20px;
        }
    </style>
    <script>
        function updateQuantity(productId, action) {
            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, action: action })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Refresh the page to reflect changes
                }
            });
        }

        function removeProduct(productId) {
            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, action: 'remove' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Refresh the page to reflect changes
                }
            });
        }
    </script>
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
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $subtotal = 0;
                foreach ($wishlist_items as $item): 
                    $total_price = $item['price'] * $item['quantity'];
                    $subtotal += $total_price;
                ?>
                <tr data-product-id="<?php echo $item['product_id']; ?>">
                    <td>
                        <div style="display: flex; align-items: center;">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width: 50px; height: 50px; margin-right: 10px;">
                            <div>
                                <strong><?php echo htmlspecialchars($item['product_name']); ?></strong><br>
                                <small>Brand: <?php echo htmlspecialchars($item['brand_name']); ?></small>
                            </div>
                        </div>
                    </td>
                    <td class="product-price">$<?php echo number_format($item['price'], 2); ?></td>
                    <td class="product-count quantity-controls">
                        <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['product_id']; ?>, 'decrease')">-</button>
                        <span><?php echo $item['quantity']; ?></span>
                        <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['product_id']; ?>, 'increase')">+</button>
                    </td>
                    <td><button class="remove-btn" onclick="removeProduct(<?php echo $item['product_id']; ?>)">Remove</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="totals">
            <p class="subtotal">Sub Total: $<?php echo number_format($subtotal, 2); ?></p>
            <p class="tax">Total Tax: $<?php echo number_format($subtotal * 0.15, 2); ?></p>
            <p class="grand-total">Grand Total: $<?php echo number_format($subtotal * 1.15, 2); ?></p>
        </div>
        <a href="#" class="checkout">Check Out</a>
    </main>
</body>
</html>