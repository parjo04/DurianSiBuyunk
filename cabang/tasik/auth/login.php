<?php
/**
 * Login page for Tasikmalaya branch admin
 */

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth.php';

// Redirect if already logged in
if (isSessionValid() && $_SESSION['cabang_id'] == CABANG_TASIK) {
    redirect('../dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        $result = loginUser($username, $password);
        
        if ($result['success']) {
            // Check if user belongs to Tasikmalaya branch
            if ($result['user']['cabang_id'] == CABANG_TASIK) {
                redirect('../dashboard.php');
            } else {
                $error = 'Anda tidak memiliki akses ke cabang Tasikmalaya!';
                logoutUser();
            }
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin Tasikmalaya | <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --tasik-primary: #28a745;
            --tasik-secondary: #20c997;
        }
        
        body {
            background: linear-gradient(135deg, var(--tasik-primary), var(--tasik-secondary));
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.95);
        }
        
        .login-header {
            background: var(--tasik-primary);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
            text-align: center;
        }
        
        .btn-login {
            background: var(--tasik-primary);
            border-color: var(--tasik-primary);
            padding: 0.75rem;
            font-weight: bold;
        }
        
        .btn-login:hover {
            background: #1e7e34;
            border-color: #1e7e34;
        }
        
        .form-control:focus {
            border-color: var(--tasik-primary);
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        
        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }
        
        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .shape:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <i class="fas fa-seedling shape" style="font-size: 3rem;"></i>
        <i class="fas fa-leaf shape" style="font-size: 2.5rem;"></i>
        <i class="fas fa-tree shape" style="font-size: 4rem;"></i>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card border-0">
                    <div class="login-header">
                        <i class="fas fa-seedling fa-3x mb-3"></i>
                        <h3 class="mb-0">Durian Si Buyunk</h3>
                        <p class="mb-0">Admin Tasikmalaya</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user"></i> Username
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" 
                                       required autofocus>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Password
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-login w-100 mb-3">
                                <i class="fas fa-sign-in-alt"></i> Masuk
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <a href="reset.php" class="text-decoration-none">
                                <i class="fas fa-key"></i> Lupa Password?
                            </a>
                        </div>
                        
                        <hr>
                        
                        <div class="text-center">
                            <a href="../../../public/" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-home"></i> Halaman Utama
                            </a>
                            <a href="../../garut/auth/login.php" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-exchange-alt"></i> Login Garut
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <small class="text-white">
                        <i class="fas fa-shield-alt"></i> Sistem Manajemen Stok Durian Si Buyunk v<?= APP_VERSION ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>