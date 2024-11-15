<?php
// Function to check and insert initial data
function initializeData($db) {
    // Check if products table has any rows
    $result = $db->query("SELECT COUNT(*) as count FROM products");
    $row = $result->fetch_assoc();
    if ($row['count'] == 0) { // Adjust this condition based on actual checks
        // Define your initial products or other data here
        $insertProductsQuery = "
        INSERT INTO products (name, description, price, category, brand, image_url, stock) VALUES
        ('Macbook Air 13\"', 'Lightweight and powerful laptop', 1399.00, 'Laptops', 'Apple', 'images/macbookair.png', 10),
        ('iPhone 16 Pro', 'The latest smartphone from Apple', 1599.00, 'Phones', 'Apple', 'images/iphone16pro.png', 15),
        ('Dell XPS 13', 'Compact and powerful laptop', 1299.00, 'Laptops', 'Dell', 'images/xps13_1.avif', 8),
        ('Samsung Galaxy S21', 'High-end smartphone with advanced camera', 999.00, 'Phones', 'Samsung', 'images/s21_1.jpg', 20),
        ('HP Spectre x360', 'Versatile 2-in-1 laptop with touch screen', 1150.00, 'Laptops', 'HP', 'images/hp_spectre_1.avif', 12),
        ('Canon EOS R6', 'Full-frame mirrorless camera for pros', 2499.00, 'Cameras', 'Canon', 'images/canon_r6_1.png', 9),
        ('Apple Watch Series 6', 'Smartwatch with ECG monitor', 399.00, 'Wearables', 'Apple', 'images/apple_watch_series6_1.jpg', 30),
        ('JBL Flip 5', 'Portable Bluetooth speaker', 119.95, 'Audio Equipment', 'JBL', 'images/flip5_1.webp', 35),
        ('Nintendo Switch', 'Portable gaming console', 299.00, 'Gaming Consoles', 'Nintendo', 'images/switch_1.avif', 25),
        ('Sony A7 III', 'Full-frame mirrorless camera', 1999.99, 'Cameras', 'Sony', 'images/sony_a7_1.webp', 15),
        ('Google Pixel 5', '5G smartphone with great battery life', 699.00, 'Phones', 'Google', 'images/pixel_5_1.webp', 20),
        ('Microsoft Surface Book 3', 'Detachable 2-in-1 laptop', 1599.00, 'Laptops', 'Microsoft', 'images/surface_3_1.jpg', 10),
        ('Bose 700', 'Noise cancelling headphones', 379.00, 'Audio Equipment', 'Bose', 'images/bose_700_1.webp', 25),
        ('Xiaomi Mi Band 5', 'Budget fitness tracker', 35.99, 'Wearables', 'Xiaomi', 'images/miband_5_1.jpg', 40),
        ('Dyson Pure Cool', 'Air purifier and fan', 549.99, 'Home Appliances', 'Dyson', 'images/dyson_purecool_1.png', 15),
        ('Echo Show 8', 'Smart display with Alexa', 129.99, 'Smart Home Devices', 'Amazon', 'images/echo_show8_1.jpg', 20),
        ('LG C1 OLED TV', '55 inch 4K Smart TV', 1499.99, 'Televisions', 'LG', 'images/lg_c1_1.avif', 10),
        ('GoPro HERO9', 'Waterproof action camera', 399.99, 'Cameras', 'GoPro', 'images/hero9_1.webp', 25),
        ('Asus ROG Phone 5', 'Gaming smartphone with 144Hz display', 999.00, 'Phones', 'Asus', 'images/rog5_1.jpg', 15),
        ('Fitbit Versa 3', 'Health & fitness smartwatch', 229.95, 'Wearables', 'Fitbit', 'images/versa3_1.jpg', 20),
        ('Razer Blade 15', 'Advanced gaming laptop with RTX 3080', 2199.00, 'Laptops', 'Razer', 'images/razer15_1.webp', 12),
        ('Samsung Galaxy Tab S7', 'High-performance Android tablet', 649.99, 'Tablets', 'Samsung', 'images/galaxytabs7_1.jpg', 18),
        ('Asus ZenBook Duo', 'Laptop with dual screens for multitasking', 1999.00, 'Laptops', 'Asus', 'images/zenbookduo_1.jpg', 10),
        ('Oculus Quest 2', 'All-in-one VR headset', 299.00, 'VR Devices', 'Facebook', 'images/oculus_1.jpg', 25),
        ('Philips Hue Go', 'Portable smart light', 79.99, 'Smart Home Devices', 'Philips', 'images/hueGo_1.jpg', 30),
        ('Logitech G Pro X', 'Wireless gaming headset', 199.99, 'Audio Equipment', 'Logitech', 'images/gproX_1.jpg', 20),
        ('Apple MacBook Pro 16\"', 'Professional laptop with M1 Max', 2799.00, 'Laptops', 'Apple', 'images/mcbookPro16_1.jpg', 10),
        ('Dell Alienware Monitor', '34 inch curved gaming monitor', 1029.99, 'Monitors', 'Dell', 'images/alienwareMonitor_1.jpg', 15),
        ('Sony Xperia 1 III', 'Smartphone with 4K HDR display', 1299.99, 'Phones', 'Sony', 'images/xperia1_1.jpg', 12),
        ('Lenovo Legion 5', 'Powerful gaming laptop', 999.99, 'Laptops', 'Lenovo', 'images/legion5_1.jpg', 18),
        ('Xiaomi Mi 11', 'High-end smartphone with 108MP camera', 749.99, 'Phones', 'Xiaomi', 'images/mi11_1.jpg', 20),
        ('Samsung The Frame TV', 'Artistic TV that blends into your decor', 1999.99, 'Televisions', 'Samsung', 'images/frameTv_1.webp', 10),
        ('Apple AirPods Max', 'High-fidelity audio headphones', 549.00, 'Audio Equipment', 'Apple', 'images/airpodsMax_1.jpg', 25),
        ('Nest Thermostat', 'Smart thermostat for home automation', 129.99, 'Smart Home Devices', 'Google', 'images/nestThermo_1.jpg', 20),
        ('HP Omen 15', 'Gaming laptop with high refresh rate display', 1099.99, 'Laptops', 'HP', 'images/omen15_1.png', 12),
        ('Canon PowerShot G7X', 'Compact vlogging camera', 749.00, 'Cameras', 'Canon', 'images/powershot_1.png', 15),
        ('Netgear Nighthawk Router', 'High-speed WiFi router', 399.99, 'Networking Devices', 'Netgear', 'images/nighthawk_1.jpg', 25),
        ('Microsoft Xbox Series S', 'Next-gen gaming console', 299.99, 'Gaming Consoles', 'Microsoft', 'images/xboxSeriesS_1.jpg', 30),
        ('Huawei Watch GT 2', 'Fitness and wellness smartwatch', 229.99, 'Wearables', 'Huawei', 'images/huaweiWatchGT_1.jpg', 20),
        ('LIFX Smart Bulb', 'Color LED Wi-Fi light bulb', 49.99, 'Smart Home Devices', 'LIFX', 'images/smartBulb_1.webp', 35),
        ('Panasonic Lumix GH5', 'Mirrorless camera for videographers', 1299.99, 'Cameras', 'Panasonic', 'images/lumixGH5_1.jpg', 15),
        ('Marshall Stanmore II', 'Wireless Bluetooth speaker', 349.99, 'Audio Equipment', 'Marshall', 'images/marshall_stanmore_1.jpg', 20),
        ('Garmin Fenix 6', 'Multisport GPS watch designed for athletes', 599.99, 'Wearables', 'Garmin', 'images/garminFenix6_1.png', 20),
        ('LG Gram 17', 'Ultra-lightweight laptop with a 17-inch screen', 1249.99, 'Laptops', 'LG', 'images/lgGram_1.avif', 12),
        ('Samsung Galaxy Buds Pro', 'Intelligent active noise-cancelling earbuds', 199.99, 'Audio Equipment', 'Samsung', 'images/galaxyBudsPro_1.avif', 25),
        ('Yamaha YAS-209', 'Sound bar with wireless subwoofer and Alexa built-in', 349.99, 'Home Audio', 'Yamaha', 'images/yamaha_1.avif', 15),
        ('Google Nest Hub Max', 'Smart home controller with a 10-inch screen', 229.99, 'Smart Home Devices', 'Google', 'images/nestHubMax_1.jpg', 18),
        ('Alienware Aurora R12', 'High-performance gaming desktop with RTX 3070', 1849.99, 'Desktop Computers', 'Alienware', 'images/auroraR12_1.jpg', 10),
        ('TCL 6-Series R635', '55-inch 4K QLED Roku Smart TV', 649.99, 'Televisions', 'TCL', 'images/tcl6_1.jpg', 15),
        ('Fitbit Charge 5', 'Advanced fitness tracker with built-in GPS', 179.95, 'Wearables', 'Fitbit', 'images/fitbitCharge5_1.jpg', 30),
        ('Nikon Z6 II', '24.5 MP full-frame mirrorless camera', 1999.95, 'Cameras', 'Nikon', 'images/nikonz62_1.jpeg', 10),
        ('Ultimate Ears WONDERBOOM 2', 'Portable waterproof Bluetooth speaker', 99.99, 'Audio Equipment', 'Ultimate Ears', 'images/wonderboom2_1.jpg', 20),
        ('Razer Huntsman Elite', 'Gaming keyboard with optical switches', 199.99, 'Computer Accessories', 'Razer', 'images/razerHuntsmanElite_1.jpg', 15),
        ('iPad Mini 6', 'Compact tablet with A15 Bionic chip', 499.00, 'Tablets', 'Apple', 'images/ipadMini6_1.jpg', 20),
        ('Asus TUF Gaming VG27AQ', '27-inch gaming monitor', 329.99, 'Monitors', 'Asus', 'images/asusTUF_1.png', 18),
        ('Corsair Scimitar RGB Elite', 'Gaming mouse with 17 programmable buttons', 79.99, 'Computer Accessories', 'Corsair', 'images/corsairScimitar_1.jpg', 25),
        ('Amazon Fire TV Stick 4K', 'Streaming device with 4K Ultra HD', 49.99, 'Media Devices', 'Amazon', 'images/fireStick_1.jpg', 40),
        ('Sonos Arc', 'Premium smart soundbar', 899.99, 'Audio Equipment', 'Sonos', 'images/sonosArc_1.jpg', 15),
        ('DJI Phantom 4 Pro V2.0', 'Professional drone with 4K camera', 1599.00, 'Drones', 'DJI', 'images/phantom4Pro_1.webp', 12),
        ('Apple iMac 24\"', 'All-in-one desktop with M1 chip', 1299.00, 'Desktop Computers', 'Apple', 'images/iMac_1.jpg', 10),
        ('Google Pixel Buds A-Series', 'Wireless earbuds with rich sound', 99.99, 'Audio Equipment', 'Google', 'images/pixelBudsA_1.webp', 25),
        ('Sony WH-1000XM4', 'Industry-leading noise cancelling headphones', 348.00, 'Audio Equipment', 'Sony', 'images/sony1000XM4_1.avif', 20),
        ('Anker PowerCore 10000', 'Compact 10000mAh portable charger', 24.99, 'Mobile Accessories', 'Anker', 'images/ankerPowerCore_1.webp', 50),
        ('Theragun Pro', 'Professional-grade percussive therapy device', 599.00, 'Health & Wellness', 'Therabody', 'images/theragunPro_1.jpg', 15),
        ('Bang & Olufsen Beoplay H9i', 'Luxury over-ear headphones with ANC', 499.00, 'Audio Equipment', 'Bang & Olufsen', 'images/beoplayH9i_1.jpg', 12),
        ('Epson EcoTank ET-3760', 'Cartridge-free printing with easy-to-fill supersized ink tanks', 399.99, 'Printers', 'Epson', 'images/epsonET_1.png', 15),
        ('Garmin Venu 2', 'GPS smartwatch with advanced health monitoring', 349.99, 'Wearables', 'Garmin', 'images/garminVenu_1.webp', 18),
        ('Microsoft Surface Duo 2', 'Dual-screen mobile productivity device', 1499.00, 'Phones', 'Microsoft', 'images/surfaceDuo2_1.jpg', 10),
        ('Samsung T7 Touch Portable SSD', 'Portable external solid state drive with fingerprint security', 189.99, 'Storage Devices', 'Samsung', 'images/samsungSSD_1.avif', 20),
        ('Apple TV 4K', 'Streaming device that supports 4K HDR content', 179.00, 'Media Devices', 'Apple', 'images/appleTV_1.jpg', 25),
        ('Lenovo Smart Clock 2', 'Smart clock with Google Assistant', 69.99, 'Smart Home Devices', 'Lenovo', 'images/lenovoSmartClock_1.png', 30),
        ('Asus ROG Zephyrus G14', 'Compact gaming laptop with powerful specs', 1449.00, 'Laptops', 'Asus', 'images/asusZephyrus_1.png', 10),
        ('Panasonic NN-SN686S', 'Stainless steel microwave with Inverter technology', 149.95, 'Kitchen Appliances', 'Panasonic', 'images/panasonicMicro_1.webp', 20),
        ('Jabra Elite 85t', 'Advanced active noise-cancelling earbuds', 229.99, 'Audio Equipment', 'Jabra', 'images/jabraElite85t_1.jpg', 25),
        ('Kindle Paperwhite', 'Waterproof e-reader with adjustable warm light', 129.99, 'E-readers', 'Amazon', 'images/kindlePaperWhite_1.jpg', 30),
        ('Roborock S6 Pure', 'Robot vacuum cleaner and mop', 459.99, 'Home Appliances', 'Roborock', 'images/roborock_1.webp', 15),
        ('OnePlus 9 Pro', 'Smartphone with Hasselblad camera for mobile', 1069.00, 'Phones', 'OnePlus', 'images/onePlus9Pro_1.png', 20),
        ('Nvidia Shield TV Pro', 'Streaming media player with advanced Dolby Vision', 199.99, 'Media Devices', 'Nvidia', 'images/shieldTV_1.jpg', 18),
        ('Philips SmartSleep Wake-up Light', 'Light therapy alarm clock', 79.95, 'Health & Wellness', 'Philips', 'images/smartSleep_1.jpg', 20),
        ('Yamaha PSR-E373', '61-key portable keyboard', 199.99, 'Musical Instruments', 'Yamaha', 'images/yamahaPiano_1.jpg', 10),
        ('Ring Video Doorbell 4', 'HD video doorbell with enhanced wifi', 199.99, 'Smart Home Devices', 'Ring', 'images/ring_1.jpg', 25),
        ('Olympus Tough TG-6', 'Waterproof, dustproof, shockproof, crushproof camera', 449.99, 'Cameras', 'Olympus', 'images/olympus_1.jpg', 15),
        ('Celestron NexStar 5SE', 'Computerized telescope for beginners and advanced users', 699.00, 'Outdoor Gear', 'Celestron', 'images/nexStar_1.png', 12),
        ('Ultimate Ears Hyperboom', 'Portable & powerful wireless Bluetooth speaker', 399.99, 'Audio Equipment', 'Ultimate Ears', 'images/hyperboom_1.png', 10),
        ('DJI Osmo Mobile 4', 'Foldable smartphone gimbal for smooth video', 149.00, 'Mobile Accessories', 'DJI', 'images/osmoMobile_1.png', 20),
        ('Logitech C920', 'HD Pro Webcam for streaming and video calls', 79.99, 'Computer Accessories', 'Logitech', 'images/logitechC920_1.webp', 35),
        ('GoPro Max', '360-degree action camera', 499.99, 'Cameras', 'GoPro', 'images/goproMax_1.png', 18),
        ('iPad Pro', 'Latest model with M2 chip and Liquid Retina display', 1099.00, 'Tablets', 'Apple', 'images/ipadpro.png', 25),
        ('Apple Watch Ultra 2', 'Advanced smartwatch with enhanced durability and tracking features', 799.00, 'Wearables', 'Apple', 'images/watchultra2.png', 20);

        "; // Add all your data

        if (!$db->query($insertProductsQuery)) {
            // Only log errors; do not output them directly
            error_log("Error inserting initial products: " . $db->error);
        } else {
            // Optionally log successful initialization
            error_log("Initial products inserted successfully.");
        }
    } else {
        // Data already exists, log this fact if you need to
        error_log("Initial data already exists, skipping insertion.");
    }
}

// Call the initialization function
initializeData($dbcnx);
?>
