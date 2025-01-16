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

// Fetch product ID from the query string
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="product.css">
</head>
<body>
    <header>
        <div class="top_div">
            <ul class="top_ul">
                <li><a href="Home.php">Home</a></li>
                <li><a href="Brand.php">Brands</a></li>
                <li><a href="#">Coupons</a></li>
                <li><a href="#">Accessories</a></li>
            </ul>
        </div>
    </header>
    <main>
        <section class="product-overview">
            <div class="product-images">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?> Main Image" class="main-image">
                <div class="thumbnails">
                    <!-- Static thumbnails for now -->
                    <img src="images/iphone16pro-1.png" alt="Thumbnail 1" class="thumbnail">
                    <img src="images/iphone16pro-2.png" alt="Thumbnail 2" class="thumbnail">
                    <img src="images/iphone16pro-3.png" alt="Thumbnail 3" class="thumbnail">
                </div>
                <p>Available in 4 colors:</p>
                <div class="color-options">
                    <span class="color gold"></span>
                    <span class="color silver"></span>
                    <span class="color black"></span>
                    <span class="color blue"></span>
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

        <div class="main_div">
            <h2 style="text-align: center;">More about the product</h2>
            <?php if (!empty($product['video_url'])): ?>
            <div class="vid_div">
                <video class="vid" src="<?php echo htmlspecialchars($product['video_url']); ?>" muted loop autoplay></video>
            </div>
            <?php endif; ?>
            <div class="container_div">
                <div class="div_1"><img src="images/P_AD_1.png" alt=""></div>
                <div class="div_2"><img src="images/P_AD_2.png" alt=""></div>
                <div class="div_3"><img src="images/P_AD_4.png" alt=""></div>
                <div class="div_4"><img src="images/P_AD_3.png" alt=""></div>
            </div>
        </div>

        <h2 style="padding-left: 50px; color: gray;">Similar Compilations</h2>
        <div class="relative_div">
            <div class="relative">
                <img class="relative_img" src="images/relative.png" alt="">
                <h1>iPhone 16 plus</h1>
                <button>Check out</button>
            </div>

            <div class="relative">
                <img class="relative_img" src="images/relative.png" alt="">
                <h1>iPhone 16 plus</h1>
                <button>Check out</button>
            </div>

            <div class="relative">
                <img class="relative_img" src="images/relative.png" alt="">
                <h1>iPhone 16 plus</h1>
                <button>Check out</button>
            </div>
        </div>
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
        document.querySelector('.buy-button').addEventListener('click', function () {
            const productId = <?php echo $product_id; ?>; // Get the product ID
            const userId = 1; // Static user ID for now, replace with dynamic user ID logic if available

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
