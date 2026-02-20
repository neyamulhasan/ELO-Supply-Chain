<?php
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

// If already logged in, redirect
if (isLoggedIn()) {
    $redirectUrl = ($_SESSION['role'] === 'admin') 
        ? SITE_URL . 'admin/dashboard.php' 
        : SITE_URL . 'seller/dashboard.php';
    header('Location: ' . $redirectUrl);
    exit;
}

// Not logged in, show homepage
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/styles.css">
</head>
<body>
    <div class="home-container">
        <div class="hero">
            <h1><?php echo SITE_NAME; ?></h1>
            <p class="subtitle">Private B2B Inventory & Distribution Management System</p>
            
            <div class="hero-content">
                <h2>Welcome to ELO Supply Chain</h2>
                <p>A centralized admin-controlled dashboard for inventory management and seller distribution.</p>
                
                <a href="<?php echo SITE_URL; ?>login.php" class="btn btn-primary btn-lg">Login to System</a>
            </div>
            
            <div class="features">
                <div class="feature-card">
                    <h3>For Admins</h3>
                    <ul>
                        <li>Manage products and inventory</li>
                        <li>Set base prices</li>
                        <li>Approve seller accounts</li>
                        <li>Review purchase requests</li>
                    </ul>
                </div>
                
                <div class="feature-card">
                    <h3>For Sellers</h3>
                    <ul>
                        <li>View available products</li>
                        <li>Check stock levels</li>
                        <li>Request/purchase stock</li>
                        <li>Track purchase history</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
