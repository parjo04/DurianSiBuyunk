<?php
/**
 * Database Testing Page - Display All Database Content
 * FOR TESTING PURPOSES ONLY - NOT FOR PRODUCTION USE
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Create database connection
$pdo = getConnection();

// Get all tables in the database
$tables = [];
$stmt = $pdo->query("SHOW TABLES");
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    $tables[] = $row[0];
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Testing - Durian Si Buyunk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .table-container {
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }
        .table-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 15px;
            margin-bottom: 0;
            border-radius: 0.375rem 0.375rem 0 0;
        }
        .table th {
            background: #f8f9fa;
            color: black;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .code-block {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 10px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            overflow-x: auto;
        }
        .nav-pills .nav-link {
            border-radius: 50px;
            margin: 0 5px;
        }
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        .warning-banner {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            color: white;
            padding: 15px;
            border-radius: 0.375rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-seedling me-2"></i>
                Durian Si Buyunk - Database Testing
            </a>
            <div class="navbar-nav ms-auto">
                <a href="index.php" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Warning Banner -->
        <div class="warning-banner">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <h5 class="mb-1">⚠️ HALAMAN TESTING ONLY</h5>
                    <p class="mb-0">Halaman ini hanya untuk testing dan debugging. Jangan digunakan di production!</p>
                </div>
            </div>
        </div>

        <!-- Database Overview -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-database me-2"></i>Database Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Database Information:</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Database Name:</strong> <?= DB_NAME ?></li>
                                    <li><strong>Host:</strong> <?= DB_HOST ?></li>
                                    <li><strong>Total Tables:</strong> <?= count($tables) ?></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Available Tables:</h6>
                                <div class="code-block">
                                    <?= implode(', ', $tables) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Navigation -->
        <div class="mb-4">
            <ul class="nav nav-pills justify-content-center" id="tableNav" role="tablist">
                <?php foreach ($tables as $index => $table): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $index === 0 ? 'active' : '' ?>" 
                                id="<?= $table ?>-tab" data-bs-toggle="pill" 
                                data-bs-target="#<?= $table ?>-content" type="button" role="tab">
                            <i class="fas fa-table me-1"></i><?= $table ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Table Contents -->
        <div class="tab-content" id="tableContent">
            <?php foreach ($tables as $index => $table): ?>
                <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>" 
                     id="<?= $table ?>-content" role="tabpanel">
                    
                    <?php
                    // Get table structure
                    $structureStmt = $pdo->query("DESCRIBE `$table`");
                    $structure = $structureStmt->fetchAll();
                    
                    // Get table data
                    $dataStmt = $pdo->query("SELECT * FROM `$table` ORDER BY id DESC LIMIT 100");
                    $data = $dataStmt->fetchAll();
                    
                    // Get row count
                    $countStmt = $pdo->query("SELECT COUNT(*) as total FROM `$table`");
                    $totalRows = $countStmt->fetch()['total'];
                    ?>
                    
                    <div class="card">
                        <div class="table-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1">
                                        <i class="fas fa-table me-2"></i>Table: <?= $table ?>
                                    </h5>
                                    <small>Total Rows: <?= number_format($totalRows) ?> | Showing: <?= count($data) ?> records</small>
                                </div>
                                <div>
                                    <button class="btn btn-outline-light btn-sm" onclick="toggleStructure('<?= $table ?>')">
                                        <i class="fas fa-cog me-1"></i>Show Structure
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Table Structure (Hidden by default) -->
                        <div id="structure-<?= $table ?>" style="display: none;" class="p-3 bg-light border-bottom">
                            <h6>Table Structure:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Field</th>
                                            <th>Type</th>
                                            <th>Null</th>
                                            <th>Key</th>
                                            <th>Default</th>
                                            <th>Extra</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($structure as $column): ?>
                                            <tr>
                                                <td><code><?= $column['Field'] ?></code></td>
                                                <td><?= $column['Type'] ?></td>
                                                <td><?= $column['Null'] ?></td>
                                                <td><?= $column['Key'] ?></td>
                                                <td><?= $column['Default'] ?></td>
                                                <td><?= $column['Extra'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Table Data -->
                        <?php if (empty($data)): ?>
                            <div class="card-body text-center">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Data Found</h5>
                                <p class="text-muted">Table <?= $table ?> is empty.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-container">
                                <table class="table table-striped table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <?php foreach (array_keys($data[0]) as $column): ?>
                                                <th><?= htmlspecialchars($column) ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $row): ?>
                                            <tr>
                                                <?php foreach ($row as $column => $value): ?>
                                                    <td>
                                                        <?php
                                                        // Special handling for different data types
                                                        if (is_null($value)) {
                                                            echo '<span class="text-muted fst-italic">NULL</span>';
                                                        } elseif ($column === 'gambar' && !empty($value)) {
                                                            $imagePath = getImagePath($value);
                                                            if ($imagePath && file_exists($imagePath)) {
                                                                echo '<img src="' . $imagePath . '" class="img-thumbnail" style="max-width: 50px; max-height: 50px;"> ';
                                                            }
                                                            echo '<small>' . htmlspecialchars($value) . '</small>';
                                                        } elseif (in_array($column, ['created_at', 'updated_at']) && !empty($value)) {
                                                            echo '<small>' . date('d/m/Y H:i', strtotime($value)) . '</small>';
                                                        } elseif (in_array($column, ['harga', 'harga_per_kg']) && is_numeric($value)) {
                                                            echo '<span class="text-success fw-bold">' . formatRupiah($value) . '</span>';
                                                        } elseif (is_string($value) && strlen($value) > 50) {
                                                            echo '<span title="' . htmlspecialchars($value) . '">' . 
                                                                 htmlspecialchars(substr($value, 0, 50)) . '...</span>';
                                                        } else {
                                                            echo htmlspecialchars($value);
                                                        }
                                                        ?>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Quick Stats -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Quick Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php
                            $stats = [
                                'produk' => 'Total Products',
                                'kategori' => 'Categories',
                                'jenis_durian' => 'Durian Types',
                                'users' => 'Users',
                                'stok_history' => 'Stock History Records'
                            ];
                            
                            foreach ($stats as $table => $label):
                                if (in_array($table, $tables)):
                                    $countStmt = $pdo->query("SELECT COUNT(*) as total FROM `$table`");
                                    $count = $countStmt->fetch()['total'];
                            ?>
                                <div class="col-md-2 col-6 mb-3">
                                    <div class="text-center">
                                        <div class="h3 text-primary"><?= number_format($count) ?></div>
                                        <div class="text-muted small"><?= $label ?></div>
                                    </div>
                                </div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleStructure(tableName) {
            const element = document.getElementById('structure-' + tableName);
            const button = event.target.closest('button');
            
            if (element.style.display === 'none') {
                element.style.display = 'block';
                button.innerHTML = '<i class="fas fa-cog me-1"></i>Hide Structure';
            } else {
                element.style.display = 'none';
                button.innerHTML = '<i class="fas fa-cog me-1"></i>Show Structure';
            }
        }

        // Add tooltips to truncated text
        document.addEventListener('DOMContentLoaded', function() {
            const truncatedElements = document.querySelectorAll('[title]');
            truncatedElements.forEach(el => {
                el.style.cursor = 'help';
            });
        });
    </script>
</body>
</html>
