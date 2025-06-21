<?php
/**
 * Stock Management page for Garut branch
 */

require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/weight_functions.php';

// Require login and check branch access
requireLogin(CABANG_GARUT);

$pageTitle = 'Manajemen Stok';

// Handle stock transaction
$message = '';
$messageType = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_stock') {
        $produk_id = (int)$_POST['produk_id'];
        $operation = $_POST['operation'];
        $bobot_kg = (float)$_POST['bobot_kg'];
        $jumlah_pcs = (int)$_POST['jumlah_pcs'];
        $keterangan = sanitize($_POST['keterangan']);
        $bukti_file = null;
        
        // Handle file upload for receipt/invoice
        if (isset($_FILES['bukti_transaksi']) && $_FILES['bukti_transaksi']['error'] == UPLOAD_ERR_OK) {
            try {
                $uploadDir = __DIR__ . '/../../../public/assets/receipts/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
                $fileExtension = strtolower(pathinfo($_FILES['bukti_transaksi']['name'], PATHINFO_EXTENSION));
                
                if (in_array($fileExtension, $allowedTypes)) {
                    $fileName = 'receipt_' . time() . '_' . $produk_id . '.' . $fileExtension;
                    $targetPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['bukti_transaksi']['tmp_name'], $targetPath)) {
                        $bukti_file = $fileName;
                    }
                }
            } catch (Exception $e) {
                // Handle upload error silently
            }
        }
        
        // Add receipt info to keterangan if uploaded
        if ($bukti_file) {
            $keterangan .= " [Bukti: " . $bukti_file . "]";
        }
        
        if ($operation === 'add') {
            $result = addStockWithWeight($produk_id, 'garut', $bobot_kg, $jumlah_pcs, $keterangan, $_SESSION['user']['id']);
        } else {
            $result = reduceStockWithWeight($produk_id, 'garut', $bobot_kg, $jumlah_pcs, $keterangan, $_SESSION['user']['id']);
        }
        
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
    }
}

// Get filter parameters
$kategori_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : null;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : null;

// Get data
$categories = getCategories();
$products = getProducts(CABANG_GARUT, $kategori_id, $search);

