
<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once 'connection.php';

$username = htmlspecialchars(
    $_SESSION['username'],
    ENT_QUOTES,
    'UTF-8'
);

try {
    // Inventory summary
    $totalProducts = (int) $pdo->query(
        "SELECT COUNT(*) FROM products"
    )->fetchColumn();

    $totalStock = (int) $pdo->query(
        "SELECT COALESCE(SUM(stock_level), 0) FROM products"
    )->fetchColumn();

    $inventoryValue = (float) $pdo->query(
        "SELECT COALESCE(SUM(price * stock_level), 0) FROM products"
    )->fetchColumn();

    $lowStock = (int) $pdo->query(
        "SELECT COUNT(*) FROM products
         WHERE stock_level > 0 AND stock_level <= 5"
    )->fetchColumn();

    $outOfStock = (int) $pdo->query(
        "SELECT COUNT(*) FROM products
         WHERE stock_level = 0"
    )->fetchColumn();

    // Retrieve products for the report
    $stmt = $pdo->query(
        "SELECT product_id, name, description, price,
                stock_level, created_at
         FROM products
         ORDER BY stock_level ASC, product_id DESC"
    );

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Reports query failed: ' . $e->getMessage());
    exit('Unable to load inventory reports.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inventory Reports | Car Parts Inventory</title>

    <link rel="stylesheet" href="styles.css">
    <script src="script.js" defer></script>
</head>
<body>

<header class="site-header">
    <a class="brand" href="index.php">
        CAR<span>PARTS</span> INVENTORY
    </a>

    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="services.php">Dashboard</a>
        <a href="product.php">Inventory</a>
        <a href="addproduct.php">Add Product</a>
        <a href="reports.php">Reports</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="container">

    <section class="page-title">
        <h1>Inventory Reports & Insights</h1>
        <p>
            Welcome, <?php echo $username; ?>.
            Review inventory levels and product information.
        </p>
    </section>

    <section class="card-grid">

        <article class="card">
            <h3>Total Products</h3>
            <div class="stat-number">
                <?php echo $totalProducts; ?>
            </div>
            <p>Products currently registered</p>
        </article>

        <article class="card">
            <h3>Total Stock</h3>
            <div class="stat-number">
                <?php echo $totalStock; ?>
            </div>
            <p>Individual parts available</p>
        </article>

        <article class="card">
            <h3>Inventory Value</h3>
            <div class="stat-number">
                $<?php echo number_format($inventoryValue, 2); ?>
            </div>
            <p>Total value of available stock</p>
        </article>

        <article class="card">
            <h3>Low Stock</h3>
            <div class="stat-number">
                <?php echo $lowStock; ?>
            </div>
            <p>Products with 1–5 items remaining</p>
        </article>

        <article class="card">
            <h3>Out of Stock</h3>
            <div class="stat-number">
                <?php echo $outOfStock; ?>
            </div>
            <p>Products that need restocking</p>
        </article>

    </section>

    <section class="section-heading">
        <h2>Product Inventory Report</h2>
        <p>Search and filter your current inventory information.</p>
    </section>

    <section class="panel">

        <div class="form-group">
            <label for="reportSearch">Search Products</label>
            <input
                type="search"
                id="reportSearch"
                placeholder="Search by product name or description..."
            >
        </div>

        <div class="form-group">
            <label for="stockFilter">Filter by Stock Status</label>
            <select id="stockFilter">
                <option value="all">All Products</option>
                <option value="available">In Stock</option>
                <option value="low">Low Stock</option>
                <option value="out">Out of Stock</option>
            </select>
        </div>

        <p id="reportCount" aria-live="polite">
            Showing <?php echo count($products); ?> products
        </p>

    </section>

    <div class="table-wrapper">
        <table id="reportTable">
            <thead>
                <tr>
                    <th>Part ID</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($products) > 0): ?>

                    <?php foreach ($products as $product): ?>

                        <?php
                        $stock = (int) $product['stock_level'];

                        if ($stock === 0) {
                            $status = 'out';
                            $statusLabel = 'Out of Stock';
                        } elseif ($stock <= 5) {
                            $status = 'low';
                            $statusLabel = 'Low Stock';
                        } else {
                            $status = 'available';
                            $statusLabel = 'In Stock';
                        }
                        ?>

                        <tr
                            data-status="<?php echo $status; ?>"
                            data-search="<?php echo htmlspecialchars(
                                strtolower(
                                    $product['name'] . ' ' .
                                    $product['description']
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            ); ?>"
                        >
                            <td>
                                <?php echo (int) $product['product_id']; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars(
                                    $product['name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars(
                                    $product['description'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ); ?>
                            </td>

                            <td>
                                $<?php echo number_format(
                                    (float) $product['price'],
                                    2
                                ); ?>
                            </td>

                            <td><?php echo $stock; ?></td>

                            <td>
                                <?php if ($status === 'out' || $status === 'low'): ?>
                                    <span class="badge badge-low">
                                        <?php echo $statusLabel; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge">
                                        <?php echo $statusLabel; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6" class="empty-state">
                            No products are currently available.
                        </td>
                    </tr>

                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <p id="noResults" class="empty-state" hidden>
        No products match your search or filter.
    </p>

    <section class="panel report-actions">
        <a href="javascript:history.back()" class="button button-secondary">
            Back
        </a>

        <a href="index.php" class="button">
            Home
        </a>

        <a href="product.php" class="button button-secondary">
            View Inventory
        </a>
    </section>

</main>

<footer class="site-footer">
    Car Parts Inventory Management | CMPS361 Module 9
</footer>

</body>
</html>
