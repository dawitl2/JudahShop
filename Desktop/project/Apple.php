<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apple Products - Judah Shop</title>
    <link rel="stylesheet" href="apple.css">
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
        </div>
    </header>
    <main>
        <header>
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

            // Fetch brand logo and description
            $brandSql = "SELECT logo_url, description FROM Brands WHERE name = 'Apple'";
            $brandResult = $conn->query($brandSql);

            if ($brandResult && $brandResult->num_rows > 0) {
                $brand = $brandResult->fetch_assoc();
                $logoUrl = $brand['logo_url'];
                $description = $brand['description'];
            } else {
                $logoUrl = 'images/default_logo.png'; // Fallback image
                $description = 'Leading technology brand.';
            }

            // Ensure the image file exists
            if (!file_exists($logoUrl)) {
                $logoUrl = 'images/default_logo.png'; // Default fallback if path is invalid
            }
            ?>
            <div class="apple_div">
                <img class="logo" src="<?php echo htmlspecialchars($logoUrl); ?>" alt="Apple Logo">
                <div class="card">
                    <div class="loader">
                        <p><?php echo htmlspecialchars($description); ?></p>
                        <p class="apple-tagline">"Think Different. Experience Innovation."</p>
                    </div>
                </div>
            </div>
        </header>

        <nav>
            <ul class="middle-nav-list">
                <?php
                // Fetch categories dynamically
                $categoriesSql = "SELECT category_id, name FROM Categories ORDER BY name";
                $categoriesResult = $conn->query($categoriesSql);

                if ($categoriesResult && $categoriesResult->num_rows > 0) {
                    while ($category = $categoriesResult->fetch_assoc()) {
                        echo "<li><a href='?category_id=" . $category['category_id'] . "'>" . htmlspecialchars($category['name']) . "</a></li>";
                    }
                }
                ?>
            </ul>
        </nav>

        <section class="product-listings">
            <div class="product_div">
                <?php
                // Fetch products filtered by category if category_id is set
                $categoryFilter = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

                $sql = "
                    SELECT 
                        Categories.name AS category_name, 
                        Products.name AS product_name, 
                        Products.price, 
                        Products.image_url 
                    FROM Products
                    JOIN Categories ON Products.category_id = Categories.category_id
                ";

                if ($categoryFilter) {
                    $sql .= " WHERE Categories.category_id = $categoryFilter";
                }

                $sql .= " ORDER BY Categories.name;";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $current_category = '';

                    while ($row = $result->fetch_assoc()) {
                        if ($current_category !== $row['category_name']) {
                            if (!empty($current_category)) {
                                echo '</div>';
                            }
                            $current_category = $row['category_name'];
                            echo "<h1 class='label'>{$current_category}</h1><div class='row_div'>";
                        }

                        echo "
                            <div class='product-item'>
                                <p class='product-name'>{$row['product_name']}</p>
                                <img class='a_img' src='{$row['image_url']}' alt='{$row['product_name']}'>
                                <div class='product_btn'>
                                    <p class='product-price'>From \${$row['price']}</p>
                                    <button class='buy-btn'>Buy</button>
                                </div>
                            </div>
                        ";
                    }
                    echo '</div>';
                } else {
                    echo '<p>No products available.</p>';
                }

                $conn->close();
                ?>
            </div>
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
