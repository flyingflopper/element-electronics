<?php
session_start();
include('dbconnect.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user shipping information (if stored in the database)
$userQuery = "SELECT * FROM users WHERE id = ?";
$stmt = $dbcnx->prepare($userQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

$defaultAddressQuery = "SELECT * FROM addresses WHERE user_id = ? AND is_default = 1";
$stmt = $dbcnx->prepare($defaultAddressQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$defaultAddressResult = $stmt->get_result();
$defaultAddress = $defaultAddressResult->fetch_assoc();

// Get total quantity in the cart for the header
$quantityQuery = "SELECT SUM(quantity) AS total_quantity FROM carts WHERE user_id = ?";
$stmt = $dbcnx->prepare($quantityQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$cartQuantity = $row['total_quantity'] ?? 0;

// Fetch user's cart items from the database
$cartItemsQuery = "
    SELECT c.id, p.product_id, p.name, p.price AS base_price, p.thumbnail_urls AS thumbnail, c.quantity, c.color, c.storage
    FROM carts c
    JOIN products p ON c.product_id = p.product_id
    WHERE c.user_id = ?";
$stmt = $dbcnx->prepare($cartItemsQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cartResult = $stmt->get_result();

$cartTotal = 0;
$cartItems = [];
while ($item = $cartResult->fetch_assoc()) {
    $adjustedPrice = adjustPriceBasedOnStorage($item['base_price'], $item['storage']);
    $itemTotal = $adjustedPrice * $item['quantity'];
    $cartTotal += $itemTotal;

    $thumbnailUrls = explode(',', $item['thumbnail']);
    $mainThumbnail = trim($thumbnailUrls[0]);

    $cartItems[] = [
        'id' => $item['id'],
        'name' => $item['name'],
        'price' => $adjustedPrice,
        'quantity' => $item['quantity'],
        'color' => $item['color'],
        'storage' => $item['storage'],
        'thumbnail' => $mainThumbnail,
        'itemTotal' => $itemTotal
    ];
}

// Function to adjust price based on storage options
function adjustPriceBasedOnStorage($basePrice, $storage) {
    switch ($storage) {
        case '512GB':
            return $basePrice + 100;
        case '1TB':
            return $basePrice + 200;
        case '2TB':
            return $basePrice + 400;
        default:
            return $basePrice;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Save form data into session
  $_SESSION['name'] = $_POST['name'];
  $_SESSION['country'] = $_POST['country'];
  $_SESSION['address1'] = $_POST['address1'];
  $_SESSION['address2'] = $_POST['address2'];
  $_SESSION['postal_code'] = $_POST['postal_code'];
  $_SESSION['phone'] = $_POST['phone'];
  
  header("Location: checkout_step2.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping info - Element Electronics</title>
    <link rel="stylesheet" href="styles.css">
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
    
    <div class="checkout-container">
        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="checkout-form">
            <h2>Shipping Information</h2>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label for="country">Country:</label>
            <select id="country" name="country" required>
                <option value="">Select your country...</option>
                <!-- Dynamically generate options for countries -->
                <?php
                $countries = [
                    "Afghanistan", "Åland Islands", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica",
                    "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain",
                    "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia",
                    "Bosnia and Herzegovina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory",
                    "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada",
                    "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island",
                    "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, The Democratic Republic of The", "Cook Islands",
                    "Costa Rica", "Cote D'ivoire", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica",
                    "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia",
                    "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "French Guiana", "French Polynesia",
                    "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland",
                    "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guernsey", "Guinea", "Guinea-bissau", "Guyana", "Haiti",
                    "Heard Island and Mcdonald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland",
                    "India", "Indonesia", "Iran, Islamic Republic of", "Iraq", "Ireland", "Isle of Man", "Israel", "Italy", "Jamaica",
                    "Japan", "Jersey", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of",
                    "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao People's Democratic Republic", "Latvia", "Lebanon", "Lesotho",
                    "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macao",
                    "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta",
                    "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of",
                    "Moldova, Republic of", "Monaco", "Mongolia", "Montenegro", "Montserrat", "Morocco", "Mozambique", "Myanmar",
                    "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua",
                    "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau",
                    "Palestinian Territory, Occupied", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn",
                    "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Helena",
                    "Saint Kitts and Nevis", "Saint Lucia", "Saint Pierre and Miquelon", "Saint Vincent and The Grenadines", "Samoa",
                    "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore",
                    "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and The South Sandwich Islands",
                    "Spain", "Sri Lanka", "Sudan", "Suriname", "Svalbard and Jan Mayen", "Swaziland", "Sweden", "Switzerland",
                    "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand",
                    "Timor-leste", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan",
                    "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States",
                    "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Viet Nam", "Virgin Islands, British",
                    "Virgin Islands, U.S.", "Wallis and Futuna", "Western Sahara", "Yemen", "Zambia", "Zimbabwe"
                ];

                foreach ($countries as $country) {
                    echo "<option value='{$country}'" . (($defaultAddress['country'] ?? '') == $country ? " selected" : "") . ">{$country}</option>";
                }
                ?>
            </select>

            <label for="address1">Address line 1:</label>
            <input type="text" id="address1" name="address1" value="<?= htmlspecialchars($defaultAddress['address_line1'] ?? ''); ?>" required>

            <label for="address2">Address line 2 (optional):</label>
            <input type="text" id="address2" name="address2" value="<?= htmlspecialchars($defaultAddress['address_line2'] ?? ''); ?>">

            <label for="postal_code">Postal Code:</label>
            <input type="text" id="postal_code" name="postal_code" value="<?= htmlspecialchars($defaultAddress['postal_code'] ?? ''); ?>" required>

            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($defaultAddress['phone_number'] ?? ''); ?>" required>

            <button type="submit">Next</button>
        </form>

        <div class="cart-summary">
            <h2>Order Summary</h2>
            <?php if (count($cartItems) > 0): ?>
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">
                        <img src="<?php echo htmlspecialchars($item['thumbnail']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-thumbnail">
                        <div class="cart-item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <?php if ($item['color'] !== 'Default'): ?>
                                <p><strong>Color: </strong><?php echo htmlspecialchars($item['color']); ?></p>
                            <?php endif; ?>
                            <?php if ($item['storage'] !== 'Default'): ?>
                                <p><strong>Storage: </strong><?php echo htmlspecialchars($item['storage']); ?></p>
                            <?php endif; ?>
                            <p><strong>Unit Price: </strong>$<?php echo number_format($item['price'], 2); ?></p>
                            <p><strong>Quantity: </strong><?php echo $item['quantity']; ?></p>
                            <p><strong>Total: </strong>$<?php echo number_format($item['itemTotal'], 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <p><strong>Total Order Price:</strong> $<?php echo number_format($cartTotal, 2); ?></p>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>
    </div>

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
</body>
</html>
