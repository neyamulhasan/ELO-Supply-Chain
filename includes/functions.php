<?php

// Session Check
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . 'login.php');
        exit;
    }
}

function requireAdmin() {
    if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
        header('Location: ' . SITE_URL . 'index.php');
        exit;
    }
}

function requireSeller() {
    if (!isLoggedIn() || $_SESSION['role'] !== 'seller') {
        header('Location: ' . SITE_URL . 'index.php');
        exit;
    }
}

// Get Current User
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    $db = new Database();
    return $db->fetch("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

// Password Hashing
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Redirect with Message
function redirect($url, $message = '', $type = 'success') {
    if (!empty($message)) {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type;
    }
    header('Location: ' . $url);
    exit;
}

// Get Flash Message
function getFlashMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'] ?? 'info';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        return ['text' => $message, 'type' => $type];
    }
    return null;
}

// Sanitize Input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Validate Email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Upload File
function uploadFile($file, $targetDir = UPLOADS_DIR) {
    if (!isset($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $filename = basename($file['name']);
    $fileExt = pathinfo($filename, PATHINFO_EXTENSION);
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (!in_array(strtolower($fileExt), $allowedExts)) {
        return false;
    }
    
    $newFilename = uniqid() . '.' . $fileExt;
    $targetPath = $targetDir . $newFilename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $newFilename;
    }
    
    return false;
}

// Delete File
function deleteFile($filename) {
    $filepath = UPLOADS_DIR . $filename;
    if (file_exists($filepath)) {
        unlink($filepath);
        return true;
    }
    return false;
}
?>
