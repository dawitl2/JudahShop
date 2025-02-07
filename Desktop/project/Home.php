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

        function searchProducts() {
            const searchInput = document.getElementById('search-input').value.toLowerCase();
            const productItems = document.querySelectorAll('.product-item');

            productItems.forEach(item => {
                const productName = item.querySelector('.product-name').textContent.toLowerCase();
                if (productName.includes(searchInput)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function toggleSearchField() {
            const searchField = document.getElementById('search-field');
            searchField.classList.toggle('show');
        }

        function applyFilters() {
            const minPrice = parseFloat(document.getElementById("min").value) || 0;
            const maxPrice = parseFloat(document.getElementById("max").value) || Infinity;
            const selectedBrand = document.querySelector("input[name='brand']:checked");
            const brand = selectedBrand ? selectedBrand.value.toLowerCase() : "";

            const productItems = document.querySelectorAll(".product-item");

            productItems.forEach(item => {
                const productName = item.querySelector(".product-name").textContent.toLowerCase();
                const productPrice = parseFloat(item.querySelector(".product-price").textContent.replace(/[^0-9.]/g, ""));
                const productBrand = item.getAttribute("data-brand").toLowerCase();

                let matchesBrand = brand === "" || productBrand === brand;
                let matchesPrice = productPrice >= minPrice && productPrice <= maxPrice;

                if (matchesBrand && matchesPrice) {
                    item.style.display = "block"; // Show matching products
                } 
                else {
                    item.style.display = "none"; // Hide non-matching products
                }
            });
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

        <div class="filter-container">  
            <button class="filter-button" style="background-image: url('images/filter.png'); width: 20px; height: 20px; background-size: cover; background-color: transparent;border: none;margin: 10px;"></button>
        </div>
            <div class="filterDropdown">
                <div class="filter_style">
                    <p style="font-size:18px">Price (ETB)</p>
                    <div class="range_div">
                        <div class="range_input">
                            <label for="min-1000002"> min </label>
                            <input type="text" id="min">
                        </div>
                        <div style="margin-left:10px ;margin-right:10px;">
                            <p>---</p>
                        </div>
                        <div class="range_input">
                            <label for="max-1000002"> max </label>
                            <input type="text" id="max" >
                        </div>
                    </div>
                    <div class="brand_select">
                        <p style="font-size:18px">Brand</p>
                        <?php
                            $conn = new mysqli('127.0.0.1', 'root', 'password', 'test_shop');
                            
                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }

                            $brandQuery = "SELECT name FROM Brands";
                            $brandResult = $conn->query($brandQuery);

                            if ($brandResult->num_rows > 0) {
                                while ($brand = $brandResult->fetch_assoc()) {
                                    $brandName = htmlspecialchars($brand['name']);
                                    echo "<label><input type='radio' name='brand' value='$brandName'> $brandName</label>";
                                }
                            } else {
                                echo "<p>No brands available</p>";
                            }

                            $conn->close();
                        ?>
                    </div>
                    <button id="apply_filter_button" onclick="applyFilters()">Apply Filters</button>
                </div>
            </div>
        
        <div class="right-stuff">
            <button class="search-button" onclick="toggleSearchField()" style="background-image: url('images/search-icon.png'); width: 20px; height: 20px; background-size: cover;"></button>
            <div id="search-field" class="search-field">
                <input type="text" id="search-input" placeholder="Search products..." oninput="searchProducts()">
            </div>
            <button class="cart-button" onclick="window.location.href='Cart.php?user_id=<?= htmlspecialchars($user_id) ?>'" style="background-image: url('images/cart-icon.png'); width: 20px; height: 20px; background-size: cover;"></button>
        </div>
    
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
                    <button class="AD_button" onclick="window.location.href='product.php?product_id=1&user_id=<?= htmlspecialchars($user_id) ?>'">More</button>
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
            $servername = '127.0.0.1';
            $username = 'root';
            $password = 'password';
            $database = 'test_shop';
            $conn = new mysqli($servername, $username, $password, $database);

            if ($conn->connect_error) {
                die("Connection failed: ". $conn->connect_error);
            }

            $selectedCategory = isset($_GET['category']) ? strtolower($_GET['category']) : '';

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

            if ($result->num_rows > 0) {
                $current_brand = "";

                while ($row = $result->fetch_assoc()) {
                    if ($current_brand !== $row['brand_name']) {
                        if (!empty($current_brand)) {
                            echo "</div>";
                        }
                        $current_brand = $row['brand_name'];
                        echo "
                            <h1 class='font'>
                                <a class='lable-p' href='brand.php?brand_id=" . $row['brand_id'] . "&user_id=$user_id'>" . htmlspecialchars($current_brand) . "</a> 
                                <span class='lable-g'>Products</span>
                            </h1>
                            <div class='row_div'>
                        ";
                    }

                    echo "
                        <div class='product-item' data-brand='" . strtolower(htmlspecialchars($row['brand_name'])) . "'>
                            <p class='product-name'>" . htmlspecialchars($row['product_name']) . "</p>
                            <img class='a_img' src='" . htmlspecialchars($row['image_url']) . "' alt='" . htmlspecialchars($row['product_name']) . "'>
                            <div class='product_btn'>
                                <p class='product-price'>From $" . number_format($row['price'], 2) . " or $50/mo for 24 mo.</p>
                                <button class='buy-btn'>Buy</button>
                            </div>
                        </div>";

                }
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
