
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
    $totalProducts = (int) $pdo->query(
        "SELECT COUNT(*) FROM products"
    )->fetchColumn();

    $totalStock = (int) $pdo->query(
        "SELECT COALESCE(SUM(stock_level), 0) FROM products"
    )->fetchColumn();

    $inventoryValue = (float) $pdo->query(
        "SELECT COALESCE(SUM(price * stock_level), 0) FROM products"
    )->fetchColumn();

} catch (PDOException $e) {
    error_log('Dashboard query failed: ' . $e->getMessage());
    exit('Unable to load dashboard information.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Car Parts Inventory</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="site-header">
    <a class="brand" href="index.php">CAR<span>PARTS</span> INVENTORY</a>

    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="services.php">Dashboard</a>
        <a href="product.php">Inventory</a>
        <a href="addproduct.php">Add Product</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="container">

    <section class="page-title">
        <h1>Welcome, <?php echo $username; ?>!</h1>
        <p>Manage your automotive inventory from one place.</p>
    </section>

    <section class="card-grid">
        <article class="card">
            <h3>Total Products</h3>
            <div class="stat-number"><?php echo $totalProducts; ?></div>
            <p>Different products in inventory</p>
        </article>

        <article class="card">
            <h3>Total Stock</h3>
            <div class="stat-number"><?php echo $totalStock; ?></div>
            <p>Individual parts currently available</p>
        </article>

        <article class="card">
            <h3>Inventory Value</h3>
            <div class="stat-number">
                $<?php echo number_format($inventoryValue, 2); ?>
            </div>
            <p>Total value based on price and stock</p>
        </article>
    </section>

    <section class="section-heading">
        <h2>Inventory Management</h2>
        <p>Choose what you would like to do next.</p>
    </section>

    <section class="card-grid">
        <article class="card">
            <h3>View Inventory</h3>
            <p>Browse your saved car parts, prices, and stock levels.</p>
            <br>
            <a href="product.php" class="button button-secondary">
                View Products
            </a>
        </article>

        <article class="card">
            <h3>Add a Product</h3>
            <p>Enter a new car part and save it to the PostgreSQL database.</p>
            <br>
            <a href="addproduct.php" class="button">
                Add Product
            </a>
        </article>
    </section>

</main>

<footer class="site-footer">
    Car Parts Inventory Management | CMPS361 Module 8
</footer>

</body>
</html>
