<?php
/**
 * Logout functionality for Garut branch
 */

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth.php';

// Perform logout
logoutUser();

// Redirect to login page
redirect('login.php');
?>