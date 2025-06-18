<?php
/**
 * Update stock with individual weight for Garut branch
 */

session_start();
require_once '../../../config/database.php';
require_once '../../../includes/auth.php';
require_once '../../../includes/weight_functions.php';

requireLogin(CABANG_GARUT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produk_id = (int)$_POST['produk_id'];
    $bobot_kg = floatval($_POST['bobot_kg']);
    $jumlah_pcs = (int)$_POST['jumlah_pcs'];
    $operation = $_POST['operation'] ?? 'add';
    $keterangan = trim($_POST['keterangan'] ?? '');
    
    try {
        // Validate input
        if ($produk_id <= 0) {
            throw new Exception("ID produk tidak valid");
        }
        
        if ($bobot_kg <= 0) {
            throw new Exception("Bobot harus lebih dari 0");
        }
        
        if ($jumlah_pcs <= 0) {
            throw new Exception("Jumlah pieces harus lebih dari 0");
        }
        
        $user_id = $_SESSION['user_id'] ?? null;
        
        if ($operation === 'add') {
            $result = addStockWithWeight($produk_id, 'garut', $bobot_kg, $jumlah_pcs, $keterangan, $user_id);
        } elseif ($operation === 'reduce') {
            $result = reduceStockWithWeight($produk_id, 'garut', $bobot_kg, $jumlah_pcs, $keterangan, $user_id);
        } else {
            throw new Exception("Operasi tidak valid");
        }
        
        if ($result['success']) {
            echo json_encode([
                'success' => true,
                'message' => $result['message']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => $result['message']
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Metode tidak valid'
    ]);
}
?> 
            $_SESSION['user_id']
        );
        
        echo json_encode(['success' => true, 'message' => 'Stok berhasil diupdate']);
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
