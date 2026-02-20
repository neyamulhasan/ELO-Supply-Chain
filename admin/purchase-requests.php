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
$action = $_GET['action'] ?? 'list';
$message = getFlashMessage();

// Approve Purchase Request
if ($action === 'approve' && isset($_GET['id'])) {
    $requestId = intval($_GET['id']);
    $request = $db->fetch("SELECT * FROM purchase_requests WHERE id = ?", [$requestId]);
    
    if ($request) {
        $product = $db->fetch("SELECT * FROM products WHERE id = ?", [$request['product_id']]);
        
        // Check if stock is available
        if ($product['stock_quantity'] >= $request['quantity']) {
            // Update request status
            $db->execute("UPDATE purchase_requests SET status = 'approved' WHERE id = ?", [$requestId]);
            
            // Decrease stock
            $newStock = $product['stock_quantity'] - $request['quantity'];
            $db->execute("UPDATE products SET stock_quantity = ? WHERE id = ?", [$newStock, $product['id']]);
            
            redirect(SITE_URL . 'admin/purchase-requests.php', 'Purchase request approved and stock updated!');
        } else {
            redirect(SITE_URL . 'admin/purchase-requests.php', 'Insufficient stock for this purchase.', 'danger');
        }
    }
}

// Reject Purchase Request
if ($action === 'reject' && isset($_GET['id'])) {
    $requestId = intval($_GET['id']);
    $db->execute("UPDATE purchase_requests SET status = 'rejected' WHERE id = ?", [$requestId]);
    redirect(SITE_URL . 'admin/purchase-requests.php', 'Purchase request rejected.');
}

// Get pending requests
$requests = [];
$filter = $_GET['filter'] ?? 'pending';
$query = "SELECT pr.*, u.name as seller_name, u.email as seller_email, p.name as product_name, p.base_price 
          FROM purchase_requests pr
          JOIN users u ON pr.seller_id = u.id
          JOIN products p ON pr.product_id = p.id";

if ($filter === 'pending') {
    $query .= " WHERE pr.status = 'pending'";
} elseif ($filter === 'approved') {
    $query .= " WHERE pr.status = 'approved'";
} elseif ($filter === 'rejected') {
    $query .= " WHERE pr.status = 'rejected'";
}

$query .= " ORDER BY pr.created_at DESC";
$requests = $db->fetchAll($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Requests - <?php echo SITE_NAME; ?></title>
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
            <li><a href="purchase-requests.php" class="active">Purchase Requests</a></li>
            <li><a href="sellers.php">Manage Sellers</a></li>
            <li><a href="reports.php">Reports</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="container">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo sanitize($message['type']); ?>">
                    <?php echo sanitize($message['text']); ?>
                </div>
            <?php endif; ?>
            
            <h1>Purchase Requests</h1>
            
            <div class="filter-buttons">
                <a href="?filter=pending" class="btn <?php echo ($filter === 'pending' ? 'btn-primary' : 'btn-secondary'); ?>">Pending</a>
                <a href="?filter=approved" class="btn <?php echo ($filter === 'approved' ? 'btn-primary' : 'btn-secondary'); ?>">Approved</a>
                <a href="?filter=rejected" class="btn <?php echo ($filter === 'rejected' ? 'btn-primary' : 'btn-secondary'); ?>">Rejected</a>
                <a href="?filter=" class="btn <?php echo ($filter === '' ? 'btn-primary' : 'btn-secondary'); ?>">All</a>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Seller</th>
                        <th>Email</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price per Unit</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($requests) > 0): ?>
                        <?php foreach ($requests as $req): ?>
                            <tr>
                                <td><?php echo sanitize($req['seller_name']); ?></td>
                                <td><?php echo sanitize($req['seller_email']); ?></td>
                                <td><?php echo sanitize($req['product_name']); ?></td>
                                <td><?php echo intval($req['quantity']); ?></td>
                                <td>$<?php echo number_format($req['base_price'], 2); ?></td>
                                <td>$<?php echo number_format($req['base_price'] * $req['quantity'], 2); ?></td>
                                <td><span class="badge badge-<?php echo $req['status']; ?>"><?php echo ucfirst($req['status']); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($req['created_at'])); ?></td>
                                <td>
                                    <?php if ($req['status'] === 'pending'): ?>
                                        <a href="?action=approve&id=<?php echo $req['id']; ?>" class="btn btn-small btn-success" onclick="return confirm('Approve this request?');">Approve</a>
                                        <a href="?action=reject&id=<?php echo $req['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Reject this request?');">Reject</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">No purchase requests found.</td>
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
