<?php
/**
 * Password reset page for Garut branch admin
 */

require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../includes/auth.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $resetCode = sanitize($_POST['reset_code']);
    $username = sanitize($_POST['username']);
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if (empty($resetCode) || empty($username) || empty($newPassword) || empty($confirmPassword)) {
        $message = 'Semua field harus diisi!';
        $messageType = 'danger';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'Password baru dan konfirmasi password tidak cocok!';
        $messageType = 'danger';
    } elseif (strlen($newPassword) < 6) {
        $message = 'Password minimal 6 karakter!';
        $messageType = 'danger';
    } else {
        $result = resetPassword($username, $newPassword, $resetCode);
        
        if ($result['success']) {
            $message = $result['message'] . ' Silakan login dengan password baru.';
            $messageType = 'success';
        } else {
            $message = $result['message'];
            $messageType = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | <?= APP_NAME ?> - Garut</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --garut-primary: #007bff;
            --garut-secondary: #6610f2;
        }
        
        body {
            background: linear-gradient(135deg, var(--garut-primary), var(--garut-secondary));
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .reset-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.95);
        }
        
        .reset-header {
            background: var(--garut-primary);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
            text-align: center;
        }
        
        .btn-reset {
            background: var(--garut-primary);
            border-color: var(--garut-primary);
            padding: 0.75rem;
            font-weight: bold;
        }
        
        .btn-reset:hover {
            background: #0056b3;
            border-color: #0056b3;
        }
        
        .form-control:focus {
            border-color: var(--garut-primary);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card reset-card border-0">
                    <div class="reset-header">
                        <i class="fas fa-key fa-3x mb-3"></i>
                        <h3 class="mb-0">Reset Password</h3>
                        <p class="mb-0">Admin Garut</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                                <i class="fas fa-<?= $messageType == 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i> 
                                <?= $message ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($messageType !== 'success'): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Kode Reset:</strong> <?= RESET_CODE ?>
                            </div>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="reset_code" class="form-label">
                                        <i class="fas fa-shield-alt"></i> Kode Reset
                                    </label>
                                    <input type="text" class="form-control" id="reset_code" name="reset_code" 
                                           value="<?= isset($_POST['reset_code']) ? htmlspecialchars($_POST['reset_code']) : '' ?>" 
                                           required autofocus>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label">
                                        <i class="fas fa-user"></i> Username
                                    </label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" 
                                           required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">
                                        <i class="fas fa-lock"></i> Password Baru
                                    </label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" 
                                           minlength="6" required>
                                    <div class="form-text">Minimal 6 karakter</div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">
                                        <i class="fas fa-lock"></i> Konfirmasi Password Baru
                                    </label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           minlength="6" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-reset w-100 mb-3">
                                    <i class="fas fa-key"></i> Reset Password
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <div class="text-center">
                            <a href="login.php" class="text-decoration-none">
                                <i class="fas fa-arrow-left"></i> Kembali ke Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>