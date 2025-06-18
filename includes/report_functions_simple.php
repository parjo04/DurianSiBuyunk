<?php
/**
 * Simple Report Functions for Durian Si Buyunk
 */

require_once __DIR__ . '/../config/database.php';

// Generate simple report summary
function generateReportSummary($cabang) {
    try {
        $pdo = getConnection();
        
        // Get stok column based on branch
        $stok_col = $cabang === 'tasik' ? 'stok_tasik' : 'stok_garut';
        $kg_col = $cabang === 'tasik' ? 'total_kg_tasik' : 'total_kg_garut';
        
        // Total produk aktif
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM produk WHERE status = 'aktif'");
        $total_produk = $stmt->fetch()['total'];
        
        // Total stok buah/pcs
        $stmt = $pdo->query("SELECT SUM($stok_col) as total FROM produk WHERE status = 'aktif'");
        $total_stok_buah = $stmt->fetch()['total'] ?? 0;
        
        // Total stok kg
        $stmt = $pdo->query("SELECT SUM($kg_col) as total FROM produk WHERE status = 'aktif'");
        $total_stok_kg = $stmt->fetch()['total'] ?? 0;
        
        // Total nilai stok (kg * harga per kg)
        $stmt = $pdo->query("SELECT SUM($kg_col * harga_per_kg) as total FROM produk WHERE status = 'aktif'");
        $total_nilai_stok = $stmt->fetch()['total'] ?? 0;
        
        // Produk dengan stok menipis (< 5)
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM produk WHERE status = 'aktif' AND $stok_col > 0 AND $stok_col < 5");
        $produk_menipis = $stmt->fetch()['total'];
        
        // Produk habis
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM produk WHERE status = 'aktif' AND $stok_col = 0");
        $produk_habis = $stmt->fetch()['total'];
        
        return [
            'total_produk' => $total_produk,
            'total_stok' => $total_stok_buah, // backward compatibility
            'total_stok_buah' => $total_stok_buah,
            'total_stok_kg' => $total_stok_kg,
            'total_nilai_stok' => $total_nilai_stok,
            'produk_menipis' => $produk_menipis,
            'produk_habis' => $produk_habis
        ];
        
    } catch (Exception $e) {
        throw new Exception("Error generating report summary: " . $e->getMessage());
    }
}

// Get stock movement history
function getStockMovementHistory($cabang, $date_from = null, $date_to = null, $limit = 50) {
    try {
        $pdo = getConnection();
        
        $sql = "
            SELECT 
                sh.*,
                p.nama_produk,
                k.nama_kategori,
                u.nama_lengkap as user_name
            FROM stok_history sh
            LEFT JOIN produk p ON sh.produk_id = p.id
            LEFT JOIN kategori k ON p.kategori_id = k.id
            LEFT JOIN users u ON sh.user_id = u.id
            WHERE sh.cabang = ?
        ";
        
        $params = [$cabang];
        
        if ($date_from) {
            $sql .= " AND DATE(sh.created_at) >= ?";
            $params[] = $date_from;
        }
        
        if ($date_to) {
            $sql .= " AND DATE(sh.created_at) <= ?";
            $params[] = $date_to;
        }
        
        $sql .= " ORDER BY sh.created_at DESC LIMIT $limit";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
        
    } catch (Exception $e) {
        throw new Exception("Error getting stock movement history: " . $e->getMessage());
    }
}

// Get current stock report
function getCurrentStockReport($cabang) {
    try {
        $pdo = getConnection();
        
        $stok_col = $cabang === 'tasik' ? 'stok_tasik' : 'stok_garut';
        $kg_col = $cabang === 'tasik' ? 'total_kg_tasik' : 'total_kg_garut';
        
        $sql = "
            SELECT 
                p.id,
                p.nama_produk,
                k.nama_kategori,
                p.$stok_col as stok_pcs,
                p.$kg_col as stok_kg,
                p.harga_per_kg,
                p.satuan,
                CASE 
                    WHEN p.$stok_col = 0 THEN 'Habis'
                    WHEN p.$stok_col < 5 THEN 'Menipis'
                    ELSE 'Tersedia'
                END as status_stok
            FROM produk p
            LEFT JOIN kategori k ON p.kategori_id = k.id
            WHERE p.status = 'aktif'
            ORDER BY p.$stok_col ASC, p.nama_produk
        ";
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
        
    } catch (Exception $e) {
        throw new Exception("Error getting current stock report: " . $e->getMessage());
    }
}

// Log stock history (simplified version)
function logStokHistory($produk_id, $cabang, $jenis_pergerakan, $jumlah_sebelum, $jumlah_sesudah, $keterangan = '', $user_id = null) {
    $pdo = getConnection();
    
    $selisih = $jumlah_sesudah - $jumlah_sebelum;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO stok_history 
            (produk_id, cabang, jenis_pergerakan, jumlah_sebelum, jumlah_sesudah, selisih, keterangan, user_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $produk_id, $cabang, $jenis_pergerakan, 
            $jumlah_sebelum, $jumlah_sesudah, $selisih, 
            $keterangan, $user_id
        ]);
    } catch(PDOException $e) {
        error_log("Error logging stok history: " . $e->getMessage());
        return false;
    }
}
