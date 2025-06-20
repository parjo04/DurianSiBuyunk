<?php
/**
 * Edit product page for Garut branch
 */

require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/report_functions_simple.php';

// Require login and check branch access
requireLogin(CABANG_GARUT);

$pageTitle = 'Edit Produk';

$message = '';
$messageType = '';

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

// Get categories and jenis durian
$categories = getCategories();
$jenisDurian = getJenisDurian();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'nama_produk' => sanitize($_POST['nama_produk']),
        'kategori_id' => !empty($_POST['kategori_id']) ? (int)$_POST['kategori_id'] : null,
        'jenis_durian_id' => !empty($_POST['jenis_durian_id']) ? (int)$_POST['jenis_durian_id'] : null,
        'harga' => (float)$_POST['harga'],
        'harga_per_kg' => (float)$_POST['harga'], // Same as main price since default is kg
        'stok_tasik' => $product['stok_tasik'], // Keep existing Tasik stock
        'stok_garut' => (int)$_POST['stok_garut'],
        'total_kg_tasik' => $product['total_kg_tasik'], // Keep existing Tasik weight
        'total_kg_garut' => (float)($_POST['total_kg_garut'] ?? $product['total_kg_garut']),
        'total_pcs_tasik' => $product['total_pcs_tasik'], // Keep existing Tasik pieces
        'total_pcs_garut' => (int)$_POST['stok_garut'], // Update with new stock
        'satuan' => sanitize($_POST['satuan']),
        'deskripsi' => sanitize($_POST['deskripsi']),
        'gambar' => $product['gambar'] // Keep existing image by default
    ];
    
    // Handle file upload
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
        try {
            $data['gambar'] = handleFileUpload($_FILES['gambar'], 'garut');
            // Delete old image if exists
            $oldImagePath = getImagePath($product['gambar']);
            if ($product['gambar'] && $oldImagePath) {
                $fullOldPath = __DIR__ . "/../../../" . $oldImagePath;
                if (file_exists($fullOldPath)) {
                    unlink($fullOldPath);
                }
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            $messageType = 'danger';
        }
    }
    
    if (empty($message)) {
        // Check if stock changed for logging
        $stok_lama = $product['stok_garut'];
        $stok_baru = (int)$_POST['stok_garut'];
        
        $result = updateProduct($id, $data);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'danger';
        
        if ($result['success']) {
            // Log stock change if different
            if ($stok_lama != $stok_baru) {
                $jenis_pergerakan = $stok_baru > $stok_lama ? 'masuk' : 'keluar';
                logStokHistory(
                    $id,
                    'garut',
                    $jenis_pergerakan,
                    $stok_lama,
                    $stok_baru,
                    'Update produk via form edit',
                    $_SESSION['user_id']
                );
            }
            
            header("Location: index.php");
            exit();
        }
    }
}

