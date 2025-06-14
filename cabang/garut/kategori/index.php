<?php
/**
 * Category management page for Garut branch
 */

require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';

// Require login and check branch access
requireLogin(CABANG_GARUT);

$pageTitle = 'Kelola Kategori';

$message = '';
$messageType = '';

// Handle form submission for adding category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $nama_kategori = sanitize($_POST['nama_kategori']);
    $deskripsi = sanitize($_POST['deskripsi']);
    
    if (!empty($nama_kategori)) {
        try {
            $pdo = getConnection();
            $stmt = $pdo->prepare("INSERT INTO kategori (nama_kategori, deskripsi) VALUES (?, ?)");
            $stmt->execute([$nama_kategori, $deskripsi]);
            
            $message = 'Kategori berhasil ditambahkan!';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Gagal menambahkan kategori: ' . $e->getMessage();
            $messageType = 'danger';
        }
    } else {
        $message = 'Nama kategori harus diisi!';
        $messageType = 'danger';
    }
}

// Handle delete category
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $pdo = getConnection();
        // Check if category is used by products
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM produk WHERE kategori_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetch()['count'];
        
        if ($count > 0) {
            $message = 'Kategori tidak dapat dihapus karena masih digunakan oleh ' . $count . ' produk!';
            $messageType = 'warning';
        } else {
            $stmt = $pdo->prepare("DELETE FROM kategori WHERE id = ?");
            $stmt->execute([$id]);
            $message = 'Kategori berhasil dihapus!';
            $messageType = 'success';
        }
    } catch (Exception $e) {
        $message = 'Gagal menghapus kategori: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

// Get all categories
$categories = getCategories();

include __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-tags"></i> Kelola Kategori - Garut</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-plus"></i> Tambah Kategori
        </button>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
        <i class="fas fa-<?= $messageType == 'success' ? 'check-circle' : ($messageType == 'warning' ? 'exclamation-triangle' : 'exclamation-triangle') ?>"></i>
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Kategori (<?= count($categories) ?> kategori)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($categories)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-tags" style="font-size: 4rem; color: #6c757d;"></i>
                        <h4 class="mt-3 text-muted">Belum ada kategori</h4>
                        <p class="text-muted">Tambahkan kategori pertama untuk mengorganisir produk</p>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="fas fa-plus"></i> Tambah Kategori Pertama
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah Produk</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <?php
                                    // Get product count for this category
                                    $pdo = getConnection();
                                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM produk WHERE kategori_id = ? AND status = 'aktif'");
                                    $stmt->execute([$category['id']]);
                                    $productCount = $stmt->fetch()['count'];
                                    ?>
                                    <tr>
                                        <td><?= $category['id'] ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($category['nama_kategori']) ?></strong>
                                        </td>
                                        <td>
                                            <?= $category['deskripsi'] ? htmlspecialchars($category['deskripsi']) : '<span class="text-muted">-</span>' ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= $productCount ?> produk</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y', strtotime($category['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editCategoryModal<?= $category['id'] ?>" 
                                                        title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if ($productCount == 0): ?>
                                                    <a href="?delete=<?= $category['id'] ?>" 
                                                       class="btn btn-outline-danger" 
                                                       onclick="return confirm('Yakin ingin menghapus kategori ini?')" 
                                                       title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <button type="button" class="btn btn-outline-secondary" 
                                                            title="Tidak dapat dihapus (masih digunakan)" disabled>
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label">Nama Kategori *</label>
                        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" 
                                  placeholder="Deskripsi kategori (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>