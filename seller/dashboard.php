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

if ($user['status'] !== 'approved') {
    redirect(SITE_URL . 'index.php', 'Your account is not approved yet.', 'danger');
}

// Get seller statistics
$totalProducts = $db->fetch("SELECT COUNT(*) as count FROM products")['count'];
$availableStock = $db->fetch("SELECT COUNT(*) as count FROM products WHERE stock_quantity > 0")['count'];

// Get seller's purchase history
$totalPurchases = $db->fetch("SELECT COUNT(*) as count FROM purchase_requests WHERE seller_id = ?", [$user['id']])['count'];
$approvedPurchases = $db->fetch("SELECT COUNT(*) as count FROM purchase_requests WHERE seller_id = ? AND status = 'approved'", [$user['id']])['count'];

// Get total spent
$totalSpent = $db->fetch("
    SELECT SUM(p.base_price * pr.quantity) as total
    FROM purchase_requests pr
    JOIN products p ON pr.product_id = p.id
    WHERE pr.seller_id = ? AND pr.status = 'approved'
", [$user['id']])['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard - <?php echo SITE_NAME; ?></title>
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
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="purchase-history.php">Purchase History</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="container">
            <h1>Seller Dashboard</h1>
            <p>Welcome, <?php echo sanitize($user['name']); ?>!</p>
            
            <div class="dashboard-grid">
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <p class="stat-number"><?php echo $totalProducts; ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Available Stock</h3>
                    <p class="stat-number"><?php echo $availableStock; ?></p>
                    <a href="products.php" class="btn btn-small">Browse</a>
                </div>
                
                <div class="stat-card">
                    <h3>Total Purchases</h3>
                    <p class="stat-number"><?php echo $totalPurchases; ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Approved Purchases</h3>
                    <p class="stat-number"><?php echo $approvedPurchases; ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Total Spent</h3>
                    <p class="stat-number">$<?php echo number_format($totalSpent, 2); ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Account Status</h3>
                    <p class="stat-number"><span class="badge badge-approved">Approved</span></p>
                </div>
            </div>
            
            <div class="actions-section">
                <h2>Quick Actions</h2>
                <a href="products.php" class="btn btn-primary">Browse Products</a>
                <a href="purchase-history.php" class="btn btn-secondary">View Purchase History</a>
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
