<?php
/**
 * Public product display page for customers
 */

require_once(__DIR__ . '/config/config.php');
require_once(__DIR__ . '/includes/functions.php');


// Get filter parameters
$kategori_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : null;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : null;
$cabang_filter = isset($_GET['cabang']) ? (int)$_GET['cabang'] : null;

// Get data
$categories = getCategories();
$products = getProducts($cabang_filter, $kategori_id, $search);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Katalog Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #28a745;
            --secondary-color: #ffc107;
            --tasik-color: #28a745;
            --garut-color: #007bff;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), #20c997);
            color: white;
            padding: 4rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
        }
        
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
            cursor: pointer;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        
        .product-image {
            height: 200px;
            object-fit: cover;
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #6c757d;
            transition: transform 0.3s ease;
        }
        
        .price-tag {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
        }
        
        .stock-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 10px;
            font-size: 0.75rem;
        }
        
        .branch-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 0.25rem 0.5rem;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: bold;
        }
        
        .branch-tasik {
            background: var(--tasik-color);
            color: white;
        }
        
        .branch-garut {
            background: var(--garut-color);
            color: white;
        }
        
        .category-filter {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .filter-btn {
            border-radius: 20px;
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            transition: all 0.3s ease;
        }
        
        .filter-btn.active {
            background: var(--primary-color);
            color: white;
        }
        
        .admin-login-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            background: linear-gradient(135deg, var(--tasik-color), var(--garut-color));
            border: none;
            color: white;
            padding: 1rem;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .admin-login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
            color: white;
        }
        
        .stock-info {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .stock-item {
            background: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
            border: 1px solid #dee2e6;
        }
        
        .stock-item.tasik {
            border-color: var(--tasik-color);
            color: var(--tasik-color);
        }
        
        .stock-item.garut {
            border-color: var(--garut-color);
            color: var(--garut-color);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Admin Login Button -->
    <a href="/DurianSiBuyunk/public/" class="admin-login-btn text-decoration-none" title="Login Admin">
        <i class="fas fa-user-shield fa-lg"></i>
    </a>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="hero-content">
                        <h1 class="display-4 fw-bold mb-3">
                            <i class="fas fa-seedling"></i> Durian Si Buyunk
                        </h1>
                        <p class="lead mb-4">Durian segar dan olahan durian terbaik dari Tasikmalaya & Garut</p>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <span>Tasikmalaya • Garut</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-phone me-2"></i>
                                <span>0265-123456 • 0262-789012</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-seedling" style="font-size: 8rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container my-5">
        <!-- Search and Filter -->
        <div class="category-filter">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <form method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Cari produk..." 
                               value="<?= htmlspecialchars($search ?? '') ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        <?php if ($kategori_id): ?>
                            <input type="hidden" name="kategori" value="<?= $kategori_id ?>">
                        <?php endif; ?>
                        <?php if ($cabang_filter): ?>
                            <input type="hidden" name="cabang" value="<?= $cabang_filter ?>">
                        <?php endif; ?>
                    </form>
                </div>
                <div class="col-md-8 text-md-end mt-3 mt-md-0">
                    <!-- Branch Filter -->
                    <div class="btn-group me-2" role="group">
                        <a href="?<?= http_build_query(array_filter(['kategori' => $kategori_id, 'search' => $search])) ?>" 
                           class="btn filter-btn <?= !$cabang_filter ? 'active' : 'btn-outline-secondary' ?>">
                            Semua Cabang
                        </a>
                        <a href="?<?= http_build_query(array_filter(['cabang' => CABANG_TASIK, 'kategori' => $kategori_id, 'search' => $search])) ?>" 
                           class="btn filter-btn <?= $cabang_filter == CABANG_TASIK ? 'active' : 'btn-outline-success' ?>">
                            <i class="fas fa-seedling"></i> Tasikmalaya
                        </a>
                        <a href="?<?= http_build_query(array_filter(['cabang' => CABANG_GARUT, 'kategori' => $kategori_id, 'search' => $search])) ?>" 
                           class="btn filter-btn <?= $cabang_filter == CABANG_GARUT ? 'active' : 'btn-outline-primary' ?>">
                            <i class="fas fa-mountain"></i> Garut
                        </a>
                    </div>
                    
                    <!-- Category Filter -->
                    <div class="btn-group" role="group">
                        <a href="?<?= http_build_query(array_filter(['cabang' => $cabang_filter, 'search' => $search])) ?>" 
                           class="btn filter-btn <?= !$kategori_id ? 'active' : 'btn-outline-primary' ?>">
                            Semua
                        </a>
                        <?php foreach ($categories as $category): ?>
                            <a href="?<?= http_build_query(array_filter(['cabang' => $cabang_filter, 'kategori' => $category['id'], 'search' => $search])) ?>" 
                               class="btn filter-btn <?= $kategori_id == $category['id'] ? 'active' : 'btn-outline-primary' ?>">
                                <?= htmlspecialchars($category['nama_kategori']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row">
            <?php if (empty($products)): ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-search" style="font-size: 4rem; color: #6c757d;"></i>
                        <h4 class="mt-3 text-muted">Tidak ada produk ditemukan</h4>
                        <p class="text-muted">Coba ubah kata kunci pencarian atau filter kategori</p>
                        <a href="index.php" class="btn btn-primary">Lihat Semua Produk</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card product-card h-100 position-relative" 
                             style="cursor: pointer;" 
                             onclick="window.location.href='detail.php?id=<?= $product['id'] ?>'">
                            <!-- Branch Badge -->
                            <?php if (!$cabang_filter): ?>
                                <div class="branch-badge branch-tasik">
                                    <i class="fas fa-seedling"></i> Tasik: <?= $product['stok_tasik'] ?>
                                </div>
                                <div class="stock-badge">
                                    <i class="fas fa-mountain"></i> Garut: <?= $product['stok_garut'] ?>
                                </div>
                            <?php else: ?>
                                <div class="stock-badge">
                                    <i class="fas fa-boxes"></i> <?= $product['stok'] ?> <?= htmlspecialchars($product['satuan']) ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="product-image">
                                <?php 
                                $imageUrl = getImageUrl($product['gambar']);
                                if ($imageUrl): ?>
                                    <img src="<?= $imageUrl ?>" 
                                         class="card-img-top product-image" 
                                         alt="<?= htmlspecialchars($product['nama_produk']) ?>">
                                <?php else: ?>
                                    <i class="fas fa-seedling"></i>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($product['nama_produk']) ?></h5>
                                
                                <?php if ($product['nama_kategori']): ?>
                                    <span class="badge bg-light text-dark mb-2">
                                        <i class="fas fa-tag"></i> <?= htmlspecialchars($product['nama_kategori']) ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($product['nama_jenis']): ?>
                                    <span class="badge bg-warning text-dark mb-2">
                                        <i class="fas fa-seedling"></i> <?= htmlspecialchars($product['nama_jenis']) ?>
                                    </span>
                                <?php endif; ?>
                                
                                <p class="card-text text-muted small flex-grow-1">
                                    <?= htmlspecialchars(substr($product['deskripsi'], 0, 100)) ?>
                                    <?= strlen($product['deskripsi']) > 100 ? '...' : '' ?>
                                </p>
                                
                                <!-- Stock Information by Branch -->
                                <?php if (!$cabang_filter): ?>
                                    <div class="stock-info mb-2">
                                        <div class="stock-item tasik">
                                            <i class="fas fa-seedling"></i> Tasik: <?= $product['stok_tasik'] ?>
                                        </div>
                                        <div class="stock-item garut">
                                            <i class="fas fa-mountain"></i> Garut: <?= $product['stok_garut'] ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="price-tag">
                                        <?= formatRupiah($product['harga']) ?>
                                    </span>
                                    <small class="text-muted">
                                        per <?= htmlspecialchars($product['satuan']) ?>
                                    </small>
                                </div>
                                
                                <?php 
                                $totalStok = $cabang_filter ? $product['stok'] : ($product['stok_tasik'] + $product['stok_garut']);
                                ?>
                                <?php if ($totalStok > 0): ?>
                                    <div class="mt-2">
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Tersedia
                                        </span>
                                        <small class="text-primary ms-2">
                                            <i class="fas fa-eye"></i> Lihat Detail
                                        </small>
                                    </div>
                                <?php else: ?>
                                    <div class="mt-2">
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times"></i> Stok Habis
                                        </span>
                                        <small class="text-primary ms-2">
                                            <i class="fas fa-eye"></i> Lihat Detail
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-seedling"></i> Durian Si Buyunk</h5>
                    <p class="mb-2">Durian segar dan olahan durian terbaik</p>
                    <p class="text-muted small">© 2025 Durian Si Buyunk. All rights reserved.</p>
                </div>
                <div class="col-md-3">
                    <h6>Cabang Tasikmalaya</h6>
                    <p class="small mb-1">
                        <i class="fas fa-map-marker-alt"></i> Jl. Raya Tasikmalaya No. 123
                    </p>
                    <p class="small">
                        <i class="fas fa-phone"></i> 0265-123456
                    </p>
                </div>
                <div class="col-md-3">
                    <h6>Cabang Garut</h6>
                    <p class="small mb-1">
                        <i class="fas fa-map-marker-alt"></i> Jl. Raya Garut No. 456
                    </p>
                    <p class="small">
                        <i class="fas fa-phone"></i> 0262-789012
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>