<?php
/**
 * Get stock information with weight data for Garut branch
 */

header('Content-Type: application/json');
session_start();

require_once '../../../config/database.php';
require_once '../../../includes/auth.php';
require_once '../../../includes/weight_functions.php';

requireLogin(CABANG_GARUT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $produk_id = (int)($input['produk_id'] ?? 0);
    
    if ($produk_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID produk tidak valid']);
        exit;
    }
    
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("
            SELECT 
                nama_produk,
                total_kg_garut,
                total_pcs_garut,
                harga_per_kg,
                stok_garut
            FROM produk 
            WHERE id = ? AND status = 'aktif'
        ");
        
        $stmt->execute([$produk_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
            exit;
        }
        
        echo json_encode([
            'success' => true,
            'nama_produk' => $product['nama_produk'],
            'total_kg' => number_format($product['total_kg_garut'], 3, '.', ''),
            'total_pcs' => $product['total_pcs_garut'],
            'harga_per_kg' => $product['harga_per_kg'],
            'stok_legacy' => $product['stok_garut']
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metode tidak valid']);
}
?>
