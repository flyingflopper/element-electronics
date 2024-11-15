-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2024 at 10:12 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `element_electronics`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `postal_code` varchar(20) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `address_line1`, `address_line2`, `postal_code`, `phone_number`, `city`, `country`, `is_default`) VALUES
(1, 1, 'asfgdsvx', 'sad', '129292', '72626222', 'Singapore', 'singapore', 1);

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `storage` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `submitted_at`) VALUES
(1, 'sadads', 'abc@gmail.com', 'sad', 'asdfas', '2024-11-11 23:13:38'),
(2, 'TEST', 'TEST124@gmail.com', 'TEST', 'TESTTT', '2024-11-15 09:04:48');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subscribed_at` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'subscribed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `newsletter_subscribers`
--

INSERT INTO `newsletter_subscribers` (`id`, `email`, `subscribed_at`, `status`) VALUES
(1, 'sad@asd.com', '2024-11-13 08:12:06', 'subscribed'),
(2, 'sad@asdsdad.sadad', '2024-11-13 08:13:41', 'subscribed'),
(3, 'sadasd@dsasad.com', '2024-11-13 08:18:47', 'subscribed'),
(6, 'sad@asd.cooo', '2024-11-15 17:04:27', 'subscribed');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `address1` varchar(255) NOT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `postal_code` varchar(20) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `shipping_type` varchar(255) NOT NULL,
  `payment_type` varchar(255) NOT NULL,
  `billing_address1` varchar(255) NOT NULL,
  `billing_address2` varchar(255) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `name`, `country`, `address1`, `address2`, `postal_code`, `phone`, `shipping_type`, `payment_type`, `billing_address1`, `billing_address2`, `total`, `order_date`, `status`) VALUES
(16, 1, 'abcd', 'Singapore', 'asfgdsvx', 'sad', '129292', '72626222', 'dasjd', 'Credit Card', 'asfgdsvx', 'sad', 17438.99, '2024-11-15 08:55:17', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `storage` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `color`, `storage`) VALUES
(20, 16, 1, 1, 1799.00, 'Gold', '2TB'),
(21, 16, 3, 10, 1499.00, 'Black', '1TB'),
(22, 16, 49, 1, 649.99, 'Default', 'Default');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(100) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `stock` int(11) NOT NULL,
  `thumbnail_urls` text DEFAULT NULL,
  `color_options` varchar(255) DEFAULT NULL,
  `storage_options` varchar(255) DEFAULT NULL,
  `featured_until` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `category`, `brand`, `image_url`, `stock`, `thumbnail_urls`, `color_options`, `storage_options`, `featured_until`) VALUES
(1, 'Macbook Air 13\"', 'Lightweight and powerful laptop', 1399.00, 'Laptops', 'Apple', 'images/macbookair.png', 10, 'images/macbookair.png, images/macbookair2.jpg', NULL, NULL, NULL),
(2, 'iPhone 16 Pro', 'The latest smartphone from Apple', 1599.00, 'Phones', 'Apple', 'images/iphone16pro.png', 15, 'images/iphone16pro.png', NULL, NULL, NULL),
(3, 'Dell XPS 13', 'Compact and powerful laptop', 1299.00, 'Laptops', 'Dell', 'images/xps13_1.avif', 8, 'images/xps13_1.avif, images/xps13_2.avif', NULL, NULL, NULL),
(4, 'Samsung Galaxy S21', 'High-end smartphone with advanced camera', 999.00, 'Phones', 'Samsung', 'images/s21_1.jpg', 20, 'images/s21_1.jpg, images/s21_2.jpg', NULL, NULL, NULL),
(5, 'HP Spectre x360', 'Versatile 2-in-1 laptop with touch screen', 1150.00, 'Laptops', 'HP', 'images/hp_spectre_1.avif', 12, 'images/hp_spectre_1.avif, images/hp_spectre_2.avif', NULL, NULL, NULL),
(6, 'Canon EOS R6', 'Full-frame mirrorless camera for pros', 2499.00, 'Cameras', 'Canon', 'images/canon_r6_1.png', 9, 'images/canon_r6_1.png, images/canon_r6_2.png', NULL, NULL, NULL),
(7, 'Apple Watch Series 6', 'Smartwatch with ECG monitor', 399.00, 'Wearables', 'Apple', 'images/apple_watch_series6_1.jpg', 30, 'images/apple_watch_series6_1.jpg, images/apple_watch_series6_2.jpg', NULL, NULL, NULL),
(8, 'JBL Flip 5', 'Portable Bluetooth speaker', 119.95, 'Audio Equipment', 'JBL', 'images/flip5_1.webp', 35, 'images/flip5_1.webp, images/flip5_2.webp', NULL, NULL, NULL),
(9, 'Nintendo Switch', 'Portable gaming console', 299.00, 'Gaming Consoles', 'Nintendo', 'images/switch_1.avif', 25, 'images/switch_1.avif, images/switch_2.avif', NULL, NULL, NULL),
(10, 'Sony A7 III', 'Full-frame mirrorless camera', 1999.99, 'Cameras', 'Sony', 'images/sony_a7_1.webp', 15, 'images/sony_a7_1.webp, images/sony_a7_2.webp', NULL, NULL, NULL),
(11, 'Google Pixel 5', '5G smartphone with great battery life', 699.00, 'Phones', 'Google', 'images/pixel_5_1.webp', 20, 'images/pixel_5_1.webp, images/pixel_5_2.jpg', NULL, NULL, NULL),
(12, 'Microsoft Surface Book 3', 'Detachable 2-in-1 laptop', 1599.00, 'Laptops', 'Microsoft', 'images/surface_3_1.jpg', 10, 'images/surface_3_1.jpg, images/surface_3_2.jpg', NULL, NULL, NULL),
(13, 'Bose 700', 'Noise cancelling headphones', 379.00, 'Audio Equipment', 'Bose', 'images/bose_700_1.webp', 25, 'images/bose_700_1.webp, images/bose_700_2.webp', NULL, NULL, NULL),
(14, 'Xiaomi Mi Band 5', 'Budget fitness tracker', 35.99, 'Wearables', 'Xiaomi', 'images/miband_5_1.jpg', 40, 'images/miband_5_1.jpg, images/miband_5_2.jpg', NULL, NULL, NULL),
(15, 'Dyson Pure Cool', 'Air purifier and fan', 549.99, 'Home Appliances', 'Dyson', 'images/dyson_purecool_1.png', 15, 'images/dyson_purecool_1.png', NULL, NULL, NULL),
(16, 'Echo Show 8', 'Smart display with Alexa', 129.99, 'Smart Home Devices', 'Amazon', 'images/echo_show8_1.jpg', 20, 'images/echo_show8_1.jpg, images/echo_show8_2.jpg', NULL, NULL, NULL),
(17, 'LG C1 OLED TV', '55 inch 4K Smart TV', 1499.99, 'Televisions', 'LG', 'images/lg_c1_1.avif', 10, 'images/lg_c1_1.avif', NULL, NULL, NULL),
(18, 'GoPro HERO9', 'Waterproof action camera', 399.99, 'Cameras', 'GoPro', 'images/hero9_1.webp', 25, 'images/hero9_1.webp', NULL, NULL, NULL),
(19, 'Asus ROG Phone 5', 'Gaming smartphone with 144Hz display', 999.00, 'Phones', 'Asus', 'images/rog5_1.jpg', 15, 'images/rog5_1.jpg, images/rog5_2.jpg', NULL, NULL, '2024-11-15 20:46:14'),
(20, 'Fitbit Versa 3', 'Health & fitness smartwatch', 229.95, 'Wearables', 'Fitbit', 'images/versa3_1.jpg', 20, 'images/versa3_1.jpg, images/versa3_2.jpg', NULL, NULL, NULL),
(21, 'Razer Blade 15', 'Advanced gaming laptop with RTX 3080', 2199.00, 'Laptops', 'Razer', 'images/razer15_1.webp', 12, 'images/razer15_1.webp, images/razer15_2.png', NULL, NULL, NULL),
(22, 'Samsung Galaxy Tab S7', 'High-performance Android tablet', 649.99, 'Tablets', 'Samsung', 'images/galaxytabs7_1.jpg', 18, 'images/galaxytabs7_1.jpg, images/galaxytabs7_2.jpg', NULL, NULL, NULL),
(23, 'Asus ZenBook Duo', 'Laptop with dual screens for multitasking', 1999.00, 'Laptops', 'Asus', 'images/zenbookduo_1.jpg', 10, 'images/zenbookduo_1.jpg, images/zenbookduo_2.jpg', NULL, NULL, NULL),
(24, 'Oculus Quest 2', 'All-in-one VR headset', 299.00, 'VR Devices', 'Facebook', 'images/oculus_1.jpg', 25, 'images/oculus_1.jpg, images/oculus_2.webp', NULL, NULL, NULL),
(25, 'Philips Hue Go', 'Portable smart light', 79.99, 'Smart Home Devices', 'Philips', 'images/hueGo_1.jpg', 30, 'images/hueGo_1.jpg, images/hueGo_2.jpg', NULL, NULL, NULL),
(26, 'Logitech G Pro X', 'Wireless gaming headset', 199.99, 'Audio Equipment', 'Logitech', 'images/gproX_1.jpg', 20, 'images/gproX_1.jpg, images/gproX_2.jpg', NULL, NULL, NULL),
(27, 'Apple MacBook Pro 16\"', 'Professional laptop with M1 Max', 2799.00, 'Laptops', 'Apple', 'images/mcbookPro16_1.jpg', 10, 'images/mcbookPro16_1.jpg, images/mcbookPro16_2.jpg', NULL, NULL, NULL),
(28, 'Dell Alienware Monitor', '34 inch curved gaming monitor', 1029.99, 'Monitors', 'Dell', 'images/alienwareMonitor_1.jpg', 15, 'images/alienwareMonitor_1.jpg, images/alienwareMonitor_2.jpg', NULL, NULL, NULL),
(29, 'Sony Xperia 1 III', 'Smartphone with 4K HDR display', 1299.99, 'Phones', 'Sony', 'images/xperia1_1.jpg', 12, 'images/xperia1_1.jpg, images/xperia1_2.avif', NULL, NULL, NULL),
(30, 'Lenovo Legion 5', 'Powerful gaming laptop', 999.99, 'Laptops', 'Lenovo', 'images/legion5_1.jpg', 18, 'images/legion5_1.jpg', NULL, NULL, NULL),
(31, 'Xiaomi Mi 11', 'High-end smartphone with 108MP camera', 749.99, 'Phones', 'Xiaomi', 'images/mi11_1.jpg', 20, 'images/mi11_1.jpg, images/mi11_2.jpg', NULL, NULL, '2024-11-15 20:46:14'),
(32, 'Samsung The Frame TV', 'Artistic TV that blends into your decor', 1999.99, 'Televisions', 'Samsung', 'images/frameTv_1.webp', 10, 'images/frameTv_1.webp, images/frameTv_2.webp', NULL, NULL, '2024-11-15 20:46:14'),
(33, 'Apple AirPods Max', 'High-fidelity audio headphones', 549.00, 'Audio Equipment', 'Apple', 'images/airpodsMax_1.jpg', 25, 'images/airpodsMax_1.jpg, images/airpodsMax_2.webp', NULL, NULL, NULL),
(34, 'Nest Thermostat', 'Smart thermostat for home automation', 129.99, 'Smart Home Devices', 'Google', 'images/nestThermo_1.jpg', 20, 'images/nestThermo_1.jpg', NULL, NULL, NULL),
(35, 'HP Omen 15', 'Gaming laptop with high refresh rate display', 1099.99, 'Laptops', 'HP', 'images/omen15_1.png', 12, 'images/omen15_1.png, images/omen15_2.png', NULL, NULL, NULL),
(36, 'Canon PowerShot G7X', 'Compact vlogging camera', 749.00, 'Cameras', 'Canon', 'images/powershot_1.png', 15, 'images/powershot_1.png, images/powershot_2.png', NULL, NULL, NULL),
(37, 'Netgear Nighthawk Router', 'High-speed WiFi router', 399.99, 'Networking Devices', 'Netgear', 'images/nighthawk_1.jpg', 25, 'images/nighthawk_1.jpg', NULL, NULL, NULL),
(38, 'Microsoft Xbox Series S', 'Next-gen gaming console', 299.99, 'Gaming Consoles', 'Microsoft', 'images/xboxSeriesS_1.jpg', 30, 'images/xboxSeriesS_1.jpg, images/xboxSeriesS_2.jpg', NULL, NULL, NULL),
(39, 'Huawei Watch GT 2', 'Fitness and wellness smartwatch', 229.99, 'Wearables', 'Huawei', 'images/huaweiWatchGT_1.jpg', 20, 'images/huaweiWatchGT_1.jpg, images/huaweiWatchGT_2.jpg', NULL, NULL, '2024-11-15 20:46:14'),
(40, 'LIFX Smart Bulb', 'Color LED Wi-Fi light bulb', 49.99, 'Smart Home Devices', 'LIFX', 'images/smartBulb_1.webp', 35, 'images/smartBulb_1.webp, images/smartBulb_2.jpg', NULL, NULL, '2024-11-15 20:46:14'),
(41, 'Panasonic Lumix GH5', 'Mirrorless camera for videographers', 1299.99, 'Cameras', 'Panasonic', 'images/lumixGH5_1.jpg', 15, 'images/lumixGH5_1.jpg, images/lumixGH5_2.png', NULL, NULL, NULL),
(42, 'Marshall Stanmore II', 'Wireless Bluetooth speaker', 349.99, 'Audio Equipment', 'Marshall', 'images/marshall_stanmore_1.jpg', 20, 'images/marshall_stanmore_1.jpg, images/marshall_stanmore_2.jpg', NULL, NULL, NULL),
(43, 'Garmin Fenix 6', 'Multisport GPS watch designed for athletes', 599.99, 'Wearables', 'Garmin', 'images/garminFenix6_1.png', 20, 'images/garminFenix6_1.png, images/garminFenix6_2.png', NULL, NULL, NULL),
(44, 'LG Gram 17', 'Ultra-lightweight laptop with a 17-inch screen', 1249.99, 'Laptops', 'LG', 'images/lgGram_1.avif', 12, 'images/lgGram_1.avif, images/lgGram_2.jpg', NULL, NULL, NULL),
(45, 'Samsung Galaxy Buds Pro', 'Intelligent active noise-cancelling earbuds', 199.99, 'Audio Equipment', 'Samsung', 'images/galaxyBudsPro_1.avif', 25, 'images/galaxyBudsPro_1.avif, images/galaxyBudsPro_2.webp', NULL, NULL, NULL),
(46, 'Yamaha YAS-209', 'Sound bar with wireless subwoofer and Alexa built-in', 349.99, 'Home Audio', 'Yamaha', 'images/yamaha_1.avif', 15, 'images/yamaha_1.avif, images/yamaha_2.avif', NULL, NULL, NULL),
(47, 'Google Nest Hub Max', 'Smart home controller with a 10-inch screen', 229.99, 'Smart Home Devices', 'Google', 'images/nestHubMax_1.jpg', 18, 'images/nestHubMax_1.jpg, images/nestHubMax_2.jpg', NULL, NULL, NULL),
(48, 'Alienware Aurora R12', 'High-performance gaming desktop with RTX 3070', 1849.99, 'Desktop Computers', 'Alienware', 'images/auroraR12_1.jpg', 10, 'images/auroraR12_1.jpg, images/auroraR12_2.jpg', NULL, NULL, NULL),
(49, 'TCL 6-Series R635', '55-inch 4K QLED Roku Smart TV', 649.99, 'Televisions', 'TCL', 'images/tcl6_1.jpg', 15, 'images/tcl6_1.jpg, images/tcl6_2.jpg', NULL, NULL, '2024-11-15 20:46:14'),
(50, 'Fitbit Charge 5', 'Advanced fitness tracker with built-in GPS', 179.95, 'Wearables', 'Fitbit', 'images/fitbitCharge5_1.jpg', 30, 'images/fitbitCharge5_1.jpg, images/fitbitCharge5_2.jpg', NULL, NULL, NULL),
(51, 'Nikon Z6 II', '24.5 MP full-frame mirrorless camera', 1999.95, 'Cameras', 'Nikon', 'images/nikonz62_1.jpeg', 10, 'images/nikonz62_1.jpeg, images/nikonz62_2.webp', NULL, NULL, NULL),
(52, 'Ultimate Ears WONDERBOOM 2', 'Portable waterproof Bluetooth speaker', 99.99, 'Audio Equipment', 'Ultimate Ears', 'images/wonderboom2_1.jpg', 20, 'images/wonderboom2_1.jpg, images/wonderboom2_2.jpg', NULL, NULL, NULL),
(53, 'Razer Huntsman Elite', 'Gaming keyboard with optical switches', 199.99, 'Computer Accessories', 'Razer', 'images/razerHuntsmanElite_1.jpg', 15, 'images/razerHuntsmanElite_1.jpg, images/razerHuntsmanElite_2.webp', NULL, NULL, NULL),
(54, 'iPad Mini 6', 'Compact tablet with A15 Bionic chip', 499.00, 'Tablets', 'Apple', 'images/ipadMini6_1.jpg', 20, 'images/ipadMini6_1.jpg, images/ipadMini6_2.jpg', NULL, NULL, NULL),
(55, 'Asus TUF Gaming VG27AQ', '27-inch gaming monitor', 329.99, 'Monitors', 'Asus', 'images/asusTUF_1.png', 18, 'images/asusTUF_1.png, images/asusTUF_2.jpg', NULL, NULL, NULL),
(56, 'Corsair Scimitar RGB Elite', 'Gaming mouse with 17 programmable buttons', 79.99, 'Computer Accessories', 'Corsair', 'images/corsairScimitar_1.jpg', 25, 'images/corsairScimitar_1.jpg, images/corsairScimitar_2.jpg', NULL, NULL, NULL),
(57, 'Amazon Fire TV Stick 4K', 'Streaming device with 4K Ultra HD', 49.99, 'Media Devices', 'Amazon', 'images/fireStick_1.jpg', 40, 'images/fireStick_1.jpg, images/fireStick_2.jpg', NULL, NULL, NULL),
(58, 'Sonos Arc', 'Premium smart soundbar', 899.99, 'Audio Equipment', 'Sonos', 'images/sonosArc_1.jpg', 15, 'images/sonosArc_1.jpg, images/sonosArc_2.webp', NULL, NULL, NULL),
(59, 'DJI Phantom 4 Pro V2.0', 'Professional drone with 4K camera', 1599.00, 'Drones', 'DJI', 'images/phantom4Pro_1.webp', 12, 'images/phantom4Pro_1.webp, images/phantom4Pro_2.jpg', NULL, NULL, NULL),
(60, 'Apple iMac 24\"', 'All-in-one desktop with M1 chip', 1299.00, 'Desktop Computers', 'Apple', 'images/iMac_1.jpg', 10, 'images/iMac_1.jpg, images/iMac_2.jpg', NULL, NULL, NULL),
(61, 'Google Pixel Buds A-Series', 'Wireless earbuds with rich sound', 99.99, 'Audio Equipment', 'Google', 'images/pixelBudsA_1.webp', 25, 'images/pixelBudsA_1.webp, images/pixelBudsA_2.jpg', NULL, NULL, '2024-11-15 20:46:14'),
(62, 'Sony WH-1000XM4', 'Industry-leading noise cancelling headphones', 348.00, 'Audio Equipment', 'Sony', 'images/sony1000XM4_1.avif', 20, 'images/sony1000XM4_1.avif, images/sony1000XM4_2.jpg', NULL, NULL, NULL),
(63, 'Anker PowerCore 10000', 'Compact 10000mAh portable charger', 24.99, 'Mobile Accessories', 'Anker', 'images/ankerPowerCore_1.webp', 50, 'images/ankerPowerCore_1.webp, images/ankerPowerCore_2.webp', NULL, NULL, NULL),
(64, 'Theragun Pro', 'Professional-grade percussive therapy device', 599.00, 'Health & Wellness', 'Therabody', 'images/theragunPro_1.jpg', 15, 'images/theragunPro_1.jpg, images/theragunPro_2.webp', NULL, NULL, NULL),
(65, 'Bang & Olufsen Beoplay H9i', 'Luxury over-ear headphones with ANC', 499.00, 'Audio Equipment', 'Bang & Olufsen', 'images/beoplayH9i_1.jpg', 12, 'images/beoplayH9i_1.jpg, images/beoplayH9i_2.jpg', NULL, NULL, '2024-11-15 20:46:14'),
(66, 'Epson EcoTank ET-3760', 'Cartridge-free printing with easy-to-fill supersized ink tanks', 399.99, 'Printers', 'Epson', 'images/epsonET_1.png', 15, 'images/epsonET_1.png, images/epsonET_2.jpg', NULL, NULL, NULL),
(67, 'Garmin Venu 2', 'GPS smartwatch with advanced health monitoring', 349.99, 'Wearables', 'Garmin', 'images/garminVenu_1.webp', 18, 'images/garminVenu_1.webp, images/garminVenu_2.webp', NULL, NULL, NULL),
(68, 'Microsoft Surface Duo 2', 'Dual-screen mobile productivity device', 1499.00, 'Phones', 'Microsoft', 'images/surfaceDuo2_1.jpg', 10, 'images/surfaceDuo2_1.jpg, images/surfaceDuo2_2.jpg', NULL, NULL, NULL),
(69, 'Samsung T7 Touch Portable SSD', 'Portable external solid state drive with fingerprint security', 189.99, 'Storage Devices', 'Samsung', 'images/samsungSSD_1.avif', 20, 'images/samsungSSD_1.avif, images/samsungSSD_2.png', NULL, NULL, NULL),
(70, 'Apple TV 4K', 'Streaming device that supports 4K HDR content', 179.00, 'Media Devices', 'Apple', 'images/appleTV_1.jpg', 25, 'images/appleTV_1.jpg, images/appleTV_2.jpg', NULL, NULL, NULL),
(71, 'Lenovo Smart Clock 2', 'Smart clock with Google Assistant', 69.99, 'Smart Home Devices', 'Lenovo', 'images/lenovoSmartClock_1.png', 30, 'images/lenovoSmartClock_1.png, images/lenovoSmartClock_2.webp', NULL, NULL, NULL),
(72, 'Asus ROG Zephyrus G14', 'Compact gaming laptop with powerful specs', 1449.00, 'Laptops', 'Asus', 'images/asusZephyrus_1.png', 10, 'images/asusZephyrus_1.png, images/asusZephyrus_2.jpg', NULL, NULL, NULL),
(73, 'Panasonic NN-SN686S', 'Stainless steel microwave with Inverter technology', 149.95, 'Kitchen Appliances', 'Panasonic', 'images/panasonicMicro_1.webp', 20, 'images/panasonicMicro_1.webp, images/panasonicMicro_2.jpg', NULL, NULL, NULL),
(74, 'Jabra Elite 85t', 'Advanced active noise-cancelling earbuds', 229.99, 'Audio Equipment', 'Jabra', 'images/jabraElite85t_1.jpg', 25, 'images/jabraElite85t_1.jpg, images/jabraElite85t_2.jpg', NULL, NULL, NULL),
(75, 'Kindle Paperwhite', 'Waterproof e-reader with adjustable warm light', 129.99, 'E-readers', 'Amazon', 'images/kindlePaperWhite_1.jpg', 30, 'images/kindlePaperWhite_1.jpg, images/kindlePaperWhite_2.webp', NULL, NULL, NULL),
(76, 'Roborock S6 Pure', 'Robot vacuum cleaner and mop', 459.99, 'Home Appliances', 'Roborock', 'images/roborock_1.webp', 15, 'images/roborock_1.webp, images/roborock_2.webp', NULL, NULL, NULL),
(77, 'OnePlus 9 Pro', 'Smartphone with Hasselblad camera for mobile', 1069.00, 'Phones', 'OnePlus', 'images/onePlus9Pro_1.png', 20, 'images/onePlus9Pro_1.png, images/onePlus9Pro_2.webp', NULL, NULL, NULL),
(78, 'Nvidia Shield TV Pro', 'Streaming media player with advanced Dolby Vision', 199.99, 'Media Devices', 'Nvidia', 'images/shieldTV_1.jpg', 18, 'images/shieldTV_1.jpg, images/shieldTV_2.jpg', NULL, NULL, NULL),
(79, 'Philips SmartSleep Wake-up Light', 'Light therapy alarm clock', 79.95, 'Health & Wellness', 'Philips', 'images/smartSleep_1.jpg', 20, 'images/smartSleep_1.jpg, images/smartSleep_2.jpg', NULL, NULL, NULL),
(80, 'Yamaha PSR-E373', '61-key portable keyboard', 199.99, 'Musical Instruments', 'Yamaha', 'images/yamahaPiano_1.jpg', 10, 'images/yamahaPiano_1.jpg, images/yamahaPiano_2.avif', NULL, NULL, NULL),
(81, 'Ring Video Doorbell 4', 'HD video doorbell with enhanced wifi', 199.99, 'Smart Home Devices', 'Ring', 'images/ring_1.jpg', 25, 'images/ring_1.jpg, images/ring_2.jpg', NULL, NULL, NULL),
(82, 'Olympus Tough TG-6', 'Waterproof, dustproof, shockproof, crushproof camera', 449.99, 'Cameras', 'Olympus', 'images/olympus_1.jpg', 15, 'images/olympus_1.jpg, images/olympus_2.jpg', NULL, NULL, NULL),
(83, 'Celestron NexStar 5SE', 'Computerized telescope for beginners and advanced users', 699.00, 'Outdoor Gear', 'Celestron', 'images/nexStar_1.png', 12, 'images/nexStar_1.png, images/nexStar_2.webp', NULL, NULL, NULL),
(84, 'Ultimate Ears Hyperboom', 'Portable & powerful wireless Bluetooth speaker', 399.99, 'Audio Equipment', 'Ultimate Ears', 'images/hyperboom_1.png', 10, 'images/hyperboom_1.png, images/hyperboom_2.webp', NULL, NULL, NULL),
(85, 'DJI Osmo Mobile 4', 'Foldable smartphone gimbal for smooth video', 149.00, 'Mobile Accessories', 'DJI', 'images/osmoMobile_1.png', 20, 'images/osmoMobile_1.png, images/osmoMobile_2.jpg', NULL, NULL, NULL),
(86, 'Logitech C920', 'HD Pro Webcam for streaming and video calls', 79.99, 'Computer Accessories', 'Logitech', 'images/logitechC920_1.webp', 35, 'images/logitechC920_1.webp, images/logitechC920_2.webp', NULL, NULL, NULL),
(87, 'GoPro Max', '360-degree action camera', 499.99, 'Cameras', 'GoPro', 'images/goproMax_1.png', 18, 'images/goproMax_1.png, images/goproMax_2.jpg', NULL, NULL, NULL),
(88, 'iPad Pro', 'Latest model with M2 chip and Liquid Retina display', 1099.00, 'Tablets', 'Apple', 'images/ipadpro.png', 25, 'images/ipadpro.png', NULL, NULL, NULL),
(89, 'Apple Watch Ultra 2', 'Advanced smartwatch with enhanced durability and tracking features', 799.00, 'Wearables', 'Apple', 'images/watchultra2.png', 20, 'images/watchultra2.png', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `avatar_path` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `avatar_path`, `gender`, `birthday`, `created_at`) VALUES
(1, 'abcd', '$2y$10$WvSBsYIEmiTgnt11/3.HOu/d1eG7Qho6I0.10o4OnPyIUlDFoFIBq', 'abcd@gmail.com', 'uploads/6730a296d0cf8-about-logo.png', 'female', '2024-11-14', '2024-11-10 12:04:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
