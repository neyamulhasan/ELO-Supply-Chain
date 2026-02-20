<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/functions.php';

$flashMessage = getFlashMessage();
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
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <h1><?php echo SITE_NAME; ?></h1>
            </div>
            <ul class="nav-links">
                <?php if (isLoggedIn()): ?>
                    <li>Welcome, <strong><?php echo sanitize($_SESSION['user_name']); ?></strong> (<?php echo ucfirst($_SESSION['role']); ?>)</li>
                    <li><a href="<?php echo SITE_URL; ?>logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo SITE_URL; ?>login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <?php if ($flashMessage): ?>
        <div class="alert alert-<?php echo sanitize($flashMessage['type']); ?>">
            <?php echo sanitize($flashMessage['text']); ?>
            <button class="close-btn" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
    <?php endif; ?>
</body>
</html>
