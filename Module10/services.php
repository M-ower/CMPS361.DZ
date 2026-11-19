
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

} catch (PDOException $e) {
    error_log('Dashboard query failed: ' . $e->getMessage());
    exit('Unable to load dashboard information.');
} catch (RuntimeException $e) {
    error_log('Dashboard query failed: ' . $e->getMessage());
    exit('Unable to load dashboard information.');
}
?>
