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
$productId = intval($_GET['id'] ?? 0);
$message = getFlashMessage();

if ($user['status'] !== 'approved') {
    redirect(SITE_URL . 'index.php', 'Your account is not approved yet.', 'danger');
}

$product = $db->fetch("SELECT * FROM products WHERE id = ?", [$productId]);

if (!$product) {
    redirect(SITE_URL . 'seller/products.php', 'Product not found.', 'danger');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = intval($_POST['quantity'] ?? 0);
    
    if ($quantity <= 0) {
        $error = 'Please enter a valid quantity.';
    } elseif ($quantity > $product['stock_quantity']) {
        $error = 'Requested quantity exceeds available stock.';
    } else {
        // Check if seller already has a pending request for this product
        $existingRequest = $db->fetch(
            "SELECT * FROM purchase_requests WHERE seller_id = ? AND product_id = ? AND status = 'pending'",
            [$user['id'], $productId]
        );
        
        if ($existingRequest) {
            $error = 'You already have a pending request for this product. Please wait for approval.';
        } else {
            // Create purchase request
            $query = "INSERT INTO purchase_requests (seller_id, product_id, quantity, status) VALUES (?, ?, ?, 'pending')";
            if ($db->execute($query, [$user['id'], $productId, $quantity])) {
                redirect(SITE_URL . 'seller/purchase-history.php', 'Purchase request submitted successfully! Please wait for admin approval.');
            } else {
                $error = 'Failed to submit purchase request.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Product - <?php echo SITE_NAME; ?></title>
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
            
            <h1>Request Product</h1>
            
            <div class="product-detail-card">
                <?php if ($product['image']): ?>
                    <div class="product-detail-image">
                        <img src="<?php echo UPLOADS_URL . sanitize($product['image']); ?>" alt="<?php echo sanitize($product['name']); ?>">
                    </div>
                <?php endif; ?>
                
                <div class="product-detail-info">
                    <h2><?php echo sanitize($product['name']); ?></h2>
                    <p class="description"><?php echo sanitize($product['description']); ?></p>
                    
                    <div class="product-specs">
                        <p><strong>Base Price per Unit:</strong> $<?php echo number_format($product['base_price'], 2); ?></p>
                        <p><strong>Stock Available:</strong> <?php echo intval($product['stock_quantity']); ?> units</p>
                    </div>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo sanitize($error); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" class="form-container">
                        <div class="form-group">
                            <label for="quantity">Quantity to Request *</label>
                            <input type="number" id="quantity" name="quantity" min="1" max="<?php echo $product['stock_quantity']; ?>" required>
                            <small>Available: <?php echo intval($product['stock_quantity']); ?> units</small>
                        </div>
                        
                        <div class="price-summary">
                            <h3>Order Summary</h3>
                            <p><strong>Unit Price:</strong> $<?php echo number_format($product['base_price'], 2); ?></p>
                            <p><strong>Quantity:</strong> <span id="qty-display">0</span> units</p>
                            <p><strong>Total:</strong> $<span id="total-display">0.00</span></p>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Submit Purchase Request</button>
                            <a href="products.php" class="btn btn-secondary">Back to Products</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        const quantityInput = document.getElementById('quantity');
        const unitPrice = <?php echo $product['base_price']; ?>;
        
        function updateTotal() {
            const quantity = parseInt(quantityInput.value) || 0;
            document.getElementById('qty-display').textContent = quantity;
            document.getElementById('total-display').textContent = (quantity * unitPrice).toFixed(2);
        }
        
        quantityInput.addEventListener('input', updateTotal);
        quantityInput.addEventListener('change', updateTotal);
    </script>
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
