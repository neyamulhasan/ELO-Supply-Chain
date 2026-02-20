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

// Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    $name = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $basePrice = floatval($_POST['base_price'] ?? 0);
    $stockQty = intval($_POST['stock_quantity'] ?? 0);
    $image = '';
    
    if (!empty($_FILES['image']['name'])) {
        $image = uploadFile($_FILES['image']);
        if (!$image) {
            $error = 'Invalid image format. Please upload JPG, PNG, or GIF.';
        }
    }
    
    if (empty($name) || $basePrice <= 0) {
        $error = $error ?? 'Product name and valid base price are required.';
    }
    
    if (empty($error)) {
        $query = "INSERT INTO products (name, description, base_price, stock_quantity, image) 
                  VALUES (?, ?, ?, ?, ?)";
        if ($db->execute($query, [$name, $description, $basePrice, $stockQty, $image])) {
            redirect(SITE_URL . 'admin/products.php', 'Product added successfully!');
        } else {
            $error = 'Failed to add product.';
        }
    }
}

// Edit Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'edit') {
    $productId = intval($_GET['id'] ?? 0);
    $name = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $basePrice = floatval($_POST['base_price'] ?? 0);
    $stockQty = intval($_POST['stock_quantity'] ?? 0);
    
    $product = $db->fetch("SELECT * FROM products WHERE id = ?", [$productId]);
    
    if (!$product) {
        redirect(SITE_URL . 'admin/products.php', 'Product not found.', 'danger');
    }
    
    $image = $product['image'];
    
    if (!empty($_FILES['image']['name'])) {
        if ($image && file_exists(UPLOADS_DIR . $image)) {
            deleteFile($image);
        }
        $image = uploadFile($_FILES['image']);
        if (!$image) {
            $error = 'Invalid image format.';
        }
    }
    
    if (empty($name) || $basePrice <= 0) {
        $error = $error ?? 'Product name and valid base price are required.';
    }
    
    if (empty($error)) {
        $query = "UPDATE products SET name = ?, description = ?, base_price = ?, stock_quantity = ?, image = ? WHERE id = ?";
        if ($db->execute($query, [$name, $description, $basePrice, $stockQty, $image, $productId])) {
            redirect(SITE_URL . 'admin/products.php', 'Product updated successfully!');
        } else {
            $error = 'Failed to update product.';
        }
    }
}

// Delete Product
if ($action === 'delete' && isset($_GET['id'])) {
    $productId = intval($_GET['id']);
    $product = $db->fetch("SELECT * FROM products WHERE id = ?", [$productId]);
    
    if ($product) {
        if ($product['image'] && file_exists(UPLOADS_DIR . $product['image'])) {
            deleteFile($product['image']);
        }
        $db->execute("DELETE FROM products WHERE id = ?", [$productId]);
        redirect(SITE_URL . 'admin/products.php', 'Product deleted successfully!');
    }
}

// Get product for edit
$editProduct = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $editProduct = $db->fetch("SELECT * FROM products WHERE id = ?", [intval($_GET['id'])]);
}

// Get all products for list view
$products = [];
if ($action === 'list') {
    $products = $db->fetchAll("SELECT * FROM products ORDER BY created_at DESC");
}

$flashMessage = getFlashMessage();
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
                <span class="welcome-text">Welcome, <strong><?php echo sanitize($user['name']); ?></strong> (Admin)</span>
                <a href="<?php echo SITE_URL; ?>logout.php" class="btn btn-logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="products.php" class="active">Products</a></li>
            <li><a href="purchase-requests.php">Purchase Requests</a></li>
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
            
            <?php if ($action === 'list'): ?>
                <h1>Products</h1>
                <a href="?action=add" class="btn btn-primary">Add New Product</a>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Base Price</th>
                            <th>Stock</th>
                            <th>Stock Value</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $prod): ?>
                            <tr>
                                <td><?php echo sanitize($prod['name']); ?></td>
                                <td>$<?php echo number_format($prod['base_price'], 2); ?></td>
                                <td><?php echo intval($prod['stock_quantity']); ?></td>
                                <td>$<?php echo number_format($prod['base_price'] * $prod['stock_quantity'], 2); ?></td>
                                <td>
                                    <?php if ($prod['image']): ?>
                                        <img src="<?php echo UPLOADS_URL . sanitize($prod['image']); ?>" alt="Product" style="max-width: 50px;">
                                    <?php else: ?>
                                        <span class="text-muted">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?action=edit&id=<?php echo $prod['id']; ?>" class="btn btn-small btn-info">Edit</a>
                                    <a href="?action=delete&id=<?php echo $prod['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Delete this product?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
            <?php elseif ($action === 'add' || $action === 'edit'): ?>
                <h1><?php echo ($action === 'add' ? 'Add New Product' : 'Edit Product'); ?></h1>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo sanitize($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" class="form-container">
                    <div class="form-group">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name" required value="<?php echo ($editProduct ? sanitize($editProduct['name']) : ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description"><?php echo ($editProduct ? sanitize($editProduct['description']) : ''); ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="base_price">Base Price *</label>
                            <input type="number" id="base_price" name="base_price" step="0.01" required value="<?php echo ($editProduct ? $editProduct['base_price'] : ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="stock_quantity">Stock Quantity *</label>
                            <input type="number" id="stock_quantity" name="stock_quantity" required value="<?php echo ($editProduct ? $editProduct['stock_quantity'] : ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Product Image</label>
                        <input type="file" id="image" name="image" accept="image/*">
                        <?php if ($editProduct && $editProduct['image']): ?>
                            <p class="text-muted">Current image: <img src="<?php echo UPLOADS_URL . sanitize($editProduct['image']); ?>" style="max-width: 100px;"></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Product</button>
                        <a href="products.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            <?php endif; ?>
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
