<?php
/**
 * Add new product page for Tasikmalaya branch
 */

require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/report_functions_simple.php';

// Require login and check branch access
requireLogin(CABANG_TASIK);

$pageTitle = 'Tambah Produk';

$message = '';
$messageType = '';

// Get categories and jenis durian
$categories = getCategories();
$jenisDurian = getJenisDurian();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'nama_produk' => sanitize($_POST['nama_produk']),
        'kategori_id' => !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null,
        'jenis_durian_id' => !empty($_POST['jenis_durian_id']) ? (int)$_POST['jenis_durian_id'] : null,
        'harga' => (float)$_POST['harga'],
        'harga_per_kg' => (float)($_POST['harga_per_kg'] ?? ($_POST['harga'] / 2.5)), // Estimate if not provided
        'stok_tasik' => (int)$_POST['stok_tasik'],
        'stok_garut' => 0, // Default 0 for Tasik admin
        'total_kg_tasik' => (float)($_POST['total_kg_tasik'] ?? ($_POST['stok_tasik'] * 2.5)), // Estimate if not provided
        'total_kg_garut' => 0, // Default 0 for Tasik admin
        'total_pcs_tasik' => (int)$_POST['stok_tasik'], // Same as stok_tasik
        'total_pcs_garut' => 0, // Default 0 for Tasik admin
        'satuan' => sanitize($_POST['satuan']),
        'deskripsi' => sanitize($_POST['deskripsi'])
    ];
    
    // Handle file upload
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
        try {
            $data['gambar'] = handleFileUpload($_FILES['gambar']);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageType = 'danger';
        }
    }
    
    if (empty($message)) {
        $result = addProduct($data);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
        
        // Log stok awal jika produk berhasil ditambahkan
        if ($result['success'] && $data['stok_tasik'] > 0) {
            $produk_id = $result['product_id']; // Asumsi addProduct mengembalikan ID produk
            $keterangan = "Stok awal produk: {$data['nama_produk']}";
            logStokHistory($produk_id, 'tasik', 'masuk', 0, $data['stok_tasik'], $keterangan, $_SESSION['user_id']);
        }
        
        if ($result['success']) {
            header("Location: index.php");
            exit();
        }
    }
}

