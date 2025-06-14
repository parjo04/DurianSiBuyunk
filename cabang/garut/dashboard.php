<?php
/**
 * Dashboard for Garut branch admin
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../config/config.php';

// Require login and check branch access
requireLogin(CABANG_GARUT);

$pageTitle = 'Dashboard';

// Get current user info
$currentUser = getCurrentUser();
$branchName = $currentUser ? $currentUser['nama_cabang'] : '';
$branchTheme = '';

if ($currentUser) {
    if ($currentUser['cabang_id'] == CABANG_TASIK) {
        $branchTheme = 'tasik';
    } elseif ($currentUser['cabang_id'] == CABANG_GARUT) {
        $branchTheme = 'garut';
    }
}

// Get dashboard statistics
$stats = getDashboardStats(CABANG_GARUT);

// Get recent products with low stock
$pdo = getConnection();
$stmt = $pdo->prepare("SELECT nama_produk, stok_garut, satuan FROM produk WHERE status = 'aktif' AND stok_garut < :limit ORDER BY stok_garut ASC LIMIT 5");
$stmt->execute(['limit' => 5]);
$lowStockProducts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> | <?= APP_NAME ?> - <?= $branchName ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: <?= $branchTheme == 'tasik' ? '#28a745' : '#007bff' ?>;
            --primary-dark: <?= $branchTheme == 'tasik' ? '#1e7e34' : '#0056b3' ?>;
            --primary-light: <?= $branchTheme == 'tasik' ? '#d4edda' : '#cce7ff' ?>;
        }
        .navbar-brand { font-weight: bold; color: var(--primary-color) !important; }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background-color: var(--primary-dark); border-color: var(--primary-dark); }
        .card-header.bg-primary { background-color: var(--primary-color) !important; }
        .text-primary { color: var(--primary-color) !important; }
        .border-primary { border-color: var(--primary-color) !important; }
        .sidebar { background-color: #f8f9fa; min-height: calc(100vh - 56px); border-right: 1px solid #dee2e6; }
        .sidebar .nav-link { color: #495057; padding: 0.75rem 1rem; border-radius: 0.375rem; margin-bottom: 0.25rem; }
        .sidebar .nav-link:hover { background-color: var(--primary-light); color: var(--primary-dark); }
        .sidebar .nav-link.active { background-color: var(--primary-color); color: white; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php"><i class="fas fa-seedling"></i> <?= APP_NAME ?></a>
        <span class="badge bg-secondary fs-6"><?= $branchName ?></span>
        <div class="navbar-nav ms-auto">
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <i class="fas fa-user"></i> <?= $currentUser['nama_lengkap'] ?? 'User' ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-3 col-lg-2 d-md-block sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':'' ?>" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link <?= strpos($_SERVER['PHP_SELF'],'/produk/')!==false?'active':'' ?>" href="produk/"><i class="fas fa-box"></i> Kelola Produk</a></li>
                    <li class="nav-item"><a class="nav-link <?= strpos($_SERVER['PHP_SELF'],'/kategori/')!==false?'active':'' ?>" href="kategori/"><i class="fas fa-tags"></i> Kelola Kategori</a></li>
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="pt-3">
              <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                  <h1 class="h2"><i class="fas fa-tachometer-alt"></i> Dashboard Garut</h1>
                  <button class="btn btn-sm btn-outline-secondary">
                      <i class="fas fa-calendar"></i> <?= date('d F Y') ?>
                  </button>
              </div>

              <div class="row mb-4">
                  <?php
                  $cards = [
                      ['title'=>'Total Produk','value'=>$stats['total_products'],'icon'=>'fa-box','class'=>'primary'],
                      ['title'=>'Total Stok','value'=>$stats['total_stock'],'icon'=>'fa-boxes','class'=>'success'],
                      ['title'=>'Stok Menipis','value'=>$stats['low_stock'],'icon'=>'fa-exclamation-triangle','class'=>'warning'],
                      ['title'=>'Kategori','value'=>$stats['total_categories'],'icon'=>'fa-tags','class'=>'info']
                  ];
                  foreach ($cards as $c): ?>
                  <div class="col-xl-3 col-md-6 mb-4">
                      <div class="card border-<?= $c['class'] ?> border-2 h-100">
                          <div class="card-body">
                              <div class="row align-items-center">
                                  <div class="col">
                                      <div class="text-xs font-weight-bold text-<?= $c['class'] ?> text-uppercase mb-1"><?= $c['title'] ?></div>
                                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($c['value'] ?? 0) ?></div>
                                  </div>
                                  <div class="col-auto"><i class="fas <?= $c['icon'] ?> fa-2x text-<?= $c['class'] ?>"></i></div>
                              </div>
                          </div>
                      </div>
                  </div>
                  <?php endforeach; ?>
              </div>

              <div class="row mb-4">
                  <div class="col-12">
                      <div class="card">
                          <div class="card-header bg-primary text-white"><h5><i class="fas fa-bolt"></i> Aksi Cepat</h5></div>
                          <div class="card-body">
                              <div class="row">
                                  <div class="col-lg-3 col-md-6 mb-3"><a href="produk/tambah.php" class="btn btn-success btn-lg w-100"><i class="fas fa-plus-circle"></i><br><small>Tambah Produk</small></a></div>
                                  <div class="col-lg-3 col-md-6 mb-3"><a href="produk/" class="btn btn-primary btn-lg w-100"><i class="fas fa-list"></i><br><small>Lihat Semua Produk</small></a></div>
                                  <div class="col-lg-3 col-md-6 mb-3"><a href="kategori/" class="btn btn-info btn-lg w-100"><i class="fas fa-tags"></i><br><small>Kelola Kategori</small></a></div>
                                  <div class="col-lg-3 col-md-6 mb-3"><a href="../../public/" target="_blank" class="btn btn-secondary btn-lg w-100"><i class="fas fa-eye"></i><br><small>Lihat Halaman Publik</small></a></div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>

              <?php if (!empty($lowStockProducts)): ?>
              <div class="row">
                  <div class="col-12">
                      <div class="card border-warning">
                          <div class="card-header bg-warning text-dark"><h5><i class="fas fa-exclamation-triangle"></i> Produk dengan Stok Menipis (&lt; 5)</h5></div>
                          <div class="card-body">
                              <div class="table-responsive">
                                  <table class="table table-striped table-hover">
                                      <thead><tr><th>Nama Produk</th><th>Stok Tersisa</th><th>Satuan</th><th>Status</th></tr></thead>
                                      <tbody>
                                      <?php foreach($lowStockProducts as $p): ?>
                                          <tr>
                                              <td><?= htmlspecialchars($p['nama_produk']) ?></td>
                                              <td><span class="badge bg-danger"><?= $p['stok_garut'] ?></span></td>
                                              <td><?= htmlspecialchars($p['satuan']) ?></td>
                                              <td><?= $p['stok_garut']==0?'<span class="badge bg-danger">Habis</span>':'<span class="badge bg-warning">Menipis</span>' ?></td>
                                          </tr>
                                      <?php endforeach; ?>
                                      </tbody>
                                  </table>
                              </div>
                              <div class="text-center mt-3"><a href="produk/" class="btn btn-warning"><i class="fas fa-boxes"></i> Kelola Stok Produk</a></div>
                          </div>
                      </div>
                  </div>
              </div>
              <?php endif; ?>

            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
