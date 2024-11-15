<?php
session_start();
include('dbconnect.php'); // Ensure the database connection

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Pagination settings
$productsPerPage = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $productsPerPage;

// Filter settings
$conditions = [];
$params = [];

// Fetch all distinct categories from the database
$categoryQuery = "SELECT DISTINCT category FROM products ORDER BY category";
$categoryResult = $dbcnx->query($categoryQuery);
$categories = [];
while ($catRow = $categoryResult->fetch_assoc()) {
    $categories[] = $catRow['category'];
}

// Fetch all distinct brands from the database
$brandQuery = "SELECT DISTINCT brand FROM products ORDER BY brand";
$brandResult = $dbcnx->query($brandQuery);
$brands = [];
while ($brandRow = $brandResult->fetch_assoc()) {
    $brands[] = $brandRow['brand'];
}

// When the page loads, check which categories and brands were checked
$selectedCategories = isset($_GET['category']) ? $_GET['category'] : [];
$selectedBrands = isset($_GET['brand']) ? $_GET['brand'] : [];

// Get total quantity in the cart for the header
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$cartQuantityQuery = "SELECT SUM(quantity) AS total_quantity FROM carts WHERE user_id = ?";
$stmt = $dbcnx->prepare($cartQuantityQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$cartQuantity = $row['total_quantity'] ?? 0;

// Handle category filter from checkboxes
if (!empty($_GET['category']) && is_array($_GET['category'])) {
    $categoryFilter = array_map(function($item) use ($dbcnx) {
        return $dbcnx->real_escape_string($item);
    }, $_GET['category']);
    $conditions[] = "category IN ('" . implode("','", $categoryFilter) . "')";
}

// Handle brand filter from checkboxes
if (!empty($_GET['brand']) && is_array($_GET['brand'])) {
    $brandFilter = array_map(function($item) use ($dbcnx) {
        return $dbcnx->real_escape_string($item);
    }, $_GET['brand']);
    $conditions[] = "brand IN ('" . implode("','", $brandFilter) . "')";
}

// Price range filter
if (!empty($_GET['min_price']) && is_numeric($_GET['min_price'])) {
    $minPrice = $dbcnx->real_escape_string($_GET['min_price']);
    $conditions[] = "price >= $minPrice";
}
if (!empty($_GET['max_price']) && is_numeric($_GET['max_price'])) {
    $maxPrice = $dbcnx->real_escape_string($_GET['max_price']);
    $conditions[] = "price <= $maxPrice";
}

// Build the SQL condition
$sqlCondition = !empty($conditions) ? " WHERE " . implode(' AND ', $conditions) : "";

// Fetch products with pagination and filtering, including brand
$sql = "SELECT *, COALESCE(thumbnail_urls, image_url) AS display_image FROM products $sqlCondition LIMIT $productsPerPage OFFSET $offset";
$result = $dbcnx->query($sql);


// Calculate the total number of pages (adjusted for filters)
$totalProductsQuery = "SELECT COUNT(*) as total FROM products $sqlCondition";
$totalProductsResult = $dbcnx->query($totalProductsQuery);
$totalProductsRow = $totalProductsResult->fetch_assoc();
$totalProducts = $totalProductsRow['total'];
$totalPages = ceil($totalProducts / $productsPerPage);

// Return JSON response
echo json_encode(['quantity' => $cartQuantity]);
$dbcnx->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Element Electronics: Electronic Goods Catalog</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const minPriceInput = document.querySelector('input[name="min_price"]');
            const maxPriceInput = document.querySelector('input[name="max_price"]');
            const filterForm = document.querySelector('.filter-form');

            filterForm.addEventListener('submit', function(event) {
                const minPrice = parseFloat(minPriceInput.value);
                const maxPrice = parseFloat(maxPriceInput.value);

                // Check if min or max price is zero or less
                if (minPrice <= 0 || maxPrice <= 0) {
                    alert('Price must be greater than zero.');
                    event.preventDefault(); // Prevent the form from being submitted
                    return;
                }

                // Ensure max price is greater than min price
                if (minPrice > maxPrice) {
                    alert('Maximum price must be greater than minimum price.');
                    event.preventDefault(); // Prevent the form from being submitted
                }
            });
        });
    </script>
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo"><a href="homepage.php"><img src="logo.png" alt="Element Electronics" width="50px"></h1>
            <nav>
                <ul>
                    <li><a href="homepage.php">Home</a></li>
                    <li><a href="catalog.php">Shop</a></li>
                    <li><a href="aboutus.php">About Us</a></li>
                    <li><a href="contactpage.php">Contact</a></li>
                </ul>
            </nav>
            <div class="search">
                <form action="search_results.php" method="GET" class="search-form">
                    <img src="search.png" alt="Search Icon" id="search-icon">
                    <input type="search" id="search-input" name="query" placeholder="Search..." style="display: none;">
                    <button type="submit" id="search-button" style="display: none;">Search</button>
                </form>
            </div>
            <div class="account">
                <a href="<?= $isLoggedIn ? 'profile.php' : 'login_register.php' ?>">
                    <img src="account.png" alt="Account Icon">
                </a>
            </div>
            <div class="cart">
                <a href="cart.php"><img src="cart.png" alt="Cart Icon"> Cart (<span id="cart-quantity"><?= $cartQuantity ?></span>)</a>
            </div>
        </div>
    </header>

    <!-- Catalog Section with Sidebar and Main Content -->
    <section class="catalog-container">
        <!-- Sidebar with filters inside a form for submission -->
        <aside class="sidebar">

            <form method="GET" action="catalog.php" class="filter-form">
                <h2>Filter by:</h2>

                <!-- Categories Section -->
                <div class="filter-container">
                    <button type="button" class="dropdown-btn" data-target="categoryDropdown">Categories</button>
                    <div id="categoryDropdown" class="dropdown-content" style="display:none;">
                        <?php foreach ($categories as $category): ?>
                            <label>
                                <input type="checkbox" name="category[]" value="<?= htmlspecialchars($category) ?>"
                                    <?= (!empty($selectedCategories) && in_array($category, $selectedCategories)) ? 'checked' : '' ?>
                                > <?= htmlspecialchars($category) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Brands Section -->
                <div class="filter-container">
                    <button type="button" class="dropdown-btn" data-target="brandDropdown">Brands</button>
                    <div id="brandDropdown" class="dropdown-content" style="display:none;">
                        <?php foreach ($brands as $brand): ?>
                            <label>
                                <input type="checkbox" name="brand[]" value="<?= htmlspecialchars($brand) ?>"
                                    <?= (!empty($selectedBrands) && in_array($brand, $selectedBrands)) ? 'checked' : '' ?>
                                > <?= htmlspecialchars($brand) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Price Range -->
                <div class="filter">
                    <label for="min_price">Min Price</label>
                    <input type="number" id="min_price" name="min_price" placeholder="Min price" value="<?= $_GET['min_price'] ?? '' ?>">
                    <label for="max_price">Max Price</label>
                    <input type="number" id="max_price" name="max_price" placeholder="Max price" value="<?= $_GET['max_price'] ?? '' ?>">
                </div>
                
                <button type="submit" style="margin:10px">Apply Filters</button>
            </form>
        </aside>


        <!-- Main catalog content, separate from the form -->
        <main class="catalog-content">
            <h1>Electronic Goods Catalog</h1>
                <!-- Inside the product loop in catalog.php -->
                <div class="catalog-grid">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php
                            $images = explode(',', $row['display_image']); // Split the image URLs
                            $firstImage = $images[0]; // Get the first image
                            ?>
                            <div class="product-item">
                                <img src="<?= htmlspecialchars($firstImage) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="product-image">
                                <h3><?= htmlspecialchars($row['name']) ?></h3>
                                <p><?= htmlspecialchars($row['description']) ?></p>
                                <p><strong>$<?= number_format($row['price'], 2) ?></strong></p>
                                <?php
                                // Check the category and adjust the button accordingly
                                if (in_array(strtolower($row['category']), ['phones', 'tablets', 'laptops'])) {
                                    echo '<button onclick="addToCart(' . $row['product_id'] . ', \'Gold\', \'256GB\', 1)">Add to Cart</button>';
                                } else {
                                    echo '<button onclick="addToCart(' . $row['product_id'] . ', \'Default\', \'Default\', 1)">Add to Cart</button>';
                                }
                                ?>
                                <a href="product.php?id=<?= urlencode($row['product_id']) ?>"><button>View Details</button></a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No products found.</p>
                    <?php endif; ?>
                </div>
            <!-- Pagination Links -->
            <div class="pagination">
                <?php
                // Build query string for filters
                $queryData = $_GET;
                unset($queryData['page']);  // Remove the page number from current GET parameters
                $queryString = http_build_query($queryData);

                $prevPageUrl = htmlspecialchars("catalog.php?page=" . ($page - 1) . "&" . $queryString);
                $nextPageUrl = htmlspecialchars("catalog.php?page=" . ($page + 1) . "&" . $queryString);

                if ($page > 1): ?>
                    <a href="<?= $prevPageUrl ?>">Previous</a>
                <?php else: ?>
                    <a class="disabled">Previous</a>
                <?php endif; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= $nextPageUrl ?>">Next</a>
                <?php else: ?>
                    <a class="disabled">Next</a>
                <?php endif; ?>
            </div>
        </main>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2024 Element Electronics. All Rights Reserved.</p>
            <div class="subscription">
                <form id="subscription-form">
                    <input type="email" id="subscriber-email" placeholder="Subscribe to our newsletter" aria-label="Email subscription" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>
    </footer>

    <script src="script_search.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.dropdown-btn').forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault(); // Stop the form from submitting on button click
            const dropdown = document.getElementById(this.getAttribute('data-target'));

            // Toggle the visibility of the dropdown
            if (dropdown.style.display === 'block') {
                dropdown.style.display = 'none';
            } else {
                dropdown.style.display = 'block';
            }
        });
    });

    // This code stops the dropdown from closing when a checkbox is clicked
    document.querySelectorAll('.dropdown-content input[type="checkbox"]').forEach(function(checkbox) {
        checkbox.addEventListener('click', function(event) {
            event.stopPropagation(); // Prevent click from closing the dropdown
        });
    });

    // Close dropdowns when clicking outside
    window.addEventListener('click', function(event) {
        if (!event.target.matches('.dropdown-btn')) {
            document.querySelectorAll('.dropdown-content').forEach(function(dropdown) {
                if (!dropdown.contains(event.target)) { // Only close if the click is outside the dropdown
                    dropdown.style.display = 'none';
                }
            });
        }
    });
});


        // Simplified toggle function with display and z-index handling
        function toggleDropdown(dropdownId) {
            var dropdown = document.getElementById(dropdownId);
            if (dropdown.style.display === "none" || !dropdown.style.display) {
                closeAllDropdowns(); // Function to close all other dropdowns
                dropdown.style.display = "block";
                dropdown.style.zIndex = "1000"; // Ensure it is on top
            } else {
                dropdown.style.display = "none";
                dropdown.style.zIndex = "0"; // Reset when closed
            }
        }

        function closeAllDropdowns() {
            document.querySelectorAll('.dropdown-content').forEach(function(dropdown) {
                dropdown.style.display = "none";
                dropdown.style.zIndex = "0";
            });
        }

        function addToCart(productId, color, storage, quantity) {
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&color=${color}&storage=${storage}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart!');
                    updateCartQuantity(); // Update the cart quantity displayed
                } else {
                    alert(data.error || "Failed to add product to cart.");
                }
            })
            .catch(error => alert('Error adding product to cart: ' + error.message));
        }

        function updateCartQuantity() {
            fetch('cart_quantity_fetch.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('cart-quantity').innerText = data.quantity;
            })
            .catch(error => console.error('Error updating cart quantity:', error));
        }

    </script>

</body>
</html>