include __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-plus-circle"></i> Tambah Produk Baru</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
        <i class="fas fa-<?= $messageType == 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informasi Produk</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_produk" class="form-label">Nama Produk *</label>
                                <input type="text" class="form-control" id="nama_produk" name="nama_produk" 
                                       value="<?= isset($_POST['nama_produk']) ? htmlspecialchars($_POST['nama_produk']) : '' ?>" 
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kategori_id" class="form-label">Kategori</label>
                                <select class="form-select" id="kategori_id" name="kategori_id">
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" 
                                                <?= isset($_POST['kategori_id']) && $_POST['kategori_id'] == $category['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['nama_kategori']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenis_durian_id" class="form-label">Jenis Durian</label>
                                <select class="form-select" id="jenis_durian_id" name="jenis_durian_id">
                                    <option value="">Pilih Jenis Durian</option>
                                    <?php foreach ($jenisDurian as $jenis): ?>
                                        <option value="<?= $jenis['id'] ?>" 
                                                <?= isset($_POST['jenis_durian_id']) && $_POST['jenis_durian_id'] == $jenis['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($jenis['nama_jenis']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="satuan" class="form-label">Satuan *</label>
                                <select class="form-select" id="satuan" name="satuan" required>
                                    <option value="">Pilih Satuan</option>
                                    <option value="kg" <?= isset($_POST['satuan']) && $_POST['satuan'] == 'kg' ? 'selected' : '' ?>>Kilogram (kg)</option>
                                    <option value="pcs" <?= isset($_POST['satuan']) && $_POST['satuan'] == 'pcs' ? 'selected' : '' ?>>Pieces (pcs)</option>
                                    <option value="box" <?= isset($_POST['satuan']) && $_POST['satuan'] == 'box' ? 'selected' : '' ?>>Box</option>
                                    <option value="cup" <?= isset($_POST['satuan']) && $_POST['satuan'] == 'cup' ? 'selected' : '' ?>>Cup</option>
                                    <option value="gelas" <?= isset($_POST['satuan']) && $_POST['satuan'] == 'gelas' ? 'selected' : '' ?>>Gelas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga Satuan (Rp) *</label>
                                <input type="number" class="form-control" id="harga" name="harga" 
                                       value="<?= isset($_POST['harga']) ? $_POST['harga'] : '' ?>" 
                                       min="0" step="100" required>
                                <div class="form-text">Harga per satuan produk</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="harga_per_kg" class="form-label">Harga per Kg (Rp) *</label>
                                <input type="number" class="form-control" id="harga_per_kg" name="harga_per_kg" 
                                       value="<?= isset($_POST['harga_per_kg']) ? $_POST['harga_per_kg'] : '' ?>" 
                                       min="0" step="100" required>
                                <div class="form-text">Harga per kilogram</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stok_tasik" class="form-label">Stok Awal (pcs) *</label>
                                <input type="number" class="form-control" id="stok_tasik" name="stok_tasik" 
                                       value="<?= isset($_POST['stok_tasik']) ? $_POST['stok_tasik'] : '0' ?>" 
                                       min="0" required>
                                <div class="form-text">Jumlah buah/pieces</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="total_kg_tasik" class="form-label">Bobot Total (kg) *</label>
                                <input type="number" class="form-control" id="total_kg_tasik" name="total_kg_tasik" 
                                       value="<?= isset($_POST['total_kg_tasik']) ? $_POST['total_kg_tasik'] : '0' ?>" 
                                       step="0.001" min="0" required>
                                <div class="form-text">Total berat dalam kilogram</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Rata-rata Bobot per Buah</label>
                                <div class="form-control-plaintext" id="avg_weight_display_tasik">
                                    <span class="text-muted">0.000 kg/buah</span>
                                </div>
                                <div class="form-text">Otomatis dihitung dari total kg ÷ jumlah pcs</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" 
                                  placeholder="Deskripsi produk..."><?= isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : '' ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Gambar Produk</label>
                        <input type="file" class="form-control" id="gambar" name="gambar" 
                               accept="image/jpeg,image/jpg,image/png,image/gif">
                        <div class="form-text">Format: JPG, JPEG, PNG, GIF. Maksimal 2MB.</div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Tips Menambah Produk</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-lightbulb text-warning"></i>
                        <small>Gunakan nama produk yang jelas dan mudah dipahami</small>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-image text-info"></i>
                        <small>Upload gambar berkualitas baik untuk menarik pelanggan</small>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-tag text-success"></i>
                        <small>Pilih kategori yang sesuai untuk memudahkan pencarian</small>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-money-bill text-primary"></i>
                        <small>Pastikan harga sudah sesuai dengan kualitas produk</small>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Calculate average weight for Tasik
        function calculateAverageWeightTasik() {
            const totalKg = parseFloat(document.getElementById('total_kg_tasik').value) || 0;
            const totalPcs = parseInt(document.getElementById('stok_tasik').value) || 0;
            
            const avgWeight = totalPcs > 0 ? totalKg / totalPcs : 0;
            document.getElementById('avg_weight_display_tasik').innerHTML = 
                `<span class="text-${avgWeight > 0 ? 'success' : 'muted'}">${avgWeight.toFixed(3)} kg/buah</span>`;
        }
        
        // Add event listeners
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('total_kg_tasik').addEventListener('input', calculateAverageWeightTasik);
            document.getElementById('stok_tasik').addEventListener('input', calculateAverageWeightTasik);
            
            // Calculate initial average
            calculateAverageWeightTasik();
        });
    </script>
</body>
</html>