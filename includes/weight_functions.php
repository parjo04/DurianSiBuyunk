<?php
/**
 * Functions untuk sistem individual weight tracking durian
 */

function addStockWithWeight($produk_id, $cabang, $bobot_kg, $jumlah_pcs, $keterangan = '', $user_id = null) {
    $pdo = getConnection();
    
    try {
        $pdo->beginTransaction();
        
        // Get current stock
        $stmt = $pdo->prepare("SELECT total_kg_{$cabang}, total_pcs_{$cabang}, harga_per_kg FROM produk WHERE id = ?");
        $stmt->execute([$produk_id]);
        $current = $stmt->fetch();
        
        if (!$current) {
            throw new Exception("Produk tidak ditemukan");
        }
        
        $total_kg_sebelum = $current["total_kg_{$cabang}"];
        $total_pcs_sebelum = $current["total_pcs_{$cabang}"];
        $total_kg_sesudah = $total_kg_sebelum + $bobot_kg;
        $total_pcs_sesudah = $total_pcs_sebelum + $jumlah_pcs;
        
        // Update produk stock
        $stmt = $pdo->prepare("
            UPDATE produk 
            SET total_kg_{$cabang} = ?, 
                total_pcs_{$cabang} = ?,
                stok_{$cabang} = ?,
                updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        $stmt->execute([$total_kg_sesudah, $total_pcs_sesudah, $total_pcs_sesudah, $produk_id]);
        
        // Get cabang_id
        $cabang_id = ($cabang === 'tasik') ? 1 : 2;
        
        // Log to stok_history
        $stmt = $pdo->prepare("
            INSERT INTO stok_history 
            (produk_id, cabang_id, cabang, jenis_transaksi, 
             jumlah_sebelum, jumlah_sesudah, selisih,
             total_kg_sebelum, total_kg_sesudah, 
             bobot_kg, jumlah_pcs, harga_per_kg_saat_itu,
             keterangan, user_id, stok_sebelum, stok_sesudah, jumlah) 
            VALUES (?, ?, ?, 'masuk', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $produk_id, $cabang_id, $cabang,
            $total_pcs_sebelum, $total_pcs_sesudah, $jumlah_pcs,
            $total_kg_sebelum, $total_kg_sesudah,
            $bobot_kg, $jumlah_pcs, $current['harga_per_kg'],
            $keterangan, $user_id, $total_pcs_sebelum, $total_pcs_sesudah, $jumlah_pcs
        ]);
        
        $pdo->commit();
        return ['success' => true, 'message' => 'Stok berhasil ditambahkan'];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error adding stock with weight: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function reduceStockWithWeight($produk_id, $cabang, $bobot_kg, $jumlah_pcs, $keterangan = '', $user_id = null) {
    $pdo = getConnection();
    
    try {
        $pdo->beginTransaction();
        
        // Get current stock
        $stmt = $pdo->prepare("SELECT total_kg_{$cabang}, total_pcs_{$cabang}, harga_per_kg FROM produk WHERE id = ?");
        $stmt->execute([$produk_id]);
        $current = $stmt->fetch();
        
        if (!$current) {
            throw new Exception("Produk tidak ditemukan");
        }
        
        $total_kg_sebelum = $current["total_kg_{$cabang}"];
        $total_pcs_sebelum = $current["total_pcs_{$cabang}"];
        
        if ($total_kg_sebelum < $bobot_kg) {
            throw new Exception("Stok tidak mencukupi. Tersedia: {$total_kg_sebelum}kg, diminta: {$bobot_kg}kg");
        }
        
        if ($total_pcs_sebelum < $jumlah_pcs) {
            throw new Exception("Jumlah pieces tidak mencukupi. Tersedia: {$total_pcs_sebelum}pcs, diminta: {$jumlah_pcs}pcs");
        }
        
        $total_kg_sesudah = $total_kg_sebelum - $bobot_kg;
        $total_pcs_sesudah = $total_pcs_sebelum - $jumlah_pcs;
        
        // Get cabang_id
        $cabang_id = ($cabang === 'tasik') ? 1 : 2;
        
        // Update produk stock
        $stmt = $pdo->prepare("
            UPDATE produk 
            SET total_kg_{$cabang} = ?, 
                total_pcs_{$cabang} = ?,
                stok_{$cabang} = ?,
                updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        $stmt->execute([$total_kg_sesudah, $total_pcs_sesudah, $total_pcs_sesudah, $produk_id]);
        
        // Log to stok_history
        $stmt = $pdo->prepare("
            INSERT INTO stok_history 
            (produk_id, cabang_id, cabang, jenis_transaksi, 
             jumlah_sebelum, jumlah_sesudah, selisih,
             total_kg_sebelum, total_kg_sesudah, 
             bobot_kg, jumlah_pcs, harga_per_kg_saat_itu,
             keterangan, user_id, stok_sebelum, stok_sesudah, jumlah) 
            VALUES (?, ?, ?, 'keluar', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $produk_id, $cabang_id, $cabang,
            $total_pcs_sebelum, $total_pcs_sesudah, -$jumlah_pcs,
            $total_kg_sebelum, $total_kg_sesudah,
            $bobot_kg, $jumlah_pcs, $current['harga_per_kg'],
            $keterangan, $user_id, $total_pcs_sebelum, $total_pcs_sesudah, -$jumlah_pcs
        ]);
        
        $pdo->commit();
        return ['success' => true, 'message' => 'Stok berhasil dikurangi'];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error reducing stock with weight: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function updateStockWithWeight($produk_id, $cabang, $new_total_kg, $new_total_pcs, $keterangan = '', $user_id = null) {
    $pdo = getConnection();
    
    try {
        $pdo->beginTransaction();
        
        // Get current stock
        $stmt = $pdo->prepare("SELECT total_kg_{$cabang}, total_pcs_{$cabang}, harga_per_kg FROM produk WHERE id = ?");
        $stmt->execute([$produk_id]);
        $current = $stmt->fetch();
        
        if (!$current) {
            throw new Exception("Produk tidak ditemukan");
        }
        
        $total_kg_sebelum = $current["total_kg_{$cabang}"];
        $total_pcs_sebelum = $current["total_pcs_{$cabang}"];
        
        $selisih_kg = $new_total_kg - $total_kg_sebelum;
        $selisih_pcs = $new_total_pcs - $total_pcs_sebelum;
        
        // Get cabang_id
        $cabang_id = ($cabang === 'tasik') ? 1 : 2;
        
        // Update produk stock
        $stmt = $pdo->prepare("
            UPDATE produk 
            SET total_kg_{$cabang} = ?, 
                total_pcs_{$cabang} = ?,
                stok_{$cabang} = ?,
                updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        $stmt->execute([$new_total_kg, $new_total_pcs, $new_total_pcs, $produk_id]);
        
        // Determine movement type
        $jenis_transaksi = 'penyesuaian';
        if ($selisih_pcs > 0) $jenis_transaksi = 'masuk';
        elseif ($selisih_pcs < 0) $jenis_transaksi = 'keluar';
        
        // Log to stok_history
        $stmt = $pdo->prepare("
            INSERT INTO stok_history 
            (produk_id, cabang_id, cabang, jenis_transaksi, 
             jumlah_sebelum, jumlah_sesudah, selisih,
             total_kg_sebelum, total_kg_sesudah, 
             bobot_kg, jumlah_pcs, harga_per_kg_saat_itu,
             keterangan, user_id, stok_sebelum, stok_sesudah, jumlah) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $produk_id, $cabang_id, $cabang, $jenis_transaksi,
            $total_pcs_sebelum, $new_total_pcs, $selisih_pcs,
            $total_kg_sebelum, $new_total_kg,
            abs($selisih_kg), abs($selisih_pcs), $current['harga_per_kg'],
            $keterangan, $user_id, $total_pcs_sebelum, $new_total_pcs, $selisih_pcs
        ]);
        
        $pdo->commit();
        return ['success' => true, 'message' => 'Stok berhasil diupdate'];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error updating stock with weight: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function calculateStockValue($total_kg, $harga_per_kg) {
    return $total_kg * $harga_per_kg;
}

function formatWeight($bobot_kg) {
    return number_format($bobot_kg, 3, ',', '.') . ' kg';
}

function formatPricePerKg($harga_per_kg) {
    return 'Rp ' . number_format($harga_per_kg, 0, ',', '.') . '/kg';
}

function getAverageWeight($total_kg, $total_pcs) {
    if ($total_pcs == 0) return 0;
    return $total_kg / $total_pcs;
}

function getProductStockInfo($produk_id) {
    $pdo = getConnection();
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                p.*,
                k.nama_kategori,
                j.nama_jenis,
                (p.total_kg_tasik * p.harga_per_kg) as nilai_stok_tasik,
                (p.total_kg_garut * p.harga_per_kg) as nilai_stok_garut,
                CASE 
                    WHEN p.total_pcs_tasik > 0 THEN p.total_kg_tasik / p.total_pcs_tasik 
                    ELSE 0 
                END as rata_bobot_tasik,
                CASE 
                    WHEN p.total_pcs_garut > 0 THEN p.total_kg_garut / p.total_pcs_garut 
                    ELSE 0 
                END as rata_bobot_garut
            FROM produk p
            LEFT JOIN kategori k ON p.kategori_id = k.id
            LEFT JOIN jenis_durian j ON p.jenis_durian_id = j.id
            WHERE p.id = ? AND p.status = 'aktif'
        ");
        
        $stmt->execute([$produk_id]);
        return $stmt->fetch();
        
    } catch(PDOException $e) {
        error_log("Error getting product stock info: " . $e->getMessage());
        return false;
    }
}
?>