include __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-warehouse"></i> Manajemen Stok - Garut</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="../produk/" class="btn btn-outline-primary">
                <i class="fas fa-boxes"></i> Kelola Produk
            </a>
        </div>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-6">
                <label for="search" class="form-label">Cari Produk</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Nama produk atau deskripsi..." 
                       value="<?= htmlspecialchars($search ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label for="kategori" class="form-label">Filter Kategori</label>
                <select class="form-select" id="kategori" name="kategori">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $kategori_id == $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Stock Management Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-cubes"></i> Daftar Produk untuk Manajemen Stok (<?= count($products) ?> produk)
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open" style="font-size: 4rem; color: #6c757d;"></i>
                <h4 class="mt-3 text-muted">Tidak ada produk ditemukan</h4>
                <p class="text-muted">Belum ada produk atau coba ubah filter pencarian</p>
                <a href="../produk/tambah.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Tambah Produk Pertama
                </a>
            </div>
        <?php else: ?>
            <!-- Mobile/Tablet View -->
            <div class="d-lg-none">
                <?php foreach ($products as $product): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <?php 
                                    $imagePath = getImagePath($product['gambar']);
                                    if ($imagePath && file_exists("../../../" . $imagePath)): ?>
                                        <img src="../../../<?= $imagePath ?>?v=<?= time() ?>" 
                                             class="img-thumbnail w-100" style="height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 50px; border-radius: 4px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-9">
                                    <h6 class="card-title mb-1"><?= htmlspecialchars($product['nama_produk']) ?></h6>
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        <?php if ($product['nama_kategori']): ?>
                                            <span class="badge bg-info text-xs"><?= htmlspecialchars($product['nama_kategori']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <small class="text-muted">Stok:</small><br>
                                            <span class="badge <?= $product['stok'] < 5 ? 'bg-danger' : ($product['stok'] < 10 ? 'bg-warning' : 'bg-success') ?>">
                                                <?= $product['stok'] ?> pcs
                                            </span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Bobot:</small><br>
                                            <span class="text-success fw-bold"><?= number_format($product['total_kg_garut'], 2) ?> kg</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Harga/kg:</small><br>
                                            <?php if ($product['harga_per_kg']): ?>
                                                <span class="text-primary"><?= formatRupiah($product['harga_per_kg']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Status:</small><br>
                                            <?php if ($product['stok'] == 0): ?>
                                                <span class="badge bg-danger">Habis</span>
                                            <?php elseif ($product['stok'] < 5): ?>
                                                <span class="badge bg-warning">Menipis</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Tersedia</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                onclick="openStockModal(<?= $product['id'] ?>, '<?= htmlspecialchars($product['nama_produk']) ?>', <?= $product['stok'] ?>, <?= $product['total_kg_garut'] ?>)">
                                            <i class="fas fa-warehouse"></i> Kelola
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Desktop View -->
            <div class="d-none d-lg-block">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th style="min-width: 250px;">Produk</th>
                                <th style="width: 120px;">Stok Saat Ini</th>
                                <th style="width: 120px;">Bobot Total</th>
                                <th style="width: 130px;">Harga/kg</th>
                                <th style="width: 140px;">Nilai Stok</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php 
                                        $imagePath = getImagePath($product['gambar']);
                                        if ($imagePath && file_exists("../../../" . $imagePath)): ?>
                                            <img src="../../../<?= $imagePath ?>?v=<?= time() ?>" 
                                                 class="img-thumbnail me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 60px; height: 60px; border-radius: 8px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?= htmlspecialchars($product['nama_produk']) ?></strong>
                                            <?php if ($product['nama_kategori']): ?>
                                                <br><span class="badge bg-info"><?= htmlspecialchars($product['nama_kategori']) ?></span>
                                            <?php endif; ?>
                                            <?php if ($product['nama_jenis']): ?>
                                                <span class="badge bg-warning text-dark"><?= htmlspecialchars($product['nama_jenis']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="h5 <?= $product['stok'] < 5 ? 'text-danger' : ($product['stok'] < 10 ? 'text-warning' : 'text-success') ?>">
                                        <?= $product['stok'] ?> pcs
                                    </span>
                                </td>
                                <td>
                                    <span class="h6 text-success">
                                        <?= number_format($product['total_kg_garut'], 3) ?> kg
                                    </span>
                                    <?php if ($product['stok'] > 0): ?>
                                        <br><small class="text-muted">
                                            ~<?= number_format($product['total_kg_garut'] / $product['stok'], 3) ?> kg/pcs
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($product['harga_per_kg']): ?>
                                        <span class="text-primary fw-bold">
                                            <?= formatRupiah($product['harga_per_kg']) ?>/kg
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($product['harga_per_kg'] && $product['total_kg_garut']): ?>
                                        <span class="fw-bold text-dark">
                                            <?= formatRupiah($product['harga_per_kg'] * $product['total_kg_garut']) ?>
                                        </span>
                                        <br><small class="text-muted">Estimasi nilai</small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($product['stok'] == 0): ?>
                                        <span class="badge bg-danger">Habis</span>
                                    <?php elseif ($product['stok'] < 5): ?>
                                        <span class="badge bg-warning">Menipis</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Tersedia</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm" 
                                            onclick="openStockModal(<?= $product['id'] ?>, '<?= htmlspecialchars($product['nama_produk']) ?>', <?= $product['stok'] ?>, <?= $product['total_kg_garut'] ?>)" 
                                            title="Kelola Stok">
                                        <i class="fas fa-warehouse"></i> Kelola
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Stock Management Modal -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockModalLabel">
                    <i class="fas fa-warehouse"></i> Manajemen Stok Produk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_stock">
                    <input type="hidden" id="produk_id" name="produk_id">
                    
                    <div class="alert alert-info">
                        <strong>Produk:</strong> <span id="produk_nama"></span>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Stok Saat Ini:</strong></label>
                            <div class="border rounded p-3 bg-light">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="h5 text-primary mb-0" id="stok_lama_pcs">0 pcs</div>
                                        <small class="text-muted">Pieces</small>
                                    </div>
                                    <div class="col-6">
                                        <div class="h5 text-success mb-0" id="stok_lama_kg">0.000 kg</div>
                                        <small class="text-muted">Kilogram</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Operasi:</strong></label>
                            <div class="border rounded p-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="operation" id="operation_add" value="add" checked>
                                    <label class="form-check-label" for="operation_add">
                                        <i class="fas fa-plus text-success"></i> <strong>Tambah Stok</strong>
                                        <br><small class="text-muted">Stok masuk / Pembelian</small>
                                    </label>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="radio" name="operation" id="operation_reduce" value="reduce">
                                    <label class="form-check-label" for="operation_reduce">
                                        <i class="fas fa-minus text-danger"></i> <strong>Kurangi Stok</strong>
                                        <br><small class="text-muted">Stok keluar / Penjualan</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="bobot_kg" class="form-label">Bobot (kg) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="bobot_kg" name="bobot_kg" 
                                   step="0.001" min="0" required>
                            <div class="form-text">Bobot total dalam kilogram</div>
                        </div>
                        <div class="col-md-6">
                            <label for="jumlah_pcs" class="form-label">Jumlah (pcs) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="jumlah_pcs" name="jumlah_pcs" 
                                   min="1" required>
                            <div class="form-text">Jumlah buah/pieces</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="2" 
                                  placeholder="Catatan transaksi (opsional)"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bukti_transaksi" class="form-label">
                            <i class="fas fa-receipt"></i> Bukti Transaksi (Opsional)
                        </label>
                        <input type="file" class="form-control" id="bukti_transaksi" name="bukti_transaksi" 
                               accept="image/*,.pdf">
                        <div class="form-text">
                            Upload nota, invoice, atau bukti transaksi lainnya. Format: JPG, PNG, PDF. Maksimal 5MB.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openStockModal(produkId, namaProduk, stokLama, bobotLama) {
    document.getElementById('produk_id').value = produkId;
    document.getElementById('produk_nama').textContent = namaProduk;
    
    // Fetch current stock with weight data
    fetch('../produk/get_stock_info.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ produk_id: produkId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('stok_lama_pcs').textContent = `${data.total_pcs} pcs`;
            document.getElementById('stok_lama_kg').textContent = `${data.total_kg} kg`;
        } else {
            document.getElementById('stok_lama_pcs').textContent = `${stokLama} pcs`;
            document.getElementById('stok_lama_kg').textContent = `${bobotLama.toFixed(3)} kg`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('stok_lama_pcs').textContent = `${stokLama} pcs`;
        document.getElementById('stok_lama_kg').textContent = `${bobotLama.toFixed(3)} kg`;
    });
    
    // Reset form
    document.getElementById('bobot_kg').value = '';
    document.getElementById('jumlah_pcs').value = '';
    document.getElementById('keterangan').value = '';
    document.getElementById('bukti_transaksi').value = '';
    document.getElementById('operation_add').checked = true;
    
    // Use Bootstrap 5 modal method
    const modalElement = document.getElementById('stockModal');
    const modal = new window.bootstrap.Modal(modalElement);
    modal.show();
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
