<?php
/**
 * Utility functions for Durian Si Buyunk
 */

require_once __DIR__ . '/../config/config.php';

// Get all categories
function getCategories() {
    try {
        $pdo = getConnection();
        $stmt = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

// Get all jenis durian
function getJenisDurian() {
    try {
        $pdo = getConnection();
        $stmt = $pdo->query("SELECT * FROM jenis_durian ORDER BY nama_jenis");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

// Get products for specific branch
function getProducts($cabang_id = null, $kategori_id = null, $search = null) {
    try {
        $pdo = getConnection();
        
        $sql = "SELECT p.*, k.nama_kategori, jd.nama_jenis,
                CASE 
                    WHEN ? = 1 THEN p.stok_tasik 
                    WHEN ? = 2 THEN p.stok_garut 
                    ELSE (p.stok_tasik + p.stok_garut) 
                END as stok
                FROM produk p 
                LEFT JOIN kategori k ON p.kategori_id = k.id 
                LEFT JOIN jenis_durian jd ON p.jenis_durian_id = jd.id 
                WHERE p.status = 'aktif'";
        
        $params = [$cabang_id, $cabang_id];
        
        if ($kategori_id) {
            $sql .= " AND p.kategori_id = ?";
            $params[] = $kategori_id;
        }
        
        if ($search) {
            $sql .= " AND (p.nama_produk LIKE ? OR p.deskripsi LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $sql .= " ORDER BY p.nama_produk";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

// Get single product by ID
function getProduct($id) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT p.*, k.nama_kategori, jd.nama_jenis,
                              COALESCE(p.total_kg_tasik, 0) as total_kg_tasik,
                              COALESCE(p.total_kg_garut, 0) as total_kg_garut
                              FROM produk p 
                              LEFT JOIN kategori k ON p.kategori_id = k.id 
                              LEFT JOIN jenis_durian jd ON p.jenis_durian_id = jd.id 
                              WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

// Alias for getProduct for backward compatibility
function getProductById($id) {
    return getProduct($id);
}

// Add new product
function addProduct($data) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("INSERT INTO produk (nama_produk, kategori_id, jenis_durian_id, harga, harga_per_kg, stok_tasik, stok_garut, total_kg_tasik, total_kg_garut, total_pcs_tasik, total_pcs_garut, satuan, deskripsi, gambar) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $data['nama_produk'],
            $data['kategori_id'] ?: null,
            $data['jenis_durian_id'] ?: null,
            $data['harga'],
            $data['harga_per_kg'] ?? 0,
            $data['stok_tasik'] ?? 0,
            $data['stok_garut'] ?? 0,
            $data['total_kg_tasik'] ?? 0,
            $data['total_kg_garut'] ?? 0,
            $data['total_pcs_tasik'] ?? 0,
            $data['total_pcs_garut'] ?? 0,
            $data['satuan'],
            $data['deskripsi'],
            $data['gambar'] ?? null
        ]);
        
        return [
            'success' => true,
            'message' => 'Produk berhasil ditambahkan!',
            'id' => $pdo->lastInsertId(),
            'product_id' => $pdo->lastInsertId()
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Gagal menambahkan produk: ' . $e->getMessage()
        ];
    }
}

// Update product
function updateProduct($id, $data) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("UPDATE produk SET 
                              nama_produk = ?, 
                              kategori_id = ?, 
                              jenis_durian_id = ?, 
                              harga = ?, 
                              harga_per_kg = ?,
                              stok_tasik = ?, 
                              stok_garut = ?,
                              total_kg_tasik = ?,
                              total_kg_garut = ?,
                              total_pcs_tasik = ?,
                              total_pcs_garut = ?,
                              satuan = ?, 
                              deskripsi = ?, 
                              gambar = ?,
                              updated_at = NOW()
                              WHERE id = ?");
        
        $stmt->execute([
            $data['nama_produk'],
            $data['kategori_id'] ?: null,
            $data['jenis_durian_id'] ?: null,
            $data['harga'],
            $data['harga_per_kg'] ?? 0,
            $data['stok_tasik'] ?? 0,
            $data['stok_garut'] ?? 0,
            $data['total_kg_tasik'] ?? 0,
            $data['total_kg_garut'] ?? 0,
            $data['total_pcs_tasik'] ?? 0,
            $data['total_pcs_garut'] ?? 0,
            $data['satuan'],
            $data['deskripsi'],
            $data['gambar'],
            $id
        ]);
        
        return [
            'success' => true,
            'message' => 'Produk berhasil diupdate!'
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Gagal mengupdate produk: ' . $e->getMessage()
        ];
    }
}

// Delete product
function deleteProduct($id) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("UPDATE produk SET status = 'nonaktif' WHERE id = ?");
        $stmt->execute([$id]);
        
        return [
            'success' => true,
            'message' => 'Produk berhasil dihapus!'
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Gagal menghapus produk: ' . $e->getMessage()
        ];
    }
}

// Get dashboard statistics
function getDashboardStats($cabang_id) {
    try {
        $pdo = getConnection();
        
        // Total products
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM produk WHERE status = 'aktif'");
        $totalProducts = $stmt->fetch()['total'];
        
        // Total stock for branch (pcs)
        if ($cabang_id == CABANG_TASIK) {
            $stmt = $pdo->query("SELECT SUM(stok_tasik) as total FROM produk WHERE status = 'aktif'");
            $totalStock = $stmt->fetch()['total'] ?? 0;
            
            // Total weight (kg) for branch
            $stmt = $pdo->query("SELECT SUM(total_kg_tasik) as total FROM produk WHERE status = 'aktif'");
            $totalWeight = $stmt->fetch()['total'] ?? 0;
            
            // Low stock products (< 5)
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM produk WHERE status = 'aktif' AND stok_tasik < 5");
        } else {
            $stmt = $pdo->query("SELECT SUM(stok_garut) as total FROM produk WHERE status = 'aktif'");
            $totalStock = $stmt->fetch()['total'] ?? 0;
            
            // Total weight (kg) for branch
            $stmt = $pdo->query("SELECT SUM(total_kg_garut) as total FROM produk WHERE status = 'aktif'");
            $totalWeight = $stmt->fetch()['total'] ?? 0;
            
            // Low stock products (< 5)
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM produk WHERE status = 'aktif' AND stok_garut < 5");
        }
        $lowStock = $stmt->fetch()['total'];
        
        // Total categories
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM kategori");
        $totalCategories = $stmt->fetch()['total'];
        
        return [
            'total_products' => $totalProducts,
            'total_stock' => $totalStock,
            'total_weight' => $totalWeight,
            'low_stock' => $lowStock,
            'total_categories' => $totalCategories
        ];
    } catch (Exception $e) {
        return [
            'total_products' => 0,
            'total_stock' => 0,
            'total_weight' => 0,
            'low_stock' => 0,
            'total_categories' => 0
        ];
    }
}

// Handle file upload with branch support
function handleFileUpload($file, $branch = null, $uploadDir = null) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    // Determine branch from session if not provided
    if (!$branch && isset($_SESSION['user']['cabang_id'])) {
        $branch = $_SESSION['user']['cabang_id'] == CABANG_TASIK ? 'tasik' : 'garut';
    }
    
    // Set upload directory based on branch
    if (!$uploadDir) {
        $baseDir = __DIR__ . '/../public/assets/images/products/';
        if ($branch) {
            $uploadDir = $baseDir . $branch . '/';
        } else {
            $uploadDir = $baseDir;
        }
    }
    
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmp = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Check file extension
    if (!in_array($fileExt, ALLOWED_EXTENSIONS)) {
        throw new Exception('Format file tidak didukung. Gunakan: ' . implode(', ', ALLOWED_EXTENSIONS));
    }
    
    // Check file size
    if ($fileSize > MAX_FILE_SIZE) {
        throw new Exception('Ukuran file terlalu besar. Maksimal 2MB.');
    }
    
    // Create unique filename with branch prefix
    $prefix = $branch ? $branch . '_' : '';
    $newFileName = $prefix . uniqid() . '_' . time() . '.' . $fileExt;
    $uploadPath = $uploadDir . $newFileName;
    
    // Create directory if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($fileTmp, $uploadPath)) {
        // Return relative path from public directory
        return ($branch ? $branch . '/' : '') . $newFileName;
    } else {
        throw new Exception('Gagal mengupload file.');
    }
}

// Get image path for display with cache busting
function getImagePath($imageName, $branch = null) {
    if (!$imageName) return null;
    
    // Check if image already has branch prefix in path
    if (strpos($imageName, '/') !== false) {
        $path = "public/assets/images/products/" . $imageName;
    } else {
        // For backward compatibility, check in multiple locations
        $possiblePaths = [
            "public/assets/images/products/" . $imageName, // Main folder
            "public/assets/images/products/tasik/" . $imageName, // Tasik folder
            "public/assets/images/products/garut/" . $imageName, // Garut folder
        ];
        
        $path = null;
        foreach ($possiblePaths as $testPath) {
            if (file_exists($testPath)) {
                $path = $testPath;
                break;
            }
        }
    }
    
    return $path;
}

// Get image URL with cache busting timestamp
function getImageUrl($imageName, $branch = null) {
    $imagePath = getImagePath($imageName, $branch);
    if (!$imagePath || !file_exists($imagePath)) {
        return null;
    }
    
    // Add timestamp for cache busting
    $timestamp = filemtime($imagePath);
    return $imagePath . '?v=' . $timestamp;
}

// Note: formatRupiah() and sanitize() functions are already defined in config/config.php
?>