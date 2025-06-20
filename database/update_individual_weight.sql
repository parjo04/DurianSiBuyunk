-- Update database schema untuk individual weight tracking
-- /home/gis/01-PROJECTS/DurianSiBuyunk/database/update_individual_weight.sql

-- Update tabel produk
ALTER TABLE produk 
DROP COLUMN IF EXISTS bobot_rata,
ADD COLUMN harga_per_kg DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Harga per kilogram',
ADD COLUMN total_kg_tasik DECIMAL(10,3) DEFAULT 0.000 COMMENT 'Total berat stok Tasik dalam kg',
ADD COLUMN total_kg_garut DECIMAL(10,3) DEFAULT 0.000 COMMENT 'Total berat stok Garut dalam kg',
ADD COLUMN total_pcs_tasik INT DEFAULT 0 COMMENT 'Total pieces Tasik',
ADD COLUMN total_pcs_garut INT DEFAULT 0 COMMENT 'Total pieces Garut';

-- Update data existing untuk compatibility
UPDATE produk SET 
    harga_per_kg = COALESCE(harga_per_kg, ROUND(harga / 2.5, 2)),
    total_kg_tasik = stok_tasik * 2.5,
    total_kg_garut = stok_garut * 2.5,
    total_pcs_tasik = stok_tasik,
    total_pcs_garut = stok_garut
WHERE harga_per_kg = 0;

-- Update tabel stok_history untuk individual tracking
ALTER TABLE stok_history 
ADD COLUMN bobot_kg DECIMAL(8,3) DEFAULT 0.000 COMMENT 'Bobot individual dalam kg untuk transaksi ini',
ADD COLUMN jumlah_pcs INT DEFAULT 0 COMMENT 'Jumlah pieces untuk transaksi ini',
ADD COLUMN total_kg_sebelum DECIMAL(10,3) DEFAULT 0.000 COMMENT 'Total kg sebelum transaksi',
ADD COLUMN total_kg_sesudah DECIMAL(10,3) DEFAULT 0.000 COMMENT 'Total kg sesudah transaksi',
ADD COLUMN harga_per_kg_saat_itu DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Harga per kg saat transaksi';

-- Tabel baru untuk detail stok individual (opsional untuk tracking detail)
CREATE TABLE IF NOT EXISTS stok_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produk_id INT NOT NULL,
    cabang ENUM('tasik', 'garut') NOT NULL,
    bobot_kg DECIMAL(8,3) NOT NULL COMMENT 'Bobot individual dalam kg',
    tanggal_masuk DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('tersedia', 'terjual', 'rusak') DEFAULT 'tersedia',
    batch_id VARCHAR(50) DEFAULT NULL COMMENT 'ID batch untuk grouping',
    keterangan TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE,
    INDEX idx_produk_cabang (produk_id, cabang),
    INDEX idx_status (status),
    INDEX idx_tanggal (tanggal_masuk)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Index untuk performa
ALTER TABLE produk ADD INDEX idx_stok_kg (total_kg_tasik, total_kg_garut);
ALTER TABLE stok_history ADD INDEX idx_bobot (bobot_kg, jumlah_pcs);
