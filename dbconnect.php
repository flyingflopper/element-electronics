<?php
// Establish database connection
@$dbcnx = new mysqli('localhost', 'root', '', 'element_electronics');
if ($dbcnx->connect_error) {
    die("Database connection failed: " . $dbcnx->connect_error);
}

// Select the database
if (!$dbcnx->select_db("element_electronics")) {
    die("Database selection failed: " . $dbcnx->error);
}

// Function to handle the creation of necessary tables
function createTables($db) {
    $queries = [
        // Products table
        "CREATE TABLE IF NOT EXISTS products (
            product_id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            category VARCHAR(100) NOT NULL,
            brand VARCHAR(100) NOT NULL,
            image_url VARCHAR(255) NOT NULL,
            stock INT NOT NULL,
            thumbnail_urls TEXT DEFAULT NULL,
            color_options VARCHAR(255) DEFAULT NULL,
            storage_options VARCHAR(255) DEFAULT NULL,
            featured_until TIMESTAMP NULL
        )",
        // Users table
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            avatar_path VARCHAR(255),
            gender VARCHAR(10),
            birthday DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        // Orders table
        "CREATE TABLE IF NOT EXISTS orders (
            order_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            country VARCHAR(255) NOT NULL,
            address1 VARCHAR(255) NOT NULL,
            address2 VARCHAR(255),
            postal_code VARCHAR(20) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            shipping_type VARCHAR(255) NOT NULL,
            payment_type VARCHAR(255) NOT NULL,
            billing_address1 VARCHAR(255) NOT NULL,
            billing_address2 VARCHAR(255),
            total DECIMAL(10, 2) NOT NULL,
            order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status VARCHAR(50) DEFAULT 'pending',
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )",
        // Carts table
        "CREATE TABLE IF NOT EXISTS carts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            price DECIMAL(10, 2),
            color VARCHAR(50) DEFAULT NULL,
            storage VARCHAR(50) DEFAULT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
        )",
        // Addresses table
        "CREATE TABLE IF NOT EXISTS addresses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            address_line1 VARCHAR(255) NOT NULL,
            address_line2 VARCHAR(255),
            postal_code VARCHAR(20) NOT NULL,
            phone_number VARCHAR(20),
            city VARCHAR(100) NOT NULL,
            country VARCHAR(100) NOT NULL,
            is_default BOOLEAN NOT NULL DEFAULT FALSE,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )",
        // Contact Messages table
        "CREATE TABLE IF NOT EXISTS contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        // Order Items table
        "CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            color VARCHAR(50) DEFAULT NULL,
            storage VARCHAR(50) DEFAULT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
        )",
        //Subscribe newsletter table
        "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            status VARCHAR(20) DEFAULT 'subscribed'
        )"      
    ];

    // Execute each table creation query
    foreach ($queries as $query) {
        if (!$db->query($query)) {
            error_log("Error creating table: " . $db->error);
        }
    }
}

// Call the function to ensure tables exist
createTables($dbcnx);

// Optionally load initial data into tables, ensuring no output
include_once 'initialize_data.php'; // Adjust this to your needs
?>
