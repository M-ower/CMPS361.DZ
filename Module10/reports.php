
<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/connection.php';

/** @var PDO $pdo */

$username = htmlspecialchars(
    (string) ($_SESSION['username'] ?? ''),
    ENT_QUOTES,
    'UTF-8'
);

try {
    // Inventory summary
    $totalProductsStmt = $pdo->query(
        "SELECT COUNT(*) FROM products"
    );

    if ($totalProductsStmt === false) {
        throw new RuntimeException('Unable to retrieve total products.');
    }

    $totalProducts = (int) $totalProductsStmt->fetchColumn();

    $totalStockStmt = $pdo->query(
        "SELECT COALESCE(SUM(stock_level), 0) FROM products"
    );

    if ($totalStockStmt === false) {
        throw new RuntimeException('Unable to retrieve total stock.');
    }

    $totalStock = (int) $totalStockStmt->fetchColumn();

    $inventoryValueStmt = $pdo->query(
        "SELECT COALESCE(SUM(price * stock_level), 0) FROM products"
    );

    if ($inventoryValueStmt === false) {
        throw new RuntimeException('Unable to retrieve inventory value.');
    }

    $inventoryValue = (float) $inventoryValueStmt->fetchColumn();

    $lowStockStmt = $pdo->query(
        "SELECT COUNT(*) FROM products
         WHERE stock_level > 0 AND stock_level <= 5"
    );

    if ($lowStockStmt === false) {
        throw new RuntimeException('Unable to retrieve low stock count.');
    }

    $lowStock = (int) $lowStockStmt->fetchColumn();

    $outOfStockStmt = $pdo->query(
        "SELECT COUNT(*) FROM products
         WHERE stock_level = 0"
    );

    if ($outOfStockStmt === false) {
        throw new RuntimeException('Unable to retrieve out of stock count.');
    }

    $outOfStock = (int) $outOfStockStmt->fetchColumn();

    // Retrieve products for the report
    $stmt = $pdo->query(
        "SELECT product_id, name, description, price,
                stock_level, created_at
         FROM products
         ORDER BY stock_level ASC, product_id DESC"
    );

    if ($stmt === false) {
        throw new RuntimeException('Unable to retrieve inventory products.');
    }

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Reports query failed: ' . $e->getMessage());
    exit('Unable to load inventory reports.');
} catch (RuntimeException $e) {
    error_log('Reports query failed: ' . $e->getMessage());
    exit('Unable to load inventory reports.');
}
?>
