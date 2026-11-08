
<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once 'connection.php';

$errors = [];

$name = '';
$description = '';
$price = '';
$stockLevel = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $stockLevel = trim($_POST['stock_level'] ?? '');

    // Validate product name
    if ($name === '' || strlen($name) > 100) {
        $errors[] = 'Product name is required and must be 100 characters or fewer.';
    }

    // Validate price
    if ($price === '' || !is_numeric($price) || (float) $price < 0) {
        $errors[] = 'Enter a valid price that is zero or greater.';
    }

    // Validate stock
    $validatedStock = filter_var($stockLevel, FILTER_VALIDATE_INT);

    if ($validatedStock === false || $validatedStock < 0) {
        $errors[] = 'Stock level must be a whole number of zero or greater.';
    }

    if (is_numeric($price) && (float) $price > 99999999.99) {
        $errors[] = 'Price is too large.';
    }

    if (!$errors) {
        try {
            $sql = "
                INSERT INTO products (name, description, price, stock_level)
                VALUES (:name, :description, :price, :stock_level)
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'stock_level' => $validatedStock
            ]);

            // Redirect after successful insertion
            header("Location: product.php?added=1");
            exit;

        } catch (PDOException $e) {
            error_log('Product insertion failed: ' . $e->getMessage());
            $errors[] = 'Unable to save the product. Please try again.';
        }
    }
}

$username = htmlspecialchars(
    $_SESSION['username'],
    ENT_QUOTES,
    'UTF-8'
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | Car Parts Inventory</title>
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
        <h1>Add a New Car Part</h1>
        <p>Welcome, <?php echo $username; ?>. Enter the product details below.</p>
    </section>

    <section class="panel" style="max-width: 750px;">

        <?php if ($errors): ?>
            <div class="alert alert-error">
                <strong>Please correct the following:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="addproduct.php">

            <div class="form-group">
                <label for="name">Product Name *</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    maxlength="100"
                    value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="Example: Brake Pads"
                    required
                >
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    placeholder="Enter product details, vehicle compatibility, or other information."
                ><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

            <div class="form-group">
                <label for="price">Price ($) *</label>
                <input
                    type="number"
                    id="price"
                    name="price"
                    min="0"
                    max="99999999.99"
                    step="0.01"
                    value="<?php echo htmlspecialchars($price, ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="Example: 49.99"
                    required
                >
            </div>

            <div class="form-group">
                <label for="stock_level">Stock Level *</label>
                <input
                    type="number"
                    id="stock_level"
                    name="stock_level"
                    min="0"
                    step="1"
                    value="<?php echo htmlspecialchars($stockLevel, ENT_QUOTES, 'UTF-8'); ?>"
                    placeholder="Example: 25"
                    required
                >
            </div>

            <button type="submit" class="button">
                Save Product
            </button>

            <a href="product.php" class="button button-secondary">
                Cancel
            </a>

        </form>
    </section>

    <p><a href="services.php">← Back to Dashboard</a></p>

</main>

<footer class="site-footer">
    Car Parts Inventory Management | CMPS361 Module 8
</footer>

</body>
</html>
