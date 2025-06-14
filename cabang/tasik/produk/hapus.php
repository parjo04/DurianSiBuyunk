<?php
/**
 * Delete product page for Tasikmalaya branch
 */

require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';

// Require login and check branch access
requireLogin(CABANG_TASIK);

// Get product ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    redirect('index.php');
}

// Get product data
$product = getProduct($id);
if (!$product) {
    redirect('index.php');
}

// Delete product
$result = deleteProduct($id);

// Set session message
$_SESSION['message'] = $result['message'];
$_SESSION['message_type'] = $result['success'] ? 'success' : 'danger';

// Redirect back to product list
redirect('index.php');
?>