<?php
/**
 * Product Detail Page for Durian Si Buyunk
 */

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/config.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: index.php');
    exit;
}

// Get product details
$product = getProductById($product_id);

if (!$product) {
    header('Location: index.php');
    exit;
}

// Get categories and jenis durian for navigation
$categories = getCategories();
$jenisDurian = getJenisDurian();

// Get WhatsApp numbers
$whatsappNumbers = [
    'tasik' => '6281225831118', // Tasik: +62 812-2583-1118
    'garut' => '6283870644388'  // Garut: +62 838-7064-4388
];

$pageTitle = htmlspecialchars($product['nama_produk']) . ' - Durian Si Buyunk';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #28a745;
            --secondary-color: #ffc107;
            --tasik-color: #28a745;
            --garut-color: #007bff;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 70px; /* Add padding to prevent navbar overlap */
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), #20c997);
            color: white;
            padding: 2rem 0;
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

        .product-image-container {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .product-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-image:hover {
            transform: scale(1.05);
        }

        .product-info {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-top: -30px;
            position: relative;
            z-index: 2;
        }

        .branch-toggle {
            background: white;
            border-radius: 50px;
            padding: 5px;
            display: inline-flex;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .branch-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 0 2px;
            cursor: pointer;
        }

        .branch-btn.tasik {
            background: transparent;
            color: var(--tasik-color);
        }

        .branch-btn.tasik.active {
            background: var(--tasik-color);
            color: white;
        }

        .branch-btn.garut {
            background: transparent;
            color: var(--garut-color);
        }

        .branch-btn.garut.active {
            background: var(--garut-color);
            color: white;
        }

        .stock-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }

        .stock-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .stock-item:last-child {
            border-bottom: none;
        }

        .price-display {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #fd7e14 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin: 20px 0;
        }

        .whatsapp-btn {
            background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
            width: 100%;
            justify-content: center;
            margin-top: 15px;
        }

        .whatsapp-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
            color: white;
        }

        .badge-custom {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
            margin: 5px;
        }

        .product-description {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin: 20px 0;
        }

        .back-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: #218838;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            color: white;
            transform: translateY(-1px);
        }

        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            body {
                padding-top: 60px; /* Reduced padding for mobile */
            }
            
            .hero-section {
                padding: 1.5rem 0;
            }
            
            .product-info {
                margin-top: -20px;
                padding: 15px;
            }
            
            .branch-toggle {
                width: 100%;
                justify-content: center;
            }
            
            .whatsapp-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-seedling text-success me-2"></i>
                Durian Si Buyunk
            </a>
            <div class="navbar-nav ms-auto">
                <a href="index.php" class="back-btn">
                    <i class="fas fa-arrow-left me-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="h2 fw-bold mb-2"><?= htmlspecialchars($product['nama_produk']) ?></h1>
                    <div class="d-flex flex-wrap mb-2">
                        <?php if ($product['nama_kategori']): ?>
                            <span class="badge-custom bg-light text-dark">
                                <i class="fas fa-tag"></i> <?= htmlspecialchars($product['nama_kategori']) ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($product['nama_jenis']): ?>
                            <span class="badge-custom bg-warning text-dark">
                                <i class="fas fa-seedling"></i> <?= htmlspecialchars($product['nama_jenis']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <p class="mb-0">Durian segar berkualitas premium langsung dari kebun terpilih</p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-seedling" style="font-size: 4rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container my-3">
        <div class="row">
            <!-- Product Image -->
            <div class="col-lg-5 mb-3">
                <div class="product-image-container">
                    <?php if ($product['gambar'] && file_exists("assets/images/products/" . $product['gambar'])): ?>
                        <img src="assets/images/products/<?= $product['gambar'] ?>" 
                             class="product-image" 
                             alt="<?= htmlspecialchars($product['nama_produk']) ?>">
                    <?php else: ?>
                        <div class="product-image d-flex align-items-center justify-content-center bg-light">
                            <i class="fas fa-seedling" style="font-size: 4rem; color: var(--primary-color);"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-7">
                <div class="product-info">
                    <!-- Branch Toggle -->
                    <div class="text-center mb-3">
                        <h6 class="mb-2">Pilih Cabang</h6>
                        <div class="branch-toggle">
                            <button class="branch-btn tasik active" onclick="toggleBranch('tasik')">
                                <i class="fas fa-seedling me-1"></i>
                                Tasikmalaya
                            </button>
                            <button class="branch-btn garut" onclick="toggleBranch('garut')">
                                <i class="fas fa-mountain me-1"></i>
                                Garut
                            </button>
                        </div>
                    </div>

                    <!-- Price Display -->
                    <div class="price-display">
                        <h4 class="mb-1"><?= formatRupiah($product['harga']) ?></h4>
                        <p class="mb-0">per <?= htmlspecialchars($product['satuan']) ?></p>
                        <?php if ($product['harga_per_kg'] > 0): ?>
                            <small>atau <?= formatRupiah($product['harga_per_kg']) ?> per kg</small>
                        <?php endif; ?>
                    </div>

                    <!-- Stock Info -->
                    <div class="stock-info">
                        <h6 class="mb-3"><i class="fas fa-boxes me-2"></i>Informasi Stok</h6>
                        
                        <!-- Tasikmalaya Stock -->
                        <div class="stock-item branch-info" data-branch="tasik">
                            <div>
                                <strong><i class="fas fa-seedling me-2 text-success"></i>Tasikmalaya</strong>
                                <br>
                                <small class="text-muted">Jl. Raya Tasikmalaya No. 123</small>
                            </div>
                            <div class="text-end">
                                <span class="badge <?= $product['stok_tasik'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $product['stok_tasik'] ?> <?= htmlspecialchars($product['satuan']) ?>
                                </span>
                                <?php if ($product['total_kg_tasik'] > 0): ?>
                                    <br><small class="text-muted"><?= number_format($product['total_kg_tasik'], 2) ?> kg</small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Garut Stock -->
                        <div class="stock-item branch-info d-none" data-branch="garut">
                            <div>
                                <strong><i class="fas fa-mountain me-2 text-primary"></i>Garut</strong>
                                <br>
                                <small class="text-muted">Jl. Raya Garut No. 456</small>
                            </div>
                            <div class="text-end">
                                <span class="badge <?= $product['stok_garut'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $product['stok_garut'] ?> <?= htmlspecialchars($product['satuan']) ?>
                                </span>
                                <?php if ($product['total_kg_garut'] > 0): ?>
                                    <br><small class="text-muted"><?= number_format($product['total_kg_garut'], 2) ?> kg</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Order Button -->
                    <div class="text-center">
                        <!-- Tasikmalaya WhatsApp Button -->
                        <a href="#" class="whatsapp-btn branch-order" data-branch="tasik" 
                           onclick="orderViaWhatsApp('tasik')">
                            <i class="fab fa-whatsapp"></i>
                            Pesan via WhatsApp - Tasikmalaya
                        </a>
                        
                        <!-- Garut WhatsApp Button -->
                        <a href="#" class="whatsapp-btn branch-order d-none" data-branch="garut" 
                           onclick="orderViaWhatsApp('garut')">
                            <i class="fab fa-whatsapp"></i>
                            Pesan via WhatsApp - Garut
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description (if exists) -->
        <?php if ($product['deskripsi']): ?>
        <div class="row mt-3">
            <div class="col-12">
                <div class="product-description">
                    <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Deskripsi Produk</h6>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($product['deskripsi'])) ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-seedling me-2"></i>Durian Si Buyunk</h5>
                    <p>Durian segar berkualitas premium dari Tasikmalaya & Garut</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="mb-2">
                        <strong>Tasikmalaya:</strong> 0265-123456
                    </div>
                    <div>
                        <strong>Garut:</strong> 0262-789012
                    </div>
                </div>
            </div>
            <hr class="my-3">
            <div class="text-center">
                <small>&copy; 2025 Durian Si Buyunk. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Branch toggle functionality
        function toggleBranch(branch) {
            // Update button states
            document.querySelectorAll('.branch-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`.branch-btn.${branch}`).classList.add('active');
            
            // Show/hide appropriate stock info
            document.querySelectorAll('.branch-info').forEach(info => {
                info.classList.add('d-none');
            });
            document.querySelector(`.branch-info[data-branch="${branch}"]`).classList.remove('d-none');
            
            // Show/hide appropriate WhatsApp button
            document.querySelectorAll('.branch-order').forEach(btn => {
                btn.classList.add('d-none');
            });
            document.querySelector(`.branch-order[data-branch="${branch}"]`).classList.remove('d-none');
        }

        // WhatsApp order functionality
        function orderViaWhatsApp(branch) {
            const productName = '<?= addslashes($product['nama_produk']) ?>';
            const price = '<?= formatRupiah($product['harga']) ?>';
            const unit = '<?= addslashes($product['satuan']) ?>';
            
            const branchInfo = {
                'tasik': {
                    name: 'Tasikmalaya',
                    phone: '<?= $whatsappNumbers['tasik'] ?>',
                    stock: <?= $product['stok_tasik'] ?>
                },
                'garut': {
                    name: 'Garut', 
                    phone: '<?= $whatsappNumbers['garut'] ?>',
                    stock: <?= $product['stok_garut'] ?>
                }
            };
            
            const selectedBranch = branchInfo[branch];
            
            if (selectedBranch.stock <= 0) {
                alert('Maaf, stok di cabang ' + selectedBranch.name + ' sedang kosong.');
                return;
            }
            
            const message = `Halo Durian Si Buyunk ${selectedBranch.name}! 🥭

Saya tertarik untuk memesan:
📦 Produk: ${productName}
💰 Harga: ${price} per ${unit}
🏪 Cabang: ${selectedBranch.name}

Mohon informasi ketersediaan dan cara pemesanannya.

Terima kasih! 😊`;

            const whatsappUrl = `https://wa.me/${selectedBranch.phone}?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        }

        // Initialize with Tasikmalaya branch
        document.addEventListener('DOMContentLoaded', function() {
            toggleBranch('tasik');
        });
    </script>
</body>
</html>
