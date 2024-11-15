<?php
// Include your database connection file
include('dbconnect.php');

// Define the thumbnail URLs for each product
// Use product_id as keys and comma-separated image paths as values
$thumbnailData = [
    1 => 'images/macbookair.png, images/macbookair2.jpg', // Thumbnails for product_id 1
    2 => 'images/iphone16pro.png',                       // Thumbnails for product_id 2
    3 => 'images/xps13_1.avif, images/xps13_2.avif',     // Thumbnails for product_id 3
    4 => 'images/s21_1.jpg, images/s21_2.jpg',           // Thumbnails for product_id 4
    5 => 'images/hp_spectre_1.avif, images/hp_spectre_2.avif', // Thumbnails for product_id 5
    6 => 'images/canon_r6_1.png, images/canon_r6_2.png', // Thumbnails for product_id 6
    7 => 'images/apple_watch_series6_1.jpg, images/apple_watch_series6_2.jpg', // Thumbnails for product_id 7
    8 => 'images/flip5_1.webp, images/flip5_2.webp',     // Thumbnails for product_id 8
    9 => 'images/switch_1.avif, images/switch_2.avif',   // Thumbnails for product_id 9
    10 => 'images/sony_a7_1.webp, images/sony_a7_2.webp', // Thumbnails for product_id 10
    11 => 'images/pixel_5_1.webp, images/pixel_5_2.jpg', // Thumbnails for product_id 11
    12 => 'images/surface_3_1.jpg, images/surface_3_2.jpg', // Thumbnails for product_id 12
    13 => 'images/bose_700_1.webp, images/bose_700_2.webp', // Thumbnails for product_id 13
    14 => 'images/miband_5_1.jpg, images/miband_5_2.jpg', // Thumbnails for product_id 14
    15 => 'images/dyson_purecool_1.png',                // Thumbnails for product_id 15
    16 => 'images/echo_show8_1.jpg, images/echo_show8_2.jpg', // Thumbnails for product_id 16
    17 => 'images/lg_c1_1.avif',                        // Thumbnails for product_id 17
    18 => 'images/hero9_1.webp',                        // Thumbnails for product_id 18
    19 => 'images/rog5_1.jpg, images/rog5_2.jpg',       // Thumbnails for product_id 19
    20 => 'images/versa3_1.jpg, images/versa3_2.jpg',   // Thumbnails for product_id 20
    21 => 'images/razer15_1.webp, images/razer15_2.png',       // Thumbnails for product_id 21
    22 => 'images/galaxytabs7_1.jpg, images/galaxytabs7_2.jpg', // Thumbnails for product_id 22
    23 => 'images/zenbookduo_1.jpg, images/zenbookduo_2.jpg',   // Thumbnails for product_id 23
    24 => 'images/oculus_1.jpg, images/oculus_2.webp',          // Thumbnails for product_id 24
    25 => 'images/hueGo_1.jpg, images/hueGo_2.jpg',             // Thumbnails for product_id 25
    26 => 'images/gproX_1.jpg, images/gproX_2.jpg',             // Thumbnails for product_id 26
    27 => 'images/mcbookPro16_1.jpg, images/mcbookPro16_2.jpg', // Thumbnails for product_id 27
    28 => 'images/alienwareMonitor_1.jpg, images/alienwareMonitor_2.jpg', // Thumbnails for product_id 28
    29 => 'images/xperia1_1.jpg, images/xperia1_2.avif',        // Thumbnails for product_id 29
    30 => 'images/legion5_1.jpg',                               // Thumbnail for product_id 30
    31 => 'images/mi11_1.jpg, images/mi11_2.jpg',               // Thumbnails for product_id 31
    32 => 'images/frameTv_1.webp, images/frameTv_2.webp',       // Thumbnails for product_id 32
    33 => 'images/airpodsMax_1.jpg, images/airpodsMax_2.webp',  // Thumbnails for product_id 33
    34 => 'images/nestThermo_1.jpg',                            // Thumbnail for product_id 34
    35 => 'images/omen15_1.png, images/omen15_2.png',           // Thumbnails for product_id 35
    36 => 'images/powershot_1.png, images/powershot_2.png',     // Thumbnails for product_id 36
    37 => 'images/nighthawk_1.jpg',                             // Thumbnail for product_id 37
    38 => 'images/xboxSeriesS_1.jpg, images/xboxSeriesS_2.jpg', // Thumbnails for product_id 38
    39 => 'images/huaweiWatchGT_1.jpg, images/huaweiWatchGT_2.jpg', // Thumbnails for product_id 39
    40 => 'images/smartBulb_1.webp, images/smartBulb_2.jpg',     // Thumbnails for product_id 40
    41 => 'images/lumixGH5_1.jpg, images/lumixGH5_2.png',                        // Thumbnails for product_id 41
    42 => 'images/marshall_stanmore_1.jpg, images/marshall_stanmore_2.jpg',      // Thumbnails for product_id 42
    43 => 'images/garminFenix6_1.png, images/garminFenix6_2.png',                // Thumbnails for product_id 43
    44 => 'images/lgGram_1.avif, images/lgGram_2.jpg',                           // Thumbnails for product_id 44
    45 => 'images/galaxyBudsPro_1.avif, images/galaxyBudsPro_2.webp',            // Thumbnails for product_id 45
    46 => 'images/yamaha_1.avif, images/yamaha_2.avif',                          // Thumbnails for product_id 46
    47 => 'images/nestHubMax_1.jpg, images/nestHubMax_2.jpg',                    // Thumbnails for product_id 47
    48 => 'images/auroraR12_1.jpg, images/auroraR12_2.jpg',                      // Thumbnails for product_id 48
    49 => 'images/tcl6_1.jpg, images/tcl6_2.jpg',                                // Thumbnails for product_id 49
    50 => 'images/fitbitCharge5_1.jpg, images/fitbitCharge5_2.jpg',              // Thumbnails for product_id 50
    51 => 'images/nikonz62_1.jpeg, images/nikonz62_2.webp',                      // Thumbnails for product_id 51
    52 => 'images/wonderboom2_1.jpg, images/wonderboom2_2.jpg',                  // Thumbnails for product_id 52
    53 => 'images/razerHuntsmanElite_1.jpg, images/razerHuntsmanElite_2.webp',   // Thumbnails for product_id 53
    54 => 'images/ipadMini6_1.jpg, images/ipadMini6_2.jpg',                      // Thumbnails for product_id 54
    55 => 'images/asusTUF_1.png, images/asusTUF_2.jpg',                          // Thumbnails for product_id 55
    56 => 'images/corsairScimitar_1.jpg, images/corsairScimitar_2.jpg',          // Thumbnails for product_id 56
    57 => 'images/fireStick_1.jpg, images/fireStick_2.jpg',                      // Thumbnails for product_id 57
    58 => 'images/sonosArc_1.jpg, images/sonosArc_2.webp',                       // Thumbnails for product_id 58
    59 => 'images/phantom4Pro_1.webp, images/phantom4Pro_2.jpg',                 // Thumbnails for product_id 59
    60 => 'images/iMac_1.jpg, images/iMac_2.jpg',                                // Thumbnails for product_id 60
    61 => 'images/pixelBudsA_1.webp, images/pixelBudsA_2.jpg',                   // Thumbnails for product_id 61
    62 => 'images/sony1000XM4_1.avif, images/sony1000XM4_2.jpg',                 // Thumbnails for product_id 62
    63 => 'images/ankerPowerCore_1.webp, images/ankerPowerCore_2.webp',          // Thumbnails for product_id 63
    64 => 'images/theragunPro_1.jpg, images/theragunPro_2.webp',                 // Thumbnails for product_id 64
    65 => 'images/beoplayH9i_1.jpg, images/beoplayH9i_2.jpg',                    // Thumbnails for product_id 65
    66 => 'images/epsonET_1.png, images/epsonET_2.jpg',                          // Thumbnails for product_id 66
    67 => 'images/garminVenu_1.webp, images/garminVenu_2.webp',                  // Thumbnails for product_id 67
    68 => 'images/surfaceDuo2_1.jpg, images/surfaceDuo2_2.jpg',                  // Thumbnails for product_id 68
    69 => 'images/samsungSSD_1.avif, images/samsungSSD_2.png',                   // Thumbnails for product_id 69
    70 => 'images/appleTV_1.jpg, images/appleTV_2.jpg',                          // Thumbnails for product_id 70
    71 => 'images/lenovoSmartClock_1.png, images/lenovoSmartClock_2.webp',       // Thumbnails for product_id 71
    72 => 'images/asusZephyrus_1.png, images/asusZephyrus_2.jpg',                // Thumbnails for product_id 72
    73 => 'images/panasonicMicro_1.webp, images/panasonicMicro_2.jpg',           // Thumbnails for product_id 73
    74 => 'images/jabraElite85t_1.jpg, images/jabraElite85t_2.jpg',              // Thumbnails for product_id 74
    75 => 'images/kindlePaperWhite_1.jpg, images/kindlePaperWhite_2.webp',       // Thumbnails for product_id 75
    76 => 'images/roborock_1.webp, images/roborock_2.webp',                      // Thumbnails for product_id 76
    77 => 'images/onePlus9Pro_1.png, images/onePlus9Pro_2.webp',                 // Thumbnails for product_id 77
    78 => 'images/shieldTV_1.jpg, images/shieldTV_2.jpg',                        // Thumbnails for product_id 78
    79 => 'images/smartSleep_1.jpg, images/smartSleep_2.jpg',                    // Thumbnails for product_id 79
    80 => 'images/yamahaPiano_1.jpg, images/yamahaPiano_2.avif',                 // Thumbnails for product_id 80
    81 => 'images/ring_1.jpg, images/ring_2.jpg',                                // Thumbnails for product_id 81
    82 => 'images/olympus_1.jpg, images/olympus_2.jpg',                          // Thumbnails for product_id 82
    83 => 'images/nexStar_1.png, images/nexStar_2.webp',                         // Thumbnails for product_id 83
    84 => 'images/hyperboom_1.png, images/hyperboom_2.webp',                     // Thumbnails for product_id 84
    85 => 'images/osmoMobile_1.png, images/osmoMobile_2.jpg',                    // Thumbnails for product_id 85
    86 => 'images/logitechC920_1.webp, images/logitechC920_2.webp',              // Thumbnails for product_id 86
    87 => 'images/goproMax_1.png, images/goproMax_2.jpg',                        // Thumbnails for product_id 87
    88 => 'images/ipadpro.png',                                                  // Thumbnails for product_id 88
    89 => 'images/watchultra2.png',                                              // Thumbnails for product_id 89
    // Add more products as needed
];

// Update thumbnail URLs in the database for each product
foreach ($thumbnailData as $productId => $thumbnails) {
    $stmt = $dbcnx->prepare("UPDATE products SET thumbnail_urls = ? WHERE product_id = ?");
    if (!$stmt) {
        echo "Failed to prepare statement: " . $dbcnx->error . "<br>";
        continue;  // Skip this iteration if the statement couldn't be prepared
    }
    $stmt->bind_param("si", $thumbnails, $productId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Updated product_id $productId with thumbnails: $thumbnails<br>";
    } else {
        echo "No changes made for product_id $productId. Check if product_id exists and thumbnails differ from existing ones.<br>";
    }
    $stmt->close();
}

$dbcnx->close();
echo "Thumbnail update process complete.";
?>
