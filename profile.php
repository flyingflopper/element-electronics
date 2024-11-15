<?php
session_start();
include('dbconnect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php"); // Redirect to login if not logged in
    exit;
}

// Fetch user details including avatar
$user_id = $_SESSION['user_id'];
$stmt = $dbcnx->prepare("SELECT username, email, avatar_path, gender, birthday FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$username = $user['username'] ?? 'N/A';
$email = $user['email'] ?? 'N/A';
$avatarPath = !empty($user['avatar_path']) ? $user['avatar_path'] : 'default-avatar.png';

// Get total quantity in the cart for the header
$cartQuantityQuery = "SELECT SUM(quantity) AS total_quantity FROM carts WHERE user_id = ?";
$stmt = $dbcnx->prepare($cartQuantityQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cartQuantity = $result->fetch_assoc()['total_quantity'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Element Electronics</title>
    <link rel="stylesheet" href="profile_styles.css">
</head>
<body>
    <?php if (isset($_SESSION['message'])): ?>
        <script>alert('<?= $_SESSION['message'] ?>');</script>
        <?php unset($_SESSION['message']); // Clear the message after displaying ?>
    <?php endif; ?>
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
                <a href="profile.php">
                    <img src="account.png" alt="Account Icon">
                </a>
            </div>
            <div class="cart">
                <a href="cart.php"><img src="cart.png" alt="Cart Icon"> Cart (<span id="cart-quantity"><?= $cartQuantity ?></span>)</a>
            </div>
        </div>
    </header>

    <div class="profile-container">
        <aside class="sidebar">
            <div class="user-info">
                <img src="<?= htmlspecialchars($avatarPath) ?>" alt="User Avatar" class="user-avatar">
                <h3><?= htmlspecialchars($username) ?></h3>
                <form action="upload_avatar.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="avatar" id="avatarInput" accept="image/*" onchange="handleFileSelected();">
                    <button type="submit" id="uploadButton" style="display:none;">Upload Avatar</button>
                </form>
            </div>
            <nav class="profile-nav">
                <a href="#" onclick="showPersonalInfo();">Personal Information</a>
                <a href="#" onclick="showOrders();">Orders</a>
                <a href="#" onclick="showAddresses();">Saved Addresses</a>
                <a href="#" onclick="showSettings();">Settings</a>
                <a href="logout.php" >Log Out</a>
            </nav>
        </aside>

        <main class="profile-content">
            <form action="update_profile.php" method="POST" class="profile-form">
                <h2>Personal Information</h2>
                <label for="username">Name:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>">

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>">

                <label for="birthday">Birthday:</label>
                <input type="date" id="birthday" name="birthday" value="<?= isset($user['birthday']) ? $user['birthday'] : '' ?>" required>

                <div class="gender-selection">
                    <label>Gender:</label>
                    <input type="radio" id="male" name="gender" value="male" <?= $user['gender'] === 'male' ? 'checked' : '' ?>>
                    <label for="male">Male</label>
                    <input type="radio" id="female" name="gender" value="female" <?= $user['gender'] === 'female' ? 'checked' : '' ?>>
                    <label for="female">Female</label>
                </div>

                <button type="submit">Save</button>
                <button type="button" onclick="location.href='homepage.php'">Cancel</button>
            </form>
        </main>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Setup event listeners for links
            const showAddressesLink = document.getElementById('showAddressesLink');
            const showPersonalInfoLink = document.getElementById('showPersonalInfoLink');

            if (showAddressesLink) {
                showAddressesLink.addEventListener('click', function (event) {
                    event.preventDefault();
                    showAddresses();
                });
            }

            if (showPersonalInfoLink) {
                showPersonalInfoLink.addEventListener('click', function (event) {
                    event.preventDefault();
                    showPersonalInfo();
                });
            }

            // Personal Info form submission
            const personalInfoForm = document.getElementById('personalInfoForm');
            if (personalInfoForm) {
                personalInfoForm.addEventListener('submit', function (event) {
                    event.preventDefault();
                    submitPersonalInfo(personalInfoForm);
                });
            }
        });

        function submitPersonalInfo(form) {
            const formData = new FormData(form);

            fetch('update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Profile updated successfully.');
                    // Optionally, refresh parts of your page here.
                } else {
                    alert('Error updating profile: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error updating profile:', error);
                alert('Failed to process the request.');
            });
        }

        function handleFileSelected() {
            const uploadButton = document.getElementById('uploadButton');
            const avatarInput = document.getElementById('avatarInput');
            if (avatarInput.files.length > 0) {
                uploadButton.style.display = 'block'; // Show the upload button when file is selected
                avatarInput.title = "Update File"; // Change the title of the input
            }
        }

        // Check if there is an existing avatar and update the input title accordingly
        window.onload = function() {
            const avatarInput = document.getElementById('avatarInput');
            if (avatarInput.value !== '') {
                avatarInput.title = "Update File";
            }
        }

        function showPersonalInfo() {
        // Select the profile content area and inject the personal information form
        const profileContent = document.querySelector('.profile-content');
        profileContent.innerHTML = `
            <form action="update_profile.php" method="POST" class="profile-form">
                <h2>Personal Information</h2>
                <label for="username">Name:</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>">

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>">

                <label for="birthday">Birthday:</label>
                <input type="date" id="birthday" name="birthday" value="<?= isset($user['birthday']) ? $user['birthday'] : '' ?>" required>

                <div class="gender-selection">
                    <label>Gender:</label>
                    <input type="radio" id="male" name="gender" value="male" <?= $user['gender'] === 'male' ? 'checked' : '' ?>>
                    <label for="male">Male</label>
                    <input type="radio" id="female" name="gender" value="female" <?= $user['gender'] === 'female' ? 'checked' : '' ?>>
                    <label for="female">Female</label>
                </div>

                <button type="submit">Save</button>
                <button type="button" onclick="location.href='homepage.php'">Cancel</button>
            </form>
        `;
    }

        function showOrders() {
            fetch('fetch_orders.php')
                .then(response => response.json())
                .then(data => {
                    const profileContent = document.querySelector('.profile-content');
                    profileContent.innerHTML = '<h2>Your Orders</h2><div class="orders-grid"></div>';
                    const grid = profileContent.querySelector('.orders-grid');
                    
                    if (data.orders && data.orders.length > 0) {
                        data.orders.forEach(order => {
                            const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
                            const orderDate = new Date(order.order_date).toLocaleDateString("en-US", dateOptions);
                            grid.innerHTML += `
                                <div class="order-card">
                                    <h3>Order #${order.order_id}</h3>
                                    <p><strong>Date:</strong> ${orderDate}</p>
                                    <p><strong>Status:</strong> ${order.status}</p>
                                    <p><strong>Total:</strong> $${parseFloat(order.total).toFixed(2)}</p>
                                    <button onclick="showOrderDetails(${order.order_id})">View Details</button>
                                </div>
                            `;
                        });
                    } else {
                        grid.innerHTML = '<p>You have no orders.</p>';
                    }
                })
                .catch(error => console.error('Error loading orders:', error));
        }

        function showOrderDetails(orderId) {
            fetch(`fetch_order_details.php?order_id=${orderId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error, status = ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Fetched data:", data); // Log the data for inspection

                    const profileContent = document.querySelector('.profile-content');
                    
                    // Check if the expected data structure is present
                    if (data && data.order && data.items && data.items.length > 0) {
                        const order = data.order; // General order details
                        const items = data.items; // Specific items in the order
                        
                        profileContent.innerHTML = `
                            <div class="order-details-wrapper">
                                <!-- Left side: Order Details in a grid -->
                                <div class="order-details-container">
                                    <h2><strong>Order ID:</strong> ${order.order_id}</h2>
                                    <div class="order-details-grid">
                                        <p><strong>Name:</strong> ${order.name}</p>
                                        <p><strong>Country:</strong> ${order.country}</p>
                                        <p><strong>Address 1:</strong> ${order.address1}</p>
                                        <p><strong>Address 2:</strong> ${order.address2}</p>
                                        <p><strong>Postal Code:</strong> ${order.postal_code}</p>
                                        <p><strong>Phone:</strong> ${order.phone}</p>
                                        <p><strong>Shipping Type:</strong> ${order.shipping_type}</p>
                                        <p><strong>Payment Type:</strong> ${order.payment_type}</p>
                                        <p><strong>Billing Address 1:</strong> ${order.billing_address1}</p>
                                        <p><strong>Billing Address 2:</strong> ${order.billing_address2}</p>
                                        <p><strong>Total Amount:</strong> S$${parseFloat(order.total).toFixed(2)}</p>
                                    </div>
                                </div>

                                <!-- Right side: Cart Summary -->
                                <div class="cart-summary">
                                    <h2>Order Summary</h2>
                                    ${items.map(item => `
                                        <div class="cart-item">
                                            <img src="${item.image_url}" alt="${item.product_name}" class="cart-thumbnail">
                                            <div class="cart-item-details">
                                                <h3>${item.product_name}</h3>
                                                ${item.color ? `<p><strong>Color: </strong>${item.color}</p>` : ''}
                                                ${item.storage ? `<p><strong>Storage: </strong>${item.storage}</p>` : ''}
                                                <p><strong>Unit Price: </strong>$${parseFloat(item.price).toFixed(2)}</p>
                                                <p><strong>Quantity: </strong>${item.quantity}</p>
                                                <p><strong>Total: </strong>$${(item.price * item.quantity).toFixed(2)}</p>
                                            </div>
                                        </div>
                                    `).join('')}
                                    <p><strong>Total Order Price:</strong> $${parseFloat(order.total).toFixed(2)}</p>
                                </div>
                            </div>
                        `;
                    } else {
                        // Display error message with additional details for debugging
                        profileContent.innerHTML = `<p>No order details available or an error occurred. Response data: ${JSON.stringify(data)}</p>`;
                    }
                })
                .catch(error => {
                    console.error('Error loading order details:', error);
                    const profileContent = document.querySelector('.profile-content');
                    profileContent.innerHTML = `<p>Failed to load order details: ${error.message}</p>`;
                });
        }

        function showAddresses() {
            fetch('fetch_addresses.php')
                .then(response => response.json())
                .then(data => {
                    console.log(data)
                    const profileContent = document.querySelector('.profile-content');
                    profileContent.innerHTML = '<h2>Saved Addresses</h2><div class="address-grid"></div>';
                    const grid = profileContent.querySelector('.address-grid');
                    data.forEach(address => {
                        const isDefault = address.is_default === 1; // Ensuring comparison is for a numeric value
                        let buttonsHtml = isDefault ?
                            '<button class="disabled" disabled>Default Address Set</button>' : // Button disabled if is_default is true
                            `<button onclick="setDefaultAddress(${address.id})">Set as Default</button>`; // Button active otherwise
                        grid.innerHTML += `
                            <div class="address-card">
                                <p><strong>Address line 1: </strong> ${address.address_line1}</p>
                                <p><strong>Address line 2: </strong>${address.address_line2}</p>
                                <p><strong>Postal Code: </strong>${address.postal_code}</p>
                                <p><strong>Phone number: </strong>${address.phone_number}</p>
                                <p><strong>City: </strong>${address.city}</p>
                                <p><strong>Country: </strong>${address.country}</p>
                                ${buttonsHtml}
                                <button onclick="deleteAddress(${address.id})">Delete</button>
                            </div>
                        `;
                    });
                    profileContent.innerHTML += `
                        <div class="address-button">
                        <button onclick="addAddress()" style="margin-top: 20px;">Add New Address</button>'
                        </div>
                        `;

                })
                .catch(error => console.error('Error loading addresses:', error));
        }


        function addAddress() {
            const profileContent = document.querySelector('.profile-content');
            profileContent.innerHTML = `
                <form id="add-address-form" class="profile-form">
                    <h2>Add New Address</h2>
                    <label for="address_line1">Address Line 1:</label>
                    <input type="text" id="address_line1" name="address_line1" placeholder="Address Line 1" required>
                    <label for="address_line2">Address Line 2:</label>
                    <input type="text" id="address_line2" name="address_line2" placeholder="Address Line 2">
                    <label for="postal_code">Postal Code:</label>
                    <input type="text" id="postal_code" name="postal_code" placeholder="Postal Code" required>
                    <label for="phone_number">Phone Number:</label>
                    <input type="text" id="phone_number" name="phone_number" placeholder="Phone Number" required>
                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" placeholder="City" required>
                    <label for="country">Country:</label>
                    <input type="text" id="country" name="country" placeholder="Country" required>
                    <button type="button" onclick="submitNewAddress()" style="margin-top:10px">Submit Address</button>
                    <button type="button" onclick="showPersonalInfo()" style="margin-top:10px">Cancel</button>
                </form>
            `;
        }

        function submitNewAddress() {
            const form = document.getElementById('add-address-form');
            if (form) {
                const formData = new FormData(form);

                formData.forEach((value, key) => console.log(key + ': ' + value));

                fetch('add_address.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Address added successfully!');
                        showAddresses();  // Refresh the address list
                    } else {
                        alert('Error adding address: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Failed to add address:', error);
                    alert('Failed to process the request.');
                });
            } else {
                alert('Form not found');
            }
        }

        function deleteAddress(addressId) {
            fetch('delete_address.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${addressId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAddresses(); // Reload the addresses
                } else {
                    alert('Failed to delete address.');
                }
            });
        }

        function setDefaultAddress(addressId) {
            fetch('set_default_address.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${addressId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Default address set successfully!');
                    showAddresses();  // Refresh the address list to show the updated default
                } else {
                    alert('Failed to set default address.');
                }
            });
        }
        </script>

</body>
</html>