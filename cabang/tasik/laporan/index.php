<?php
/**
 * Simple Reports page for Tasikmalaya branch
 */

require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/report_functions_simple.php';

// Require login and check branch access
requireLogin(CABANG_TASIK);

$pageTitle = 'Laporan Stok';

// Get filter parameters
$date_from = $_GET['date_from'] ?? date('Y-m-01'); // Start of month
$date_to = $_GET['date_to'] ?? date('Y-m-d'); // Today
$report_type = $_GET['report_type'] ?? 'summary';

// Generate report data
try {
    $summary = generateReportSummary('tasik');
    $current_stock = getCurrentStockReport('tasik');
    $stock_movements = getStockMovementHistory('tasik', $date_from, $date_to, 20);
} catch (Exception $e) {
    $error_message = "Error loading report: " . $e->getMessage();
}

include __DIR__ . '/../../../includes/header.php';
?>

<style>
@media print {
    /* Hide everything except the printable content */
    .navbar, 
    .sidebar, 
    nav.sidebar,
    .col-md-3.col-lg-2.d-md-block.sidebar,
    .btn-toolbar, 
    .card-body form, 
    .border-bottom,
    .container-fluid .row .col-md-3,
    .container-fluid .row .col-lg-2 {
        display: none !important;
    }
    
    /* Ensure the main content takes full width */
    .container-fluid {
        padding: 0 !important;
        margin: 0 !important;
    }
    
    .container-fluid .row {
        margin: 0 !important;
    }
    
    main.col-md-9.ms-sm-auto.col-lg-10 {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Show only the report content */
    .printable-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    
    /* Adjust margins for print */
    body {
        margin: 0;
        padding: 20px;
        background: white !important;
    }
    
    /* Style summary cards for print */
    .summary-cards .card {
        break-inside: avoid;
        margin-bottom: 10px;
        border: 1px solid #dee2e6 !important;
    }
    
    /* Ensure tables print properly */
    .table {
        font-size: 12px;
    }
    
    .table th, .table td {
        padding: 6px !important;
        border: 1px solid #dee2e6 !important;
    }
    
    /* Hide interactive elements */
    button, .btn, .dropdown, .form-control, .form-select {
        display: none !important;
    }
    
    /* Style headings for print */
    h1, h2, h3, h4, h5 {
        color: black !important;
    }
}

.summary-cards .card {
    transition: transform 0.2s;
}

.summary-cards .card:hover {
    transform: translateY(-2px);
}
</style>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-chart-bar"></i> Laporan Stok - Tasikmalaya</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak
            </button>
        </div>
    </div>
</div>

<div class="printable-content">

<!-- Error Message -->
<?php if (isset($error_message)): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?= htmlspecialchars($error_message) ?>
    </div>
<?php endif; ?>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Jenis Laporan</label>
                <select name="report_type" class="form-select" onchange="this.form.submit()">
                    <option value="summary" <?= $report_type === 'summary' ? 'selected' : '' ?>>Ringkasan Stok</option>
                    <option value="movement" <?= $report_type === 'movement' ? 'selected' : '' ?>>Pergerakan Stok</option>
                    <option value="current" <?= $report_type === 'current' ? 'selected' : '' ?>>Stok Saat Ini</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="<?= $date_from ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="<?= $date_to ?>">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<?php if (isset($summary)): ?>
    <div class="row mb-4 summary-cards">
        <div class="col-lg-2 col-md-4 mb-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="fas fa-boxes fa-2x text-primary mb-2"></i>
                    <h4 class="mb-1"><?= number_format($summary['total_produk']) ?></h4>
                    <p class="mb-0 small">Total Produk</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 mb-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="fas fa-cubes fa-2x text-success mb-2"></i>
                    <h4 class="mb-1"><?= number_format($summary['total_stok_buah']) ?></h4>
                    <p class="mb-0 small">Total Buah</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 mb-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="fas fa-weight fa-2x text-info mb-2"></i>
                    <h4 class="mb-1"><?= number_format($summary['total_stok_kg'], 1) ?></h4>
                    <p class="mb-0 small">Total Kg</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 mb-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1"><?= number_format($summary['produk_menipis']) ?></h4>
                    <p class="mb-0 small">Stok Menipis</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 mb-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                    <h4 class="mb-1"><?= number_format($summary['produk_habis']) ?></h4>
                    <p class="mb-0 small">Stok Habis</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 mb-3">
            <div class="card border-dark">
                <div class="card-body text-center">
                    <i class="fas fa-money-bill-wave fa-2x text-dark mb-2"></i>
                    <h4 class="mb-1"><?= number_format($summary['total_nilai_stok']/1000000, 1) ?>M</h4>
                    <p class="mb-0 small">Nilai Stok</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Report Content -->
<?php if ($report_type === 'movement'): ?>
    <!-- Stock Movement Report -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-exchange-alt"></i> Pergerakan Stok (<?= date('d/m/Y', strtotime($date_from)) ?> - <?= date('d/m/Y', strtotime($date_to)) ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($stock_movements)): ?>
                <p class="text-muted text-center py-4">Tidak ada pergerakan stok dalam periode ini.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Produk</th>
                                <th>Jenis</th>
                                <th>Sebelum</th>
                                <th>Sesudah</th>
                                <th>Selisih</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stock_movements as $movement): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($movement['created_at'])) ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($movement['nama_produk']) ?></strong>
                                        <?php if ($movement['nama_kategori']): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($movement['nama_kategori']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($movement['jenis_transaksi'] === 'masuk'): ?>
                                            <span class="badge bg-success">Masuk</span>
                                        <?php elseif ($movement['jenis_transaksi'] === 'keluar'): ?>
                                            <span class="badge bg-danger">Keluar</span>
                                        <?php else: ?>
                                            <span class="badge bg-info"><?= ucfirst($movement['jenis_transaksi']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $movement['jumlah_sebelum'] ?></td>
                                    <td><?= $movement['jumlah_sesudah'] ?></td>
                                    <td>
                                        <span class="<?= $movement['selisih'] > 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= $movement['selisih'] > 0 ? '+' : '' ?><?= $movement['selisih'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($movement['keterangan']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php elseif ($report_type === 'current'): ?>
    <!-- Current Stock Report -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-warehouse"></i> Stok Saat Ini</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Stok (pcs)</th>
                            <th>Bobot (kg)</th>
                            <th>Harga/kg</th>
                            <th>Nilai Stok</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($current_stock as $stock): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($stock['nama_produk']) ?></strong></td>
                                <td><?= htmlspecialchars($stock['nama_kategori'] ?? '-') ?></td>
                                <td><?= number_format($stock['stok_pcs']) ?></td>
                                <td><?= number_format($stock['stok_kg'], 3) ?></td>
                                <td><?= $stock['harga_per_kg'] ? formatRupiah($stock['harga_per_kg']) : '-' ?></td>
                                <td>
                                    <?php if ($stock['harga_per_kg'] && $stock['stok_kg']): ?>
                                        <?= formatRupiah($stock['harga_per_kg'] * $stock['stok_kg']) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($stock['status_stok'] === 'Habis'): ?>
                                        <span class="badge bg-danger">Habis</span>
                                    <?php elseif ($stock['status_stok'] === 'Menipis'): ?>
                                        <span class="badge bg-warning">Menipis</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Tersedia</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- Summary Report -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle text-warning"></i> Produk Perlu Perhatian</h6>
                </div>
                <div class="card-body">
                    <?php 
                    $low_stock = array_filter($current_stock, function($item) {
                        return $item['status_stok'] === 'Habis' || $item['status_stok'] === 'Menipis';
                    });
                    ?>
                    <?php if (empty($low_stock)): ?>
                        <p class="text-success mb-0"><i class="fas fa-check"></i> Semua produk memiliki stok yang cukup</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach (array_slice($low_stock, 0, 5) as $item): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= htmlspecialchars($item['nama_produk']) ?></strong>
                                        <span class="badge bg-<?= $item['status_stok'] === 'Habis' ? 'danger' : 'warning' ?>">
                                            <?= $item['stok_pcs'] ?> pcs
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-clock text-info"></i> Aktivitas Terakhir</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($stock_movements)): ?>
                        <p class="text-muted mb-0">Belum ada aktivitas stok</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach (array_slice($stock_movements, 0, 5) as $movement): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong><?= htmlspecialchars($movement['nama_produk']) ?></strong>
                                            <br><small class="text-muted"><?= date('d/m/Y H:i', strtotime($movement['created_at'])) ?></small>
                                        </div>
                                        <span class="badge bg-<?= $movement['jenis_transaksi'] === 'masuk' ? 'success' : 'danger' ?>">
                                            <?= $movement['jenis_transaksi'] === 'masuk' ? '+' : '' ?><?= $movement['selisih'] ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

</div> <!-- End printable-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>