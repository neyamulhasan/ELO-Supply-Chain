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

// Approve Seller
if ($action === 'approve' && isset($_GET['id'])) {
    $sellerId = intval($_GET['id']);
    $db->execute("UPDATE users SET status = 'approved' WHERE id = ? AND role = 'seller'", [$sellerId]);
    redirect(SITE_URL . 'admin/sellers.php', 'Seller approved successfully!');
}

// Reject Seller
if ($action === 'reject' && isset($_GET['id'])) {
    $sellerId = intval($_GET['id']);
    $db->execute("UPDATE users SET status = 'rejected' WHERE id = ? AND role = 'seller'", [$sellerId]);
    redirect(SITE_URL . 'admin/sellers.php', 'Seller rejected.');
}

// Delete Seller
if ($action === 'delete' && isset($_GET['id'])) {
    $sellerId = intval($_GET['id']);
    $db->execute("DELETE FROM users WHERE id = ? AND role = 'seller'", [$sellerId]);
    redirect(SITE_URL . 'admin/sellers.php', 'Seller deleted successfully!');
}

// Add Seller (admin creates seller directly — auto-approved)
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName  = trim($_POST['new_name']  ?? '');
    $newEmail = trim($_POST['new_email'] ?? '');
    $newPass  = $_POST['new_password']   ?? '';
    $addError = '';

    if (empty($newName) || empty($newEmail) || empty($newPass)) {
        $addError = 'All fields are required.';
    } elseif (!isValidEmail($newEmail)) {
        $addError = 'Invalid email address.';
    } elseif (strlen($newPass) < 6) {
        $addError = 'Password must be at least 6 characters.';
    } else {
        $exists = $db->fetch("SELECT id FROM users WHERE email = ?", [$newEmail]);
        if ($exists) {
            $addError = 'A user with this email already exists.';
        } else {
            $hashed = hashPassword($newPass);
            $db->execute(
                "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'seller', 'approved')",
                [$newName, $newEmail, $hashed]
            );
            redirect(SITE_URL . 'admin/sellers.php', 'Seller added and approved successfully!');
        }
    }
}

// Get sellers
$sellers = [];
$filter = $_GET['filter'] ?? '';
$query = "SELECT u.*, COUNT(pr.id) as purchase_count FROM users u 
          LEFT JOIN purchase_requests pr ON u.id = pr.seller_id
          WHERE u.role = 'seller'";

if ($filter === 'pending') {
    $query .= " AND u.status = 'pending'";
} elseif ($filter === 'approved') {
    $query .= " AND u.status = 'approved'";
} elseif ($filter === 'rejected') {
    $query .= " AND u.status = 'rejected'";
}

$query .= " GROUP BY u.id ORDER BY u.created_at DESC";
$sellers = $db->fetchAll($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sellers - <?php echo SITE_NAME; ?></title>
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
            <li><a href="sellers.php" class="active">Manage Sellers</a></li>
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
            
            <div class="actions-section" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
                <h1 style="margin:0">Manage Sellers</h1>
                <a href="?action=add" class="btn btn-primary">+ Add Seller</a>
            </div>

            <?php if ($action === 'add'): ?>
            <div class="form-container" style="margin-bottom:2rem">
                <h2>Add New Seller</h2>
                <?php if (!empty($addError)): ?>
                    <div class="alert alert-danger"><?php echo sanitize($addError); ?></div>
                <?php endif; ?>
                <form method="POST" action="?action=add">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name <span style="color:#e74c3c">*</span></label>
                            <input type="text" name="new_name" value="<?php echo sanitize($_POST['new_name'] ?? ''); ?>" required placeholder="Seller full name">
                        </div>
                        <div class="form-group">
                            <label>Email Address <span style="color:#e74c3c">*</span></label>
                            <input type="email" name="new_email" value="<?php echo sanitize($_POST['new_email'] ?? ''); ?>" required placeholder="seller@company.com">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Password <span style="color:#e74c3c">*</span></label>
                            <input type="password" name="new_password" required placeholder="Min 6 characters">
                        </div>
                        <div class="form-group">
                            <label style="color:transparent">_</label>
                            <p style="margin:.5rem 0;font-size:.85rem;color:#666">Account will be <strong>auto-approved</strong>. The seller can log in immediately.</p>
                        </div>
                    </div>
                    <div class="form-actions">
                        <a href="sellers.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Seller</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <div class="filter-buttons">
                <a href="?filter=" class="btn <?php echo ($filter === '' ? 'btn-primary' : 'btn-secondary'); ?>">All</a>
                <a href="?filter=pending" class="btn <?php echo ($filter === 'pending' ? 'btn-primary' : 'btn-secondary'); ?>">Pending</a>
                <a href="?filter=approved" class="btn <?php echo ($filter === 'approved' ? 'btn-primary' : 'btn-secondary'); ?>">Approved</a>
                <a href="?filter=rejected" class="btn <?php echo ($filter === 'rejected' ? 'btn-primary' : 'btn-secondary'); ?>">Rejected</a>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Purchases</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($sellers) > 0): ?>
                        <?php foreach ($sellers as $seller): ?>
                            <tr>
                                <td><?php echo sanitize($seller['name']); ?></td>
                                <td><?php echo sanitize($seller['email']); ?></td>
                                <td><span class="badge badge-<?php echo $seller['status']; ?>"><?php echo ucfirst($seller['status']); ?></span></td>
                                <td><?php echo intval($seller['purchase_count']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($seller['created_at'])); ?></td>
                                <td>
                                    <?php if ($seller['status'] === 'pending'): ?>
                                        <a href="?action=approve&id=<?php echo $seller['id']; ?>" class="btn btn-small btn-success" onclick="return confirm('Approve this seller?');">Approve</a>
                                        <a href="?action=reject&id=<?php echo $seller['id']; ?>" class="btn btn-small btn-warning" onclick="return confirm('Reject this seller?');">Reject</a>
                                    <?php endif; ?>
                                    <a href="?action=delete&id=<?php echo $seller['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Delete this seller? This will also delete their purchase history.');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No sellers found.</td>
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
