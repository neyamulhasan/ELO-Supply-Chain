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
$message = getFlashMessage();

// Get report data
$productReport = $db->fetchAll("
    SELECT p.id, p.name, p.base_price, p.stock_quantity, 
           COUNT(pr.id) as total_requests,
           SUM(CASE WHEN pr.status = 'approved' THEN pr.quantity ELSE 0 END) as units_sold,
           (p.base_price * SUM(CASE WHEN pr.status = 'approved' THEN pr.quantity ELSE 0 END)) as revenue
    FROM products p
    LEFT JOIN purchase_requests pr ON p.id = pr.product_id
    GROUP BY p.id
    ORDER BY revenue DESC
");

$sellerReport = $db->fetchAll("
    SELECT u.id, u.name, u.email, u.status,
           COUNT(pr.id) as total_requests,
           SUM(CASE WHEN pr.status = 'approved' THEN pr.quantity ELSE 0 END) as units_purchased,
           SUM(CASE WHEN pr.status = 'approved' THEN (p.base_price * pr.quantity) ELSE 0 END) as total_spent
    FROM users u
    LEFT JOIN purchase_requests pr ON u.id = pr.seller_id
    LEFT JOIN products p ON pr.product_id = p.id
    WHERE u.role = 'seller'
    GROUP BY u.id
    ORDER BY total_spent DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - <?php echo SITE_NAME; ?></title>
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
                <span class="welcome-text">Welcome, <?php echo sanitize($user['name']); ?> (Admin)</span>
                <a href="<?php echo SITE_URL; ?>logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </nav>
    <div class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="purchase-requests.php">Purchase Requests</a></li>
            <li><a href="sellers.php">Manage Sellers</a></li>
            <li><a href="reports.php" class="active">Reports</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="container">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo sanitize($message['type']); ?>">
                    <?php echo sanitize($message['text']); ?>
                </div>
            <?php endif; ?>
            
            <h1>Reports & Analytics</h1>
            
            <h2>Product Performance</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Base Price</th>
                        <th>Current Stock</th>
                        <th>Total Requests</th>
                        <th>Units Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($productReport) > 0): ?>
                        <?php foreach ($productReport as $item): ?>
                            <tr>
                                <td><?php echo sanitize($item['name']); ?></td>
                                <td>$<?php echo number_format($item['base_price'], 2); ?></td>
                                <td><?php echo intval($item['stock_quantity']); ?></td>
                                <td><?php echo intval($item['total_requests']); ?></td>
                                <td><?php echo intval($item['units_sold'] ?? 0); ?></td>
                                <td>$<?php echo number_format($item['revenue'] ?? 0, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No data available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <h2>Seller Performance</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Seller Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Total Requests</th>
                        <th>Units Purchased</th>
                        <th>Total Spent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($sellerReport) > 0): ?>
                        <?php foreach ($sellerReport as $item): ?>
                            <tr>
                                <td><?php echo sanitize($item['name']); ?></td>
                                <td><?php echo sanitize($item['email']); ?></td>
                                <td><span class="badge badge-<?php echo $item['status']; ?>"><?php echo ucfirst($item['status']); ?></span></td>
                                <td><?php echo intval($item['total_requests']); ?></td>
                                <td><?php echo intval($item['units_purchased'] ?? 0); ?></td>
                                <td>$<?php echo number_format($item['total_spent'] ?? 0, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No data available.</td>
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
