<?php
/**
 * Common header template for admin pages
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/auth.php';

// Get current user info
$currentUser = getCurrentUser();
$branchName = $currentUser ? $currentUser['nama_cabang'] : '';
$branchTheme = '';

// Set theme based on branch
if ($currentUser) {
    if ($currentUser['cabang_id'] == CABANG_TASIK) {
        $branchTheme = 'tasik';
    } elseif ($currentUser['cabang_id'] == CABANG_GARUT) {
        $branchTheme = 'garut';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' | ' : '' ?><?= APP_NAME ?> - <?= $branchName ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: <?= $branchTheme == 'tasik' ? '#28a745' : '#007bff' ?>;
            --primary-dark: <?= $branchTheme == 'tasik' ? '#1e7e34' : '#0056b3' ?>;
            --primary-light: <?= $branchTheme == 'tasik' ? '#d4edda' : '#cce7ff' ?>;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .card-header.bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .border-primary {
            border-color: var(--primary-color) !important;
        }
        
        .sidebar {
            background-color: #f8f9fa;
            min-height: calc(100vh - 56px);
            border-right: 1px solid #dee2e6;
        }
        
        .sidebar .nav-link {
            color: #495057;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
        }
        
        .sidebar .nav-link:hover {
            background-color: var(--primary-light);
            color: var(--primary-dark);
        }
        
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-seedling"></i> <?= APP_NAME ?>
            </a>
            <span class="badge bg-secondary fs-6"><?= $branchName ?></span>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> <?= $currentUser['nama_lengkap'] ?? 'User' ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" href="../dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/produk/') !== false ? 'active' : '' ?>" href="../produk/">
                                <i class="fas fa-box"></i> Kelola Produk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/manajemen-stok/') !== false ? 'active' : '' ?>" href="../manajemen-stok/">
                                <i class="fas fa-warehouse"></i> Kelola Stok
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/kategori/') !== false ? 'active' : '' ?>" href="../kategori/">
                                <i class="fas fa-tags"></i> Kelola Kategori
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], '/laporan/') !== false ? 'active' : '' ?>" href="../laporan/">
                                <i class="fas fa-chart-bar me-2"></i>
                                Laporan & Analisis
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="../../" target="_blank">
                                <i class="fas fa-eye"></i> Lihat Halaman Publik
                            </a>
                        </li> -->
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="pt-3">