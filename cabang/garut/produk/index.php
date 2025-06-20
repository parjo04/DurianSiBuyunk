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
            <!-- Mobile/Tablet View -->
            <div class="d-md-none">
                <?php foreach ($products as $product): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <?php if ($product['gambar'] && file_exists("../../../public/assets/images/products/" . $product['gambar'])): ?>
                                        <img src="../../../public/assets/images/products/<?= $product['gambar'] ?>" 
                                             class="img-thumbnail w-100" style="height: 60px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 60px; border-radius: 4px;">
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
                                        <?php if ($product['nama_jenis']): ?>
                                            <span class="badge bg-warning text-dark text-xs"><?= htmlspecialchars($product['nama_jenis']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <small class="text-muted">Harga/kg:</small><br>
                                            <?php if ($product['harga_per_kg']): ?>
                                                <strong><?= formatRupiah($product['harga_per_kg']) ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Satuan:</small><br>
                                            <span class="badge bg-secondary"><?= htmlspecialchars($product['satuan'] ?: 'kg') ?></span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Status:</small><br>
                                            <span class="badge <?= $product['status'] === 'aktif' ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= ucfirst($product['status']) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a href="edit.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="../manajemen-stok/index.php" class="btn btn-sm btn-info">
                                            <i class="fas fa-boxes"></i> Kelola Stok
                                        </a>
                                        <a href="hapus.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Desktop View -->
            <div class="d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 60px;">Gambar</th>
                                <th style="min-width: 200px;">Nama Produk</th>
                                <th style="width: 120px;">Kategori</th>
                                <th style="width: 120px;">Jenis</th>
                                <th style="width: 130px;">Harga/kg</th>
                                <th style="width: 100px;">Satuan</th>
                                <th style="width: 80px;">Status</th>
                                <th style="width: 160px;">Aksi</th>
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
                                <td>
                                    <?php if ($product['harga_per_kg']): ?>
                                        <span class="text-primary fw-bold"><?= formatRupiah($product['harga_per_kg']) ?></span>
                                        <br><small class="text-muted">per kg</small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($product['satuan'] ?: 'kg') ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-success">Aktif</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="edit.php?id=<?= $product['id'] ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="../manajemen-stok/index.php" class="btn btn-outline-info" title="Kelola Stok">
                                            <i class="fas fa-boxes"></i>
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
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Stock Update Modal -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-labelledby="stockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockModalLabel">
                    <i class="fas fa-cubes"></i> Update Stok Produk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="stockForm">
                <div class="modal-body">
                    <input type="hidden" id="produk_id" name="produk_id">
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Produk:</strong></label>
                        <p id="produk_nama" class="text-muted"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Stok Saat Ini:</label>
                        <div class="row">
                            <div class="col-6">
                                <p id="stok_lama_pcs" class="fw-bold text-primary">0 pcs</p>
                            </div>
                            <div class="col-6">
                                <p id="stok_lama_kg" class="fw-bold text-success">0.000 kg</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="bobot_kg" class="form-label">Bobot (kg) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="bobot_kg" name="bobot_kg" 
                                       step="0.001" min="0" required>
                                <div class="form-text">Bobot total dalam kilogram</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="jumlah_pcs" class="form-label">Jumlah (pcs) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="jumlah_pcs" name="jumlah_pcs" 
                                       min="1" required>
                                <div class="form-text">Jumlah buah/pieces</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="operation" id="operation_add" value="add" checked>
                            <label class="form-check-label" for="operation_add">
                                <i class="fas fa-plus text-success"></i> Tambah Stok
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="operation" id="operation_reduce" value="reduce">
                            <label class="form-check-label" for="operation_reduce">
                                <i class="fas fa-minus text-danger"></i> Kurangi Stok
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" 
                                  rows="3" placeholder="Alasan perubahan stok (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Stok
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
    
    <script>
        function openStockModal(produkId, namaProduk, stokLama) {
            document.getElementById('produk_id').value = produkId;
            document.getElementById('produk_nama').textContent = namaProduk;
            
            // Fetch current stock with weight data
            fetch('get_stock_info.php', {
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
                    document.getElementById('stok_lama_kg').textContent = '0.000 kg';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('stok_lama_pcs').textContent = `${stokLama} pcs`;
                document.getElementById('stok_lama_kg').textContent = '0.000 kg';
            });
            
            // Reset form
            document.getElementById('bobot_kg').value = '';
            document.getElementById('jumlah_pcs').value = '';
            document.getElementById('keterangan').value = '';
            document.getElementById('operation_add').checked = true;
            
            const modal = new bootstrap.Modal(document.getElementById('stockModal'));
            modal.show();
        }
        
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            const stockForm = document.getElementById('stockForm');
            if (stockForm) {
                stockForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    
                    // Disable button and show loading
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                    
                    fetch('update_stok.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            alert('Stok berhasil diperbarui!');
                            
                            // Close modal
                            bootstrap.Modal.getInstance(document.getElementById('stockModal')).hide();
                            
                            // Reload page to show updated data
                            window.location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat memperbarui stok');
                    })
                    .finally(() => {
                        // Re-enable button
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
                });
            }
        });
    </script>
</body>
</html>