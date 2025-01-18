<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judah Shop</title>
    <link rel="stylesheet" href="main.css">
    <script>
        function filterProducts(category) {
            const userId = new URLSearchParams(window.location.search).get('user_id');
            window.location.href = `?category=${category}&user_id=${userId}`;
        }
    </script>
</head>
<body>
<header>
    <div class="top_div">
        <ul class="top_ul">
            <li>Home</li>
            <li>Brands</li>
            <li>Coupons</li>
            <li>Accessories</li>
        </ul>
        <?php
        $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
        ?>
        <a href="Cart.php?user_id=<?= htmlspecialchars($user_id) ?>" class="cart-button">CART</a>
    </div>
</header>
<main>
    <section>
        <header>
            <div class="AD_container">
                <img class="AD_image" src="images/iphone_AD.png" alt="iPhone 16">
                <div class="AD_text">
                    <h1 class="AD_h1">iPHONE 16</h1>
                    <h2 class="AD_h2">Hello, Apple intelligence</h2>
                    <button class="AD_button">More</button>
                </div>
            </div>
        </header>

        <!-- Category Navigation -->
        <nav class="icon-nav">
            <?php
            $categories = [
                "Accessories", "Laptop", "Smartphones", "Tablet", "EarPhone", 
                "Watch", "Speakers", "TV", "HeadPhone", "Bud"
            ];
            foreach ($categories as $index => $category) {
                echo "<div class='icon-item' onclick=\"filterProducts('" . strtolower($category) . "')\">
                        <img class='AD_img' src='images/" . ($index + 1) . ".png' alt='{$category}'>
                        <p>{$category}</p>
                      </div>";
            }
            ?>
        </nav>

        <!-- Products and Goods -->
        <section class="product-listings">
            <?php
            // Database credentials
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

            // Get the selected category and user_id from query parameters
            $selectedCategory = isset($_GET['category']) ? strtolower($_GET['category']) : '';

            // SQL query with filtering by category
            $sql = "
                SELECT 
                    Products.product_id,
                    Products.name AS product_name, 
                    Products.price, 
                    Products.description, 
                    Products.image_url, 
                    Brands.name AS brand_name, 
                    Brands.brand_id 
                FROM Products 
                JOIN Brands ON Products.brand_id = Brands.brand_id
            ";

            if (!empty($selectedCategory)) {
                $sql .= " WHERE EXISTS (
                    SELECT 1 FROM Categories WHERE Categories.category_id = Products.category_id AND LOWER(Categories.name) = ?
                )";
            }

            $stmt = $conn->prepare($sql);

            if (!empty($selectedCategory)) {
                $stmt->bind_param("s", $selectedCategory);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            // Start generating the HTML
            if ($result->num_rows > 0) {
                $current_brand = "";

                while ($row = $result->fetch_assoc()) {
                    // Check if the brand has changed
                    if ($current_brand !== $row['brand_name']) {
                        if (!empty($current_brand)) {
                            // Close previous brand's divs
                            echo "</div>";
                        }
                        // Start a new brand section
                        $current_brand = $row['brand_name'];
                        echo "<div class='product_div'>
                                <h1 class='font'>
                                    <a class='label-p' href='brand.php?brand_id=" . $row['brand_id'] . "&user_id=$user_id'>" . htmlspecialchars($current_brand) . "</a> 
                                    <span class='label-g'>Products</span>
                                </h1>
                                <div class='row_div'>";
                    }

                    // Generate product HTML
                    echo "
                        <div class='product-item' onclick=\"window.location.href='product.php?product_id=" . $row['product_id'] . "&user_id=$user_id'\">
                            <p class='product-name'>" . htmlspecialchars($row['product_name']) . "</p>
                            <img class='a_img' src='" . htmlspecialchars($row['image_url']) . "' alt='" . htmlspecialchars($row['product_name']) . "'>
                            <div class='product_btn'>
                                <p class='product-price'>From $" . number_format($row['price'], 2) . " or $50/mo for 24 mo.</p>
                                <button class='buy-btn'>Buy</button>
                            </div>
                        </div>";
                }
                // Close the last brand's divs
                echo "</div>";
            } else {
                echo "<p>No products available for the selected category.</p>";
            }

            $stmt->close();
            $conn->close();
            ?>
        </section>
    </section>
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
</body>
</html>
