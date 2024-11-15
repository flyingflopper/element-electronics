<?php
session_start();
include('dbconnect.php'); // Ensure the database connection

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$cartQuantity = 0;

// Fetch product ID from URL, e.g., product.php?id=1
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Prepare and execute query to fetch product details
$stmt = $dbcnx->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if product exists
if ($result && $result->num_rows > 0) {
    $product = $result->fetch_assoc();

    // Set variables for product details
    $productName = $product['name'];
    $productDescription = $product['description'];
    $productCategory = $product['category'];  // Assume your category column is named 'category'
    $basePrice = floatval($product['price']); // Ensure base price is a number
    $thumbnailUrls = explode(',', $product['thumbnail_urls']);
    $mainImage = trim($thumbnailUrls[0]); // First image as the main image
} else {
    $dbcnx->close();
    die("Product not found. Please return to the <a href='catalog.php'>catalog</a>.");
}

if ($isLoggedIn) {
    $user_id = $_SESSION['user_id'];
    // Query to get the sum of all quantities in the cart for the logged-in user
    $cartQuery = "SELECT SUM(quantity) AS total_quantity FROM carts WHERE user_id = ?";
    $stmt = $dbcnx->prepare($cartQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($cartRow = $result->fetch_assoc()) {
        $cartQuantity = $cartRow['total_quantity'] ?? 0;
    }
}

// Close the database connection
$dbcnx->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($productName); ?> - Element Electronics</title>
    <link rel="stylesheet" href="product_styles.css">
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

    <section class="product-page-container">
        <div class="product-image-container">
            <div class="thumbnail-list">
                <?php foreach ($thumbnailUrls as $index => $thumbnail): ?>
                    <img src="<?php echo htmlspecialchars(trim($thumbnail)); ?>" alt="Thumbnail" class="thumbnail" onclick="changeMainImage('<?php echo htmlspecialchars(trim($thumbnail)); ?>')">
                <?php endforeach; ?>
            </div>
            <div class="main-image">
                <img id="product-main-image" src="<?php echo htmlspecialchars($mainImage); ?>" alt="Main Product Image">
            </div>
        </div>

        <div class="product-details">
            <h1><?php echo htmlspecialchars($productName); ?></h1>
            <p class="description"><?php echo htmlspecialchars($productDescription); ?></p>

            <div class="configurations">
                <?php
                // Define an array of categories that require additional options
                $configurableCategories = ['laptops', 'phones', 'tablets'];
                if (in_array(strtolower($productCategory), $configurableCategories)): ?>
                    <h3>Select Configuration</h3>
                    <label for="color">Color:</label>
                    <select id="color">
                        <option value="Gold">Gold</option>
                        <option value="Silver">Silver</option>
                        <option value="Black">Black</option>
                        <option value="White">White</option>
                    </select>

                    <label for="storage">Storage:</label>
                    <select id="storage" onchange="updatePrice()">
                        <option value="256GB">256GB</option>
                        <option value="512GB">512GB</option>
                        <option value="1TB">1TB</option>
                        <option value="2TB">2TB</option>
                    </select>
                <?php endif; ?>
            </div>

            <div class="purchase-details">

                <div class="quantity-row">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="100">
                </div>
                <p class="price"><strong>Price:</strong> <span id="final-price">$<?php echo number_format($basePrice, 2); ?></span></p>
                <p class="delivery-date"><strong>Next possible delivery date:</strong> <span id="delivery-date">November 5, 2024</span></p>
                <button class="add-to-cart" onclick="addToCart(<?php echo $product_id; ?>)">Add to Cart</button>
            </div>
        </div>
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
    document.addEventListener("DOMContentLoaded", function () {
        // Only call updatePrice if storage exists
        if (document.getElementById('storage')) {
            updatePrice();  // This will set the initial price based on the default storage option
        }
        updateCartQuantity(); // Updates cart quantity in header on page load
    });

    const basePrice = parseFloat(<?php echo json_encode($basePrice); ?>);

    function updatePrice() {
        const storageSelect = document.getElementById('storage');
        const quantityInput = document.getElementById('quantity');

        // Check if elements exist before accessing their values
        const storageOption = storageSelect ? storageSelect.value : 'default';
        const quantity = quantityInput ? parseInt(quantityInput.value) : 1;

        const storageMultiplier = { '256GB': 0, '512GB': 100, '1TB': 200, '2TB': 400 };
        let finalPrice = basePrice + (storageMultiplier[storageOption] || 0);
        finalPrice *= quantity;  // Multiply by quantity
        document.getElementById('final-price').innerText = `$${finalPrice.toFixed(2)}`;
    }

    if (document.getElementById('quantity')) {
        document.getElementById('quantity').addEventListener('input', function() {
            updatePrice(); // Update the price live as they type
        });

        document.getElementById('quantity').addEventListener('change', function() {
            let quantity = parseInt(this.value);
            if (isNaN(quantity) || quantity < 1) {
                alert('Quantity must be a positive number.');
                this.value = 1;
            } else if (quantity > 100) {
                alert('Quantity cannot exceed 100.');
                this.value = 100;
            }
            updatePrice(); // Reconfirm the price update after validation
        });
    }

    if (document.getElementById('storage')) {
        document.getElementById('storage').addEventListener('change', updatePrice);  // Ensure this listener is still here if not already added
    }

    const isLoggedIn = <?= json_encode($isLoggedIn); ?>; // Pass the login status to JavaScript

    function addToCart(productId) {
        if (!isLoggedIn) {
            alert('Please log in to add items to your cart.');
            window.location.href = 'login_register.php';
            return;
        }

        const colorSelect = document.getElementById('color');
        const storageSelect = document.getElementById('storage');
        const quantityInput = document.getElementById('quantity');

        // Get values only if elements exist, otherwise set defaults or empty
        const color = colorSelect ? colorSelect.value : 'Default'; // Use 'default' or any other logic you've defined
        const storage = storageSelect ? storageSelect.value : 'Default'; // Use 'default' or adjust as needed
        const quantity = quantityInput ? parseInt(quantityInput.value) : 1;  // Default to 1 if no input available

        fetch('add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                product_id: productId,
                quantity: quantity,
                color: color,
                storage: storage
            })
        })
        .then(response => response.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    alert("Product added to cart!");
                    updateCartQuantity();
                } else {
                    alert(data.error || "Failed to add product to cart.");
                }
            } catch (error) {
                console.error('Error parsing JSON:', text);
                alert('Error processing your request. Please check the console for more information.');
            }
        })
        .catch(error => {
            console.error('Error in fetch operation:', error);
            alert('Error processing your request: ' + error.message);
        });
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
