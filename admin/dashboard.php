<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$db = new Database();
$user = getCurrentUser();

// Get dashboard statistics
$totalProducts = $db->fetch("SELECT COUNT(*) as count FROM products")['count'];
$totalSellers = $db->fetch("SELECT COUNT(*) as count FROM users WHERE role = 'seller'")['count'];
$approvedSellers = $db->fetch("SELECT COUNT(*) as count FROM users WHERE role = 'seller' AND status = 'approved'")['count'];
$pendingSellers = $db->fetch("SELECT COUNT(*) as count FROM users WHERE role = 'seller' AND status = 'pending'")['count'];

// Get total stock value
$totalStockValue = $db->fetch("SELECT SUM(base_price * stock_quantity) as total FROM products")['total'] ?? 0;

// Get pending purchase requests
$pendingRequests = $db->fetch("SELECT COUNT(*) as count FROM purchase_requests WHERE status = 'pending'")['count'];

$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
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
                <span class="welcome-text">Welcome, <strong><?php echo sanitize($user['name']); ?></strong> (Admin)</span>
                <a href="<?php echo SITE_URL; ?>logout.php" class="btn btn-logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active">Dashboard</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="purchase-requests.php">Purchase Requests <?php if ($pendingRequests > 0) echo "<span class='badge'>$pendingRequests</span>"; ?></a></li>
            <li><a href="sellers.php">Manage Sellers</a></li>
            <li><a href="reports.php">Reports</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="container">
            <?php if ($flashMessage): ?>
                <div class="alert alert-<?php echo sanitize($flashMessage['type']); ?>">
                    <?php echo sanitize($flashMessage['text']); ?>
                    <button class="close-btn" onclick="this.parentElement.style.display='none';">&times;</button>
                </div>
            <?php endif; ?>
            <h1>Admin Dashboard</h1>
            <p>Welcome, <?php echo sanitize($user['name']); ?>!</p>
            
            <div class="dashboard-grid">
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <p class="stat-number"><?php echo $totalProducts; ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Total Stock Value</h3>
                    <p class="stat-number">$<?php echo number_format($totalStockValue, 2); ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Total Sellers</h3>
                    <p class="stat-number"><?php echo $totalSellers; ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Approved Sellers</h3>
                    <p class="stat-number"><?php echo $approvedSellers; ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Pending Sellers</h3>
                    <p class="stat-number"><?php echo $pendingSellers; ?></p>
                    <a href="sellers.php" class="btn btn-small">Review</a>
                </div>
                
                <div class="stat-card">
                    <h3>Pending Requests</h3>
                    <p class="stat-number"><?php echo $pendingRequests; ?></p>
                    <a href="purchase-requests.php" class="btn btn-small">Review</a>
                </div>
            </div>
            
            <div class="actions-section">
                <h2>Quick Actions</h2>
                <a href="products.php?action=add" class="btn btn-primary">Add New Product</a>
                <a href="sellers.php" class="btn btn-secondary">Review Sellers</a>
                <a href="purchase-requests.php" class="btn btn-secondary">Review Purchase Requests</a>
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
