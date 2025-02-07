<?php
// Database connection
$servername = 'localhost';
$username = 'root';
$password = 'password';
$database = 'test_shop';

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch product ID and user ID from the query string
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($product_id <= 0 || $user_id <= 0) {
    die("Invalid product or user ID.");
}

// Fetch product data from the database
$product = null;
if ($product_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM Products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}

if (!$product) {
    die("Product not found.");
}

// Fetch thumbnails from the database
$thumbnails = [];
$stmt = $conn->prepare("SELECT image_url FROM Thumbnails WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$thumbnails = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Ensure the original product image is included in thumbnails
array_unshift($thumbnails, ['image_url' => $product['image_url']]);

// Fetch similar products from the same category
$similar_products = [];
$stmt = $conn->prepare(
    "SELECT product_id, name, image_url, price FROM Products 
     WHERE category_id = ? AND product_id != ? 
     LIMIT 3"
);
$stmt->bind_param("ii", $product['category_id'], $product_id);
$stmt->execute();
$result = $stmt->get_result();
$similar_products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="product.css">
    <script>
        function changeImage(src) {
            document.getElementById('mainImage').src = src;
        }
    </script>
</head>
<body>
    <header>
        <div class="top_div">
          <p style="margin-left: 10px;">Judah shop</p>
          <div class="right-stuff">
            <button class="cart-button" onclick="window.location.href='Cart.php?user_id=<?php echo htmlspecialchars($user_id); ?>'" style="background-image: url('images/cart-icon.png'); width: 20px; height: 20px; background-size: cover;"></button>
        </div>
        </div>
    </header>
    <main>
        <section class="product-overview">
            <div class="product-images">
                <img 
                    id="mainImage" 
                    src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                    alt="<?php echo htmlspecialchars($product['name']); ?> Main Image" 
                    class="main-image">
                <div class="thumbnails">
                    <?php foreach ($thumbnails as $thumb): ?>
                        <img src="<?php echo htmlspecialchars($thumb['image_url']); ?>" 
                             alt="Thumbnail" 
                             class="thumbnail" 
                             onclick="changeImage('<?php echo htmlspecialchars($thumb['image_url']); ?>')">
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="product-details">
                <header>
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                    <button class="buy-button">BUY</button>
                </header>
                <p class="price">From $<?php echo number_format($product['price'], 2); ?></p>
                <ul>
                    <li><strong>Brand:</strong> <?php
                        $brandStmt = $conn->prepare("SELECT name FROM Brands WHERE brand_id = ?");
                        $brandStmt->bind_param("i", $product['brand_id']);
                        $brandStmt->execute();
                        $brandResult = $brandStmt->get_result();
                        echo htmlspecialchars($brandResult->fetch_assoc()['name']);
                        $brandStmt->close();
                    ?></li>
                    <li><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></li>
                    <li><strong>Features:</strong> <?php echo htmlspecialchars($product['features']); ?></li>
                </ul>
            </div>
        </section>

        <?php if (!empty($similar_products)): ?>
        <h2 style="padding-left: 50px; color: gray; margin-top: 50px">Similar Products</h2>
        <div class="relative_div">
            <?php foreach ($similar_products as $similar): ?>
            <div class="relative">
                <img class="relative_img" src="<?php echo htmlspecialchars($similar['image_url']); ?>" alt="<?php echo htmlspecialchars($similar['name']); ?>">
                <h1><?php echo htmlspecialchars($similar['name']); ?></h1>
                <p>Price: $<?php echo number_format($similar['price'], 2); ?></p>
                <button onclick="window.location.href='product.php?product_id=<?php echo $similar['product_id']; ?>&user_id=<?php echo $user_id; ?>'">Check out</button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
    <footer>
        <div class="foot_div">
            <div class="footer-content">
                <div class="footer-section about">
                    <h3>About Us</h3>
                    <p>Judah Shop is your one-stop store for premium electronics and accessories. We provide top brands and excellent customer service.</p>
                </div>
                <div class="footer-section links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Shop</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section contact">
                    <h3>Contact Us</h3>
                    <p>Email: support@judahshop.com</p>
                    <p>Phone: +123 456 7890</p>
                    <p>Address: 123 Tech Street, Innovation City</p>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; 2024 Judah Shop. All rights reserved.
            </div>
        </div>
    </footer>
    <script>
        // buy
        document.querySelector('.buy-button').addEventListener('click', function () {
            const productId = <?php echo $product_id; ?>;
            const userId = <?php echo $user_id; ?>;

            fetch('add_to_history.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: userId,
                    product_id: productId,
                    action: 'wishlist',
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Added to cart');
                } else {
                    alert('Failed to add to cart: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred.');
            });
        });
    </script>
</body>
</html>