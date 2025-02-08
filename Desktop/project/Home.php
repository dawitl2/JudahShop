<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Judah Shop</title>
    <link rel="stylesheet" href="main.css">
    <script>
        // Filter products by category
        function filterProducts(category) {
            const userId = new URLSearchParams(window.location.search).get('user_id');
            window.location.href = `?category=${category}&user_id=${userId}`;
        }

        // Search products by name
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

        // Toggle search field visibility
        function toggleSearchField() {
            const searchField = document.getElementById('search-field');
            searchField.classList.toggle('show');
        }

        // Apply filters (price, brand, category)
        function applyFilters() {
            const minPrice = document.getElementById("min-price").value;
            const maxPrice = document.getElementById("max-price").value;
            const brand = document.getElementById("brand-filter").value;
            const category = document.getElementById("category-filter").value;
            const userId = new URLSearchParams(window.location.search).get("user_id");

            let query = `?user_id=${userId}`;
            if (minPrice) query += `&min_price=${minPrice}`;
            if (maxPrice) query += `&max_price=${maxPrice}`;
            if (brand) query += `&brand=${brand}`;
            if (category) query += `&category=${category}`;

            window.location.href = query;
        }

        // Fetch brands and categories dynamically
        function fetchFilters() {
            fetch('get_filters.php')
                .then(response => response.json())
                .then(data => {
                    const brandFilter = document.getElementById('brand-filter');
                    const categoryFilter = document.getElementById('category-filter');

                    // Populate brands
                    data.brands.forEach(brand => {
                        const option = document.createElement("option");
                        option.value = brand.name;
                        option.textContent = brand.name;
                        brandFilter.appendChild(option);
                    });

                    // Populate categories
                    data.categories.forEach(category => {
                        const option = document.createElement("option");
                        option.value = category.name;
                        option.textContent = category.name;
                        categoryFilter.appendChild(option);
                    });
                });
        }

        // Toggle user info dropdown
        function toggleUserInfo() {
            const userInfo = document.getElementById("user-info");
            if (userInfo.style.display === "none" || userInfo.style.display === "") {
                userInfo.style.display = "block";
            } else {
                userInfo.style.display = "none";
            }
        }

        // Expand top_div on hover and show filter panel
        document.addEventListener("DOMContentLoaded", function () {
            const topDiv = document.querySelector(".top_div");
            const filterPanel = document.createElement("div");

            // Create filter panel structure
            filterPanel.innerHTML = `
                <div class="filter-panel" style="padding-top: 5vh; padding-left: 3vh;">
                    <h3>Filter Products</h3>
                    <label>Price Range:</label>
                    <input type="number" id="min-price" placeholder="Min Price">
                    <input type="number" id="max-price" placeholder="Max Price">
                    <label>Brand:</label>
                    <select id="brand-filter">
                        <option value="">All Brands</option>
                    </select>
                    <label>Category:</label>
                    <select id="category-filter">
                        <option value="">All Categories</option>
                    </select>
                    <button onclick="applyFilters()">Go</button>
                </div>
            `;

            filterPanel.style.display = "none"; // Initially hidden
            topDiv.appendChild(filterPanel);

            // Expand top_div when hovered
            topDiv.addEventListener("mouseenter", function () {
                topDiv.style.height = "150px";
                topDiv.style.transition = "height 0.3s ease-in-out";
                filterPanel.style.display = "block"; // Show filters
            });

            topDiv.addEventListener("mouseleave", function () {
                topDiv.style.height = "";
                filterPanel.style.display = "none"; // Hide filters
            });

            // Populate filters dynamically
            fetchFilters();
        });
    </script>
    <style>
        .logo {
            color: #956afa;
        }

        /* User info panel as a dropdown in the header */
        .user-info-panel {
            position: absolute;
            top: 40px;
            right: 0;
            background: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            width: 250px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
            font-size: 14px;
            display: none; /* Initially hidden */
        }
        .user-info-panel p {
            margin: 5px 0;
        }
        .close-user-info {
            background: transparent;
            border: none;
            font-size: 16px;
            float: right;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "password";
    $database = "test_shop";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check if connection is successful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get user_id from URL
    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
    ?>
    <header>
        <div class="top_div">
            <div class="top_content">
                <p class="logo">Judah Shop</p>
                <div class="right-stuff">
                    <button class="search-button" onclick="toggleSearchField()" style="background-image: url('images/search-icon.png'); width: 20px; height: 20px; background-size: cover;"></button>
                    <div id="search-field" class="search-field">
                        <input type="text" id="search-input" placeholder="Search products..." oninput="searchProducts()">
                    </div>
                    <button class="cart-button" onclick="window.location.href='Cart.php?user_id=<?= htmlspecialchars($user_id) ?>'" style="background-image: url('images/cart-icon.png'); width: 20px; height: 20px; background-size: cover;"></button>
                    <!-- Account button toggles the user info dropdown -->
                    <button class="account-button" onclick="toggleUserInfo()" style="background-image: url('images/profile.png'); width: 20px; height: 20px; background-size: cover;"></button>
                    <!-- User info dropdown panel -->
                    <div id="user-info" class="user-info-panel">
                        <button class="close-user-info" onclick="toggleUserInfo()">X</button>
                        <?php
                        if (!empty($user_id)) {
                            $stmt_user = $conn->prepare("SELECT name, email, username FROM Users WHERE user_id = ?");
                            if ($stmt_user) {
                                $stmt_user->bind_param("i", $user_id);
                                $stmt_user->execute();
                                $result_user = $stmt_user->get_result();
                                if ($result_user->num_rows > 0) {
                                    $user_data = $result_user->fetch_assoc();
                                    echo "<p><strong>Name:</strong> " . htmlspecialchars($user_data['name']) . "</p>";
                                    echo "<p><strong>Email:</strong> " . htmlspecialchars($user_data['email']) . "</p>";
                                    echo "<p><strong>Username:</strong> " . htmlspecialchars($user_data['username']) . "</p>";
                                } else {
                                    echo "<p>User not found.</p>";
                                }
                                $stmt_user->close();
                            } else {
                                echo "<p>Error fetching user info.</p>";
                            }
                        } else {
                            echo "<p>No user logged in.</p>";
                        }
                        ?>
                    </div>
                </div>
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
                    "ALL", "Laptop", "Smartphones", "Tablet", "EarPhone",
                    "Watch", "Speakers", "TV", "HeadPhone", "Bud"
                ];
                foreach ($categories as $index => $category) {
                    if ($category === "ALL") {
                        echo "<div class='icon-item' onclick=\"window.location.href='?user_id=$user_id'\">
                                <img class='AD_img' src='images/" . ($index + 1) . ".png' alt='{$category}'>
                                <p>{$category}</p>
                              </div>";
                    } else {
                        echo "<div class='icon-item' onclick=\"filterProducts('" . strtolower($category) . "')\">
                                <img class='AD_img' src='images/" . ($index + 1) . ".png' alt='{$category}'>
                                <p>{$category}</p>
                              </div>";
                    }
                }
                ?>
            </nav>

            <!-- Products and Goods -->
            <section class="product-listings">
                <?php
                // Fetch products based on filters
                $selectedCategory = isset($_GET['category']) ? strtolower($_GET['category']) : '';
                $minPrice = isset($_GET['min_price']) ? $_GET['min_price'] : '';
                $maxPrice = isset($_GET['max_price']) ? $_GET['max_price'] : '';
                $brand = isset($_GET['brand']) ? $_GET['brand'] : '';

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
                    WHERE 1=1
                ";

                $params = [];
                $types = "";

                if (!empty($selectedCategory) && $selectedCategory !== "all") {
                    $sql .= " AND EXISTS (
                        SELECT 1 FROM Categories WHERE Categories.category_id = Products.category_id AND LOWER(Categories.name) = ?
                    )";
                    $params[] = $selectedCategory;
                    $types .= "s";
                }
                if (!empty($minPrice)) {
                    $sql .= " AND Products.price >= ?";
                    $params[] = $minPrice;
                    $types .= "d";
                }
                if (!empty($maxPrice)) {
                    $sql .= " AND Products.price <= ?";
                    $params[] = $maxPrice;
                    $types .= "d";
                }
                if (!empty($brand)) {
                    $sql .= " AND Brands.name = ?";
                    $params[] = $brand;
                    $types .= "s";
                }

                $stmt = $conn->prepare($sql);

                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
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
                            <div class='product-item' onclick=\"window.location.href='product.php?product_id=" . $row['product_id'] . "&user_id=$user_id'\">
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
                    echo "<p>No products available for the selected filters.</p>";
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