<?php
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/functions.php';

// If already logged in, redirect based on role
if (isLoggedIn()) {
    $redirectUrl = ($_SESSION['role'] === 'admin') 
        ? SITE_URL . 'admin/dashboard.php' 
        : SITE_URL . 'seller/dashboard.php';
    header('Location: ' . $redirectUrl);
    exit;
}

$error = '';
$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } else {
        $user = $db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
        
        if ($user && verifyPassword($password, $user['password'])) {
            if ($user['status'] !== 'approved') {
                $error = 'Your account is not approved yet. Please contact the administrator.';
            } else {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];
                
                $redirectUrl = ($user['role'] === 'admin')
                    ? SITE_URL . 'admin/dashboard.php'
                    : SITE_URL . 'seller/dashboard.php';
                
                redirect($redirectUrl, 'Login successful!');
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/styles.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <h1><?php echo SITE_NAME; ?></h1>
            <p class="subtitle">Private B2B Inventory & Distribution Management</p>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?php echo sanitize($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            
            <p class="login-note">Default Admin: admin@elo.com / admin123</p>
            <p class="login-note" style="margin-top:.75rem">New seller? <a href="<?php echo SITE_URL; ?>register.php" style="color:var(--green);font-weight:600">Register here</a></p>
        </div>
    </div>
</body>
</html>
