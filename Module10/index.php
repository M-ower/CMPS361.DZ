
<?php
session_start();

$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn
    ? htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8')
    : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Parts Inventory | Home</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="site-header">
    <a class="brand" href="index.php">CAR<span>PARTS</span> INVENTORY</a>

    <nav class="nav-links">
        <a href="index.php">Home</a>

        <?php if ($isLoggedIn): ?>
            <a href="services.php">Dashboard</a>
            <a href="product.php">Inventory</a>
            <a href="addproduct.php">Add Product</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<main class="container">

    <section class="hero">
        <h1>Manage Your Car Parts Inventory in One Place</h1>

        <p>
            Keep track of automotive parts, manage product information,
            monitor stock levels, and organize your inventory through
            one simple application.
        </p>

        <?php if ($isLoggedIn): ?>
            <p>Welcome back, <?php echo $username; ?>!</p>
            <a class="button" href="services.php">Open Dashboard</a>
            <a class="button button-light" href="product.php">View Inventory</a>
        <?php else: ?>
            <a class="button" href="login.php">Login to Get Started</a>
        <?php endif; ?>
    </section>

    <section class="section-heading">
        <h2>What You Can Do</h2>
        <p>Tools to help organize and manage your automotive inventory.</p>
    </section>

    <section class="card-grid">
        <article class="card">
            <h3>Product Inventory</h3>
            <p>View car parts, descriptions, prices, and available stock.</p>
        </article>

        <article class="card">
            <h3>Add New Products</h3>
            <p>Enter new automotive parts and save them directly to the database.</p>
        </article>

        <article class="card">
            <h3>Account Security</h3>
            <p>Use secure login sessions to protect inventory management pages.</p>
        </article>
    </section>

    <section class="section-heading">
        <h2>Built for Simple Inventory Management</h2>
    </section>

    <section class="card">
        <p>
            This application is built with PHP and PostgreSQL.
            It connects the login system to a database so authenticated
            users can add and view inventory records.
        </p>
    </section>

</main>

<footer class="site-footer">
    Car Parts Inventory Management | CMPS361 Module 8
</footer>

</body>
</html>
