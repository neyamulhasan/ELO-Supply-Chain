<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/functions.php';

// If already logged in, redirect
if (isLoggedIn()) {
    $url = ($_SESSION['role'] === 'admin') ? SITE_URL . 'admin/dashboard.php' : SITE_URL . 'seller/dashboard.php';
    header('Location: ' . $url);
    exit;
}

$error   = '';
$success = '';
$db      = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $confirm  = $_POST['confirm']       ?? '';
    $company  = trim($_POST['company']  ?? '');
    $phone    = trim($_POST['phone']    ?? '');

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Name, email and password are required.';
    } elseif (!isValidEmail($email)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $existing = $db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing) {
            $error = 'An account with this email already exists.';
        } else {
            $hashed = hashPassword($password);
            $ok = $db->execute(
                "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, 'seller', 'pending')",
                [$name, $email, $hashed]
            );
            if ($ok) {
                $success = true;
            } else {
                $error = 'Registration failed. Please try again.';
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
    <title>Register - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/styles.css">
</head>
<body class="login-page">
    <div class="login-container" style="max-width:480px">
        <div class="login-box">
            <h1><?php echo SITE_NAME; ?></h1>
            <p class="subtitle">Create a Seller Account</p>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    ✅ Registration successful! Your account is <strong>pending approval</strong> by an admin. You'll be able to log in once approved.
                </div>
                <a href="<?php echo SITE_URL; ?>login.php" class="btn btn-primary btn-block">Go to Login</a>
            <?php else: ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo sanitize($error); ?></div>
                <?php endif; ?>

                <form method="POST" novalidate>
                    <div class="form-group">
                        <label>Full Name <span style="color:#e74c3c">*</span></label>
                        <input type="text" name="name" value="<?php echo sanitize($_POST['name'] ?? ''); ?>" required placeholder="Your full name">
                    </div>
                    <div class="form-group">
                        <label>Email Address <span style="color:#e74c3c">*</span></label>
                        <input type="email" name="email" value="<?php echo sanitize($_POST['email'] ?? ''); ?>" required placeholder="you@company.com">
                    </div>
                    <div class="form-group">
                        <label>Password <span style="color:#e74c3c">*</span></label>
                        <input type="password" name="password" required placeholder="Min 6 characters">
                    </div>
                    <div class="form-group">
                        <label>Confirm Password <span style="color:#e74c3c">*</span></label>
                        <input type="password" name="confirm" required placeholder="Repeat password">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="margin-top:.5rem">Create Account</button>
                </form>

                <div class="login-note">
                    After registering, an admin must <strong>approve</strong> your account before you can log in.<br><br>
                    Already have an account? <a href="<?php echo SITE_URL; ?>login.php">Login here</a>
                </div>

            <?php endif; ?>
        </div>
    </div>
</body>
</html>
