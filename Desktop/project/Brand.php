<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Products - Judah Shop</title>
    <link rel="stylesheet" href="Brand.css">
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

            // Get the brand ID from the query parameter
            $brand_id = isset($_GET['brand_id']) ? intval($_GET['brand_id']) : 0;

            // Fetch brand logo and description
            $brandSql = "SELECT name, logo_url, description FROM Brands WHERE brand_id = $brand_id";
            $brandResult = $conn->query($brandSql);

            if ($brandResult && $brandResult->num_rows > 0) {
                $brand = $brandResult->fetch_assoc();
                $brand_name = htmlspecialchars($brand['name']);
                $logoUrl = htmlspecialchars($brand['logo_url']);
                $description = htmlspecialchars($brand['description']);
            } else {
                // Fallback for invalid or missing brand_id
                $brand_name = 'Unknown Brand';
                $logoUrl = 'images/default_logo.png';
                $description = 'Brand not found.';
            }
            ?>
            <div class="brand_div">
                <img class="logo" src="<?php echo $logoUrl; ?>" alt="<?php echo $brand_name; ?> Logo">
                <div class="card">
                    <div class="loader">
                        <p><?php echo $description; ?></p>
                        <p class="brand-tagline">"Discover the best products from <?php echo $brand_name; ?>."</p>
                    </div>
                </div>
            </div>
        </header>

        <nav>
            <ul class="middle-nav-list">
                <?php
                // Fetch categories dynamically for the selected brand
                $categoriesSql = "SELECT DISTINCT Categories.category_id, Categories.name 
                                FROM Products 
                                JOIN Categories ON Products.category_id = Categories.category_id 
                                WHERE Products.brand_id = $brand_id
                                ORDER BY Categories.name";
                $categoriesResult = $conn->query($categoriesSql);

                if ($categoriesResult && $categoriesResult->num_rows > 0) {
                    while ($category = $categoriesResult->fetch_assoc()) {
                        echo "<li><a href='?brand_id=$brand_id&category_id=" . $category['category_id'] . "'>" . htmlspecialchars($category['name']) . "</a></li>";
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
                    WHERE Products.brand_id = $brand_id
                ";

                if ($categoryFilter) {
                    $sql .= " AND Categories.category_id = $categoryFilter";
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
                            $current_category = htmlspecialchars($row['category_name']);
                            echo "<h1 class='label'>{$current_category}</h1><div class='row_div'>";
                        }

                        echo "
                            <div class='product-item'>
                                <p class='product-name'>" . htmlspecialchars($row['product_name']) . "</p>
                                <img class='a_img' src='" . htmlspecialchars($row['image_url']) . "' alt='" . htmlspecialchars($row['product_name']) . "'>
                                <div class='product_btn'>
                                    <p class='product-price'>From $" . number_format($row['price'], 2) . "</p>
                                    <button class='buy-btn'>Buy</button>
                                </div>
                            </div>
                        ";
                    }
                    echo '</div>';
                } else {
                    echo '<p>No products available for this brand.</p>';
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
                        <li><a href="index.php">Home</a></li>
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