include __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-edit"></i> Edit Produk</h1>
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
                <h5 class="mb-0">Edit Informasi Produk</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_produk" class="form-label">Nama Produk *</label>
                                <input type="text" class="form-control" id="nama_produk" name="nama_produk" 
                                       value="<?= isset($_POST['nama_produk']) ? htmlspecialchars($_POST['nama_produk']) : htmlspecialchars($product['nama_produk']) ?>" 
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
                                                <?= (isset($_POST['kategori_id']) ? $_POST['kategori_id'] : $product['kategori_id']) == $category['id'] ? 'selected' : '' ?>>
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
                                                <?= (isset($_POST['jenis_durian_id']) ? $_POST['jenis_durian_id'] : $product['jenis_durian_id']) == $jenis['id'] ? 'selected' : '' ?>>
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
                                    <?php 
                                    $satuanOptions = ['kg' => 'Kilogram (kg)', 'pcs' => 'Pieces (pcs)', 'box' => 'Box', 'cup' => 'Cup', 'gelas' => 'Gelas'];
                                    $currentSatuan = isset($_POST['satuan']) ? $_POST['satuan'] : $product['satuan'];
                                    foreach ($satuanOptions as $value => $label): 
                                    ?>
                                        <option value="<?= $value ?>" <?= $currentSatuan == $value ? 'selected' : '' ?>><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="harga" class="form-label">Harga Satuan (Rp) *</label>
                                <input type="number" class="form-control" id="harga" name="harga" 
                                       value="<?= isset($_POST['harga']) ? $_POST['harga'] : $product['harga'] ?>" 
                                       min="0" step="100" required>
                                <div class="form-text">Harga per satuan produk</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="harga_per_kg" class="form-label">Harga per Kg (Rp) *</label>
                                <input type="number" class="form-control" id="harga_per_kg" name="harga_per_kg" 
                                       value="<?= isset($_POST['harga_per_kg']) ? $_POST['harga_per_kg'] : $product['harga_per_kg'] ?>" 
                                       min="0" step="100" required>
                                <div class="form-text">Harga per kilogram</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stok_garut" class="form-label">Stok Garut (pcs) *</label>
                                <input type="number" class="form-control" id="stok_garut" name="stok_garut" 
                                       value="<?= isset($_POST['stok_garut']) ? $_POST['stok_garut'] : $product['stok_garut'] ?>" 
                                       min="0" required>
                                <div class="form-text">Jumlah buah/pieces</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="total_kg_garut" class="form-label">Bobot Total (kg) *</label>
                                <input type="number" class="form-control" id="total_kg_garut" name="total_kg_garut" 
                                       value="<?= isset($_POST['total_kg_garut']) ? $_POST['total_kg_garut'] : $product['total_kg_garut'] ?>" 
                                       step="0.001" min="0" required>
                                <div class="form-text">Total berat dalam kilogram</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Rata-rata Bobot per Buah</label>
                                <div class="form-control-plaintext" id="avg_weight_display_edit">
                                    <span class="text-muted">0.000 kg/buah</span>
                                </div>
                                <div class="form-text">Otomatis dihitung dari total kg ÷ jumlah pcs</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" 
                                  placeholder="Deskripsi produk..."><?= isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : htmlspecialchars($product['deskripsi']) ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Gambar Produk</label>
                        <?php 
                        $imagePath = getImagePath($product['gambar']);
                        if ($imagePath && file_exists("../../../" . $imagePath)): ?>
                            <div class="mb-2">
                                <img src="../../../<?= $imagePath ?>?v=<?= time() ?>" 
                                     class="img-thumbnail" style="max-width: 200px;">
                                <div class="form-text">Gambar saat ini</div>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="gambar" name="gambar" 
                               accept="image/jpeg,image/jpg,image/png,image/gif">
                        <div class="form-text">Format: JPG, JPEG, PNG, GIF. Maksimal 2MB. Kosongkan jika tidak ingin mengubah gambar.</div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Informasi Stok</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <div class="h4 text-success"><?= $product['stok_tasik'] ?></div>
                            <small class="text-muted">Stok Tasik</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <div class="h4 text-primary"><?= $product['stok_garut'] ?></div>
                            <small class="text-muted">Stok Garut</small>
                        </div>
                    </div>
                </div>
                <hr>
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i>
                    Anda hanya dapat mengubah stok Garut. Stok Tasikmalaya dikelola oleh admin Tasikmalaya.
                </small>
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
        // Calculate average weight
        function calculateAverageWeight() {
            const totalKg = parseFloat(document.getElementById('total_kg_garut').value) || 0;
            const totalPcs = parseInt(document.getElementById('stok_garut').value) || 0;
            
            const avgWeight = totalPcs > 0 ? totalKg / totalPcs : 0;
            document.getElementById('avg_weight_display_edit').innerHTML = 
                `<span class="text-${avgWeight > 0 ? 'success' : 'muted'}">${avgWeight.toFixed(3)} kg/buah</span>`;
        }
        
        // Add event listeners
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('total_kg_garut').addEventListener('input', calculateAverageWeight);
            document.getElementById('stok_garut').addEventListener('input', calculateAverageWeight);
            
            // Calculate initial average
            calculateAverageWeight();
        });
    </script>
</body>
</html>