<?php
/**
 * Product management page for Garut branch
 */

require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/functions.php';

// Require login and check branch access
requireLogin(CABANG_GARUT);

$pageTitle = 'Kelola Produk';

// Get filter parameters
$kategori_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : null;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : null;

// Get data
$categories = getCategories();
$products = getProducts(CABANG_GARUT, $kategori_id, $search);

include __DIR__ . '/../../../includes/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-boxes"></i> Kelola Produk - Garut</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="tambah.php" class="btn btn-success">
                <i class="fas fa-plus"></i> Tambah Produk
            </a>
        </div>
    </div>
</div>

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

<!-- Products Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Daftar Produk (<?= count($products) ?> produk)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open" style="font-size: 4rem; color: #6c757d;"></i>
                <h4 class="mt-3 text-muted">Tidak ada produk ditemukan</h4>
                <p class="text-muted">Belum ada produk atau coba ubah filter pencarian</p>
                <a href="tambah.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Tambah Produk Pertama
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Jenis Durian</th>
                            <th>Harga</th>
                            <th>Stok Garut</th>
                            <th>Satuan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <?php if ($product['gambar'] && file_exists("../../../public/assets/images/products/" . $product['gambar'])): ?>
                                        <img src="../../../public/assets/images/products/<?= $product['gambar'] ?>" 
                                             class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px; border-radius: 4px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($product['nama_produk']) ?></strong>
                                    <?php if ($product['deskripsi']): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars(substr($product['deskripsi'], 0, 50)) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($product['nama_kategori']): ?>
                                        <span class="badge bg-info"><?= htmlspecialchars($product['nama_kategori']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($product['nama_jenis']): ?>
                                        <span class="badge bg-warning text-dark"><?= htmlspecialchars($product['nama_jenis']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= formatRupiah($product['harga']) ?></td>
                                <td>
                                    <span class="badge <?= $product['stok'] < 5 ? 'bg-danger' : ($product['stok'] < 10 ? 'bg-warning' : 'bg-success') ?>">
                                        <?= $product['stok'] ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($product['satuan']) ?></td>
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
                                    <div class="btn-group btn-group-sm">
                                        <a href="edit.php?id=<?= $product['id'] ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="hapus.php?id=<?= $product['id'] ?>" class="btn btn-outline-danger" 
                                           onclick="return confirm('Yakin ingin menghapus produk ini?')" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
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
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>