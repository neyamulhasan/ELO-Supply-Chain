<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/functions.php';

requireSeller();

$db = new Database();
$user = getCurrentUser();
$message = getFlashMessage();

if ($user['status'] !== 'approved') {
    redirect(SITE_URL . 'index.php', 'Your account is not approved yet.', 'danger');
}

// Get all available products
$products = $db->fetchAll("SELECT * FROM products ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h1><?php echo SITE_NAME; ?></h1>
            </div>
            <button class="hamburger" id="hamburger" aria-label="Menu" style="display:none"><span></span><span></span><span></span></button>
            <div class="nav-right">
                <span class="welcome-text">Welcome, <?php echo sanitize($user['name']); ?> (Seller)</span>
                <a href="<?php echo SITE_URL; ?>logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </nav>
    <div class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="products.php" class="active">Products</a></li>
            <li><a href="purchase-history.php">Purchase History</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="container">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo sanitize($message['type']); ?>">
                    <?php echo sanitize($message['text']); ?>
                </div>
            <?php endif; ?>
            
            <h1>Available Products</h1>
            <p class="subtitle">View all available products and their pricing</p>
            
            <div class="products-grid">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <?php if ($product['image']): ?>
                                <div class="product-image">
                                    <img src="<?php echo UPLOADS_URL . sanitize($product['image']); ?>" alt="<?php echo sanitize($product['name']); ?>">
                                </div>
                            <?php else: ?>
                                <div class="product-image no-image">
                                    <span>No Image</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="product-info">
                                <h3><?php echo sanitize($product['name']); ?></h3>
                                <p class="description"><?php echo sanitize(substr($product['description'], 0, 100)); ?>...</p>
                                
                                <div class="product-details">
                                    <p><strong>Base Price:</strong> $<?php echo number_format($product['base_price'], 2); ?></p>
                                    <p><strong>Stock Available:</strong> 
                                        <?php if ($product['stock_quantity'] > 0): ?>
                                            <span class="stock-available"><?php echo intval($product['stock_quantity']); ?> units</span>
                                        <?php else: ?>
                                            <span class="stock-unavailable">Out of Stock</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                
                                <?php if ($product['stock_quantity'] > 0): ?>
                                    <a href="request-product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">Request Product</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled>Out of Stock</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted">No products available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
<div id="sidebar-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:99" onclick="closeSidebar()"></div>
<script>
var hamburger=document.getElementById("hamburger"),sidebar=document.querySelector(".sidebar"),overlay=document.getElementById("sidebar-overlay");
function openSidebar(){sidebar.classList.add("open");overlay.style.display="block";}
function closeSidebar(){sidebar.classList.remove("open");overlay.style.display="none";}
if(hamburger)hamburger.addEventListener("click",function(){sidebar.classList.contains("open")?closeSidebar():openSidebar();});
window.addEventListener("resize",function(){if(window.innerWidth>768)closeSidebar();});
</script>
</body>
</html>
