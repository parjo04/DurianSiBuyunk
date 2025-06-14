<?php
/**
 * Main login page - Choose branch admin login
 */
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../includes/auth.php');


// Redirect if already logged in to appropriate dashboard
if (isSessionValid()) {
    if ($_SESSION['cabang_id'] == CABANG_TASIK) {
        redirect('cabang/tasik/dashboard.php');
    } elseif ($_SESSION['cabang_id'] == CABANG_GARUT) {
        redirect('cabang/garut/dashboard.php');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #28a745, #007bff);
            --tasik-color: #28a745;
            --garut-color: #007bff;
        }
        
        body {
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-card {
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.95);
            overflow: hidden;
        }
        
        .header-section {
            background: linear-gradient(135deg, var(--tasik-color), var(--garut-color));
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            position: relative;
        }
        
        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .header-content {
            position: relative;
            z-index: 1;
        }
        
        .branch-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
            border-radius: 15px;
            padding: 2rem;
            text-decoration: none;
            display: block;
            height: 100%;
        }
        
        .branch-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            text-decoration: none;
        }
        
        .branch-card.tasik {
            border-color: var(--tasik-color);
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.05), rgba(40, 167, 69, 0.1));
        }
        
        .branch-card.tasik:hover {
            border-color: var(--tasik-color);
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.15));
        }
        
        .branch-card.garut {
            border-color: var(--garut-color);
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.05), rgba(0, 123, 255, 0.1));
        }
        
        .branch-card.garut:hover {
            border-color: var(--garut-color);
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.1), rgba(0, 123, 255, 0.15));
        }
        
        .branch-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .tasik .branch-icon {
            color: var(--tasik-color);
        }
        
        .garut .branch-icon {
            color: var(--garut-color);
        }
        
        .branch-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .tasik .branch-title {
            color: var(--tasik-color);
        }
        
        .garut .branch-title {
            color: var(--garut-color);
        }
        
        .branch-subtitle {
            color: #6c757d;
            margin-bottom: 1rem;
        }
        
        .branch-features {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .branch-features li {
            padding: 0.25rem 0;
            color: #495057;
            font-size: 0.9rem;
        }
        
        .branch-features i {
            width: 20px;
            text-align: center;
            margin-right: 0.5rem;
        }
        
        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
            pointer-events: none;
        }
        
        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 12s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
            color: var(--tasik-color);
        }
        
        .shape:nth-child(2) {
            top: 20%;
            right: 15%;
            animation-delay: 4s;
            color: var(--garut-color);
        }
        
        .shape:nth-child(3) {
            bottom: 15%;
            left: 20%;
            animation-delay: 8s;
            color: var(--tasik-color);
        }
        
        .shape:nth-child(4) {
            bottom: 25%;
            right: 25%;
            animation-delay: 2s;
            color: var(--garut-color);
        }
        
        @keyframes float {
            0%, 100% { 
                transform: translateY(0px) rotate(0deg) scale(1); 
            }
            25% { 
                transform: translateY(-20px) rotate(90deg) scale(1.1); 
            }
            50% { 
                transform: translateY(-10px) rotate(180deg) scale(0.9); 
            }
            75% { 
                transform: translateY(-30px) rotate(270deg) scale(1.05); 
            }
        }
        
        .public-link {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .btn-public {
            background: rgba(255,255,255,0.9);
            border: 2px solid rgba(255,255,255,0.3);
            color: #495057;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .btn-public:hover {
            background: white;
            color: #495057;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        @media (max-width: 768px) {
            .header-section {
                padding: 2rem 1rem;
            }
            
            .branch-card {
                padding: 1.5rem;
                margin-bottom: 1rem;
            }
            
            .branch-icon {
                font-size: 3rem;
            }
            
            .public-link {
                position: static;
                text-align: center;
                margin-top: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <i class="fas fa-seedling shape" style="font-size: 4rem;"></i>
        <i class="fas fa-mountain shape" style="font-size: 3.5rem;"></i>
        <i class="fas fa-leaf shape" style="font-size: 2.8rem;"></i>
        <i class="fas fa-sun shape" style="font-size: 3.2rem;"></i>
    </div>

    <div class="public-link d-none d-md-block">
        <a href="../" class="btn-public">
            <i class="fas fa-eye"></i> Lihat Katalog Produk
        </a>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card main-card border-0">
                    <div class="header-section">
                        <div class="header-content">
                            <i class="fas fa-seedling fa-4x mb-3"></i>
                            <h1 class="display-5 fw-bold mb-2">Durian Si Buyunk</h1>
                            <p class="lead mb-0">Sistem Manajemen Stok & Inventori</p>
                            <p class="mb-0 opacity-75">Pilih cabang untuk masuk sebagai admin</p>
                        </div>
                    </div>
                    
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <!-- Tasikmalaya Branch -->
                            <div class="col-md-6">
                                <a href="../cabang/tasik/auth/login.php" class="branch-card tasik">
                                    <div class="text-center">
                                        <i class="fas fa-seedling branch-icon"></i>
                                        <h3 class="branch-title">Cabang Tasikmalaya</h3>
                                        <p class="branch-subtitle">Admin Panel Tasikmalaya</p>
                                        
                                        <ul class="branch-features text-start">
                                            <li><i class="fas fa-map-marker-alt text-success"></i> Jl. Raya Tasikmalaya No. 123</li>
                                            <li><i class="fas fa-phone text-success"></i> 0265-123456</li>
                                            <li><i class="fas fa-boxes text-success"></i> Kelola Stok Produk</li>
                                            <li><i class="fas fa-chart-line text-success"></i> Dashboard & Laporan</li>
                                            <li><i class="fas fa-tags text-success"></i> Manajemen Kategori</li>
                                        </ul>
                                        
                                        <div class="mt-3">
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="fas fa-sign-in-alt"></i> Masuk Admin Tasik
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            
                            <!-- Garut Branch -->
                            <div class="col-md-6">
                                <a href="../cabang/garut/auth/login.php" class="branch-card garut">
                                    <div class="text-center">
                                        <i class="fas fa-mountain branch-icon"></i>
                                        <h3 class="branch-title">Cabang Garut</h3>
                                        <p class="branch-subtitle">Admin Panel Garut</p>
                                        
                                        <ul class="branch-features text-start">
                                            <li><i class="fas fa-map-marker-alt text-primary"></i> Jl. Raya Garut No. 456</li>
                                            <li><i class="fas fa-phone text-primary"></i> 0262-789012</li>
                                            <li><i class="fas fa-boxes text-primary"></i> Kelola Stok Produk</li>
                                            <li><i class="fas fa-chart-line text-primary"></i> Dashboard & Laporan</li>
                                            <li><i class="fas fa-tags text-primary"></i> Manajemen Kategori</li>
                                        </ul>
                                        
                                        <div class="mt-3">
                                            <span class="badge bg-primary px-3 py-2">
                                                <i class="fas fa-sign-in-alt"></i> Masuk Admin Garut
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Mobile Public Link -->
                        <div class="d-md-none text-center mt-4">
                            <a href="public/" class="btn btn-outline-secondary">
                                <i class="fas fa-eye"></i> Lihat Katalog Produk
                            </a>
                        </div>
                        
                        <!-- Info Section -->
                        <div class="row mt-5">
                            <div class="col-12">
                                <div class="alert alert-info border-0" style="background: linear-gradient(135deg, rgba(13, 202, 240, 0.1), rgba(13, 202, 240, 0.05));">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="alert-heading mb-2">
                                                <i class="fas fa-info-circle"></i> Informasi Login Admin
                                            </h6>
                                            <p class="mb-0 small">
                                                Setiap cabang memiliki sistem login terpisah untuk keamanan. 
                                                Pilih cabang sesuai dengan akses admin Anda.
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                            <small class="text-muted">
                                                <i class="fas fa-shield-alt"></i> 
                                                Sistem Aman & Terpercaya
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Info -->
                <div class="text-center mt-4">
                    <small class="text-white">
                        <i class="fas fa-copyright"></i> 2025 Durian Si Buyunk - Sistem Manajemen Stok v<?= APP_VERSION ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Animate cards on load
            const cards = document.querySelectorAll('.branch-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
            
            // Add click effect
            cards.forEach(card => {
                card.addEventListener('click', function(e) {
                    // Add ripple effect
                    const ripple = document.createElement('div');
                    ripple.style.position = 'absolute';
                    ripple.style.borderRadius = '50%';
                    ripple.style.background = 'rgba(255,255,255,0.6)';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.animation = 'ripple 0.6s linear';
                    ripple.style.left = (e.clientX - card.offsetLeft) + 'px';
                    ripple.style.top = (e.clientY - card.offsetTop) + 'px';
                    ripple.style.width = ripple.style.height = '20px';
                    ripple.style.marginLeft = ripple.style.marginTop = '-10px';
                    
                    card.style.position = 'relative';
                    card.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        });
        
        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>