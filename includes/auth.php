<?php
/**
 * Authentication functions for Durian Si Buyunk
 */

require_once __DIR__ . '/../config/config.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']) && isset($_SESSION['cabang_id']);
}

// Check if session is valid (not expired)
function isSessionValid() {
    if (!isLoggedIn()) {
        return false;
    }
    
    if (isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
            session_destroy();
            return false;
        }
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

// Require login - redirect if not logged in
function requireLogin($cabang_id = null) {
    if (!isSessionValid()) {
        $login_url = $cabang_id == CABANG_TASIK ? 
            '../auth/login.php' : 
            ($cabang_id == CABANG_GARUT ? '../auth/login.php' : '/login.php');
        redirect($login_url);
    }
    
    // Check if user is accessing correct branch
    if ($cabang_id && $_SESSION['cabang_id'] != $cabang_id) {
        session_destroy();
        redirect('../auth/login.php');
    }
}

// Login user
function loginUser($username, $password) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT id, username, password, nama_lengkap, cabang_id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['cabang_id'] = $user['cabang_id'];
            $_SESSION['last_activity'] = time();
            
            return [
                'success' => true,
                'user' => $user
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Username atau password salah!'
            ];
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
        ];
    }
}

// Logout user
function logoutUser() {
    session_start();
    session_destroy();
    session_start();
}

// Reset password
function resetPassword($username, $newPassword, $resetCode) {
    if ($resetCode !== RESET_CODE) {
        return [
            'success' => false,
            'message' => 'Kode reset tidak valid!'
        ];
    }
    
    try {
        $pdo = getConnection();
        
        // Check if username exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Username tidak ditemukan!'
            ];
        }
        
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE username = ?");
        $stmt->execute([$hashedPassword, $username]);
        
        return [
            'success' => true,
            'message' => 'Password berhasil direset!'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
        ];
    }
}

// Get current user info
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT u.*, c.nama_cabang, c.kode_cabang 
                              FROM users u 
                              JOIN cabang c ON u.cabang_id = c.id 
                              WHERE u.id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}
?>