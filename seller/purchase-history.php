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

// Get seller's purchase history
$filter = $_GET['filter'] ?? '';
$query = "SELECT pr.*, p.name as product_name, p.base_price 
          FROM purchase_requests pr
          JOIN products p ON pr.product_id = p.id
          WHERE pr.seller_id = ?";

if ($filter === 'pending') {
    $query .= " AND pr.status = 'pending'";
} elseif ($filter === 'approved') {
    $query .= " AND pr.status = 'approved'";
} elseif ($filter === 'rejected') {
    $query .= " AND pr.status = 'rejected'";
}

$query .= " ORDER BY pr.created_at DESC";
$purchases = $db->fetchAll($query, [$user['id']]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History - <?php echo SITE_NAME; ?></title>
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
            <li><a href="products.php">Products</a></li>
            <li><a href="purchase-history.php" class="active">Purchase History</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="container">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo sanitize($message['type']); ?>">
                    <?php echo sanitize($message['text']); ?>
                </div>
            <?php endif; ?>
            
            <h1>Purchase History</h1>
            
            <div class="filter-buttons">
                <a href="?filter=" class="btn <?php echo ($filter === '' ? 'btn-primary' : 'btn-secondary'); ?>">All</a>
                <a href="?filter=pending" class="btn <?php echo ($filter === 'pending' ? 'btn-primary' : 'btn-secondary'); ?>">Pending</a>
                <a href="?filter=approved" class="btn <?php echo ($filter === 'approved' ? 'btn-primary' : 'btn-secondary'); ?>">Approved</a>
                <a href="?filter=rejected" class="btn <?php echo ($filter === 'rejected' ? 'btn-primary' : 'btn-secondary'); ?>">Rejected</a>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price per Unit</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Requested Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($purchases) > 0): ?>
                        <?php foreach ($purchases as $purchase): ?>
                            <tr>
                                <td><?php echo sanitize($purchase['product_name']); ?></td>
                                <td><?php echo intval($purchase['quantity']); ?></td>
                                <td>$<?php echo number_format($purchase['base_price'], 2); ?></td>
                                <td>$<?php echo number_format($purchase['base_price'] * $purchase['quantity'], 2); ?></td>
                                <td><span class="badge badge-<?php echo $purchase['status']; ?>"><?php echo ucfirst($purchase['status']); ?></span></td>
                                <td><?php echo date('M d, Y H:i', strtotime($purchase['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No purchase history found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
