<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judah Shop</title>
    <link rel="stylesheet" href="main.css">
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

                // Fetch categories dynamically
                $categoriesSql = "SELECT category_id, name FROM Categories ORDER BY name";
                $categoriesResult = $conn->query($categoriesSql);

                if ($categoriesResult && $categoriesResult->num_rows > 0) {
                    while ($category = $categoriesResult->fetch_assoc()) {
                        echo "<div class='icon-item'>
                                <a href='?category_id=" . $category['category_id'] . "'>
                                    <img class='AD_img' src='images/" . $category['name'] . ".png' alt='" . htmlspecialchars($category['name']) . "'>
                                    <p>" . htmlspecialchars($category['name']) . "</p>
                                </a>
                              </div>";
                    }
                }
                ?>
            </nav>

            <!-- Products and Goods -->
            <section class="product-listings">
                <?php
                // Filter products by category if category_id is set
                $categoryFilter = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

                $sql = "
                    SELECT 
                        Products.name AS product_name, 
                        Products.price, 
                        Products.description, 
                        Products.image_url, 
                        Brands.name AS brand_name 
                    FROM Products 
                    JOIN Brands ON Products.brand_id = Brands.brand_id
                ";

                if ($categoryFilter) {
                    $sql .= " WHERE Products.category_id = $categoryFilter";
                }

                $sql .= " ORDER BY Brands.name";

                $result = $conn->query($sql);

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
                                        <a class='lable-p' href='" . htmlspecialchars($current_brand) . ".php'>" . htmlspecialchars($current_brand) . "</a> 
                                        <span class='lable-g'>Products</span>
                                    </h1>
                                    <div class='row_div'>";
                        }

                        // Generate product HTML
                        echo "
                            <div class='product-item'>
                                <p class='product-name'>" . htmlspecialchars($row['product_name']) . "</p>
                                <img class='a_img' src='" . htmlspecialchars($row['image_url']) . "' alt='" . htmlspecialchars($row['product_name']) . "'>
                                <div class='product_btn'>
                                    <p class='product-price'>From \$" . htmlspecialchars($row['price']) . " or \$50/mo for 24 mo.</p>
                                    <button class='buy-btn'>Buy</button>
                                </div>
                            </div>";
                    }
                    // Close the last brand's divs
                    echo "</div>";
                } else {
                    echo "<p>No products available.</p>";
                }

                // Close the connection
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
