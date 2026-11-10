<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once 'connection.php';

// Search and pagination settings
$search = trim($_GET['search'] ?? '');
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

if ($page === false || $page === null || $page < 1) {
    $page = 1;
}

$limit = 10;
$offset = ($page - 1) * $limit;

try {
    // Count matching products
    $countSql = "
        SELECT COUNT(*)
        FROM products
        WHERE name ILIKE :search_name
        OR description ILIKE :search_description
    ";

    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute([
        ':search_name' => "%$search%",
        ':search_description' => "%$search%"
    ]);

    $totalProducts = (int) $countStmt->fetchColumn();

    $totalPages = max(1, (int) ceil($totalProducts / $limit));

    // Keep requested page within available pages
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

    // Retrieve products for current page
    $sql = "
        SELECT product_id, name, description, price,
               stock_level, created_at
        FROM products
        WHERE name ILIKE :search_name
        OR description ILIKE :search_description
        ORDER BY product_id DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':search_name', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':search_description', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();

    // Fetch inventory records
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Inventory query failed: ' . $e->getMessage());
    exit('Unable to load the inventory. Please try again later.');
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
    <title>Product Inventory | Car Parts</title>
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
        <h1>Product Inventory</h1>
        <p>Welcome, <?php echo $username; ?>. View and manage your car parts.</p>
    </section>

    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success">
            Product added successfully!
        </div>
    <?php endif; ?>

    <section class="panel">
        <form method="GET" action="product.php">
            <div class="form-group">
                <label for="search">Search Products</label>
                <input
                    type="search"
                    id="search"
                    name="search"
                    placeholder="Search by product name or description..."
                    value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                >
            </div>

            <button type="submit" class="button button-secondary">
                Search
            </button>

            <a href="product.php" class="button">
                Clear Search
            </a>

            <a href="addproduct.php" class="button">
                + Add Product
            </a>
        </form>
    </section>

    <section class="section-heading">
        <h2>Inventory Records</h2>
        <p>
            <?php echo $totalProducts; ?>
            product(s) found.
            Showing page <?php echo $page; ?> of <?php echo $totalPages; ?>.
        </p>
    </section>

    <div class="table-wrapper">
        <table>
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
                        <tr>
                            <td>
                                #<?php echo (int) $product['product_id']; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                            </td>

                            <td>
                                $<?php echo number_format((float) $product['price'], 2); ?>
                            </td>

                            <td>
                                <?php echo (int) $product['stock_level']; ?>
                            </td>

                            <td>
                                <?php if ((int) $product['stock_level'] === 0): ?>
                                    <span class="badge badge-low">Out of Stock</span>
                                <?php elseif ((int) $product['stock_level'] <= 5): ?>
                                    <span class="badge badge-low">Low Stock</span>
                                <?php else: ?>
                                    <span class="badge">In Stock</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty-state">
                            No products found. Add your first car part to get started.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Inventory pages">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php
                $url = 'product.php?' . http_build_query([
                    'search' => $search,
                    'page' => $i
                ]);
                ?>

                <?php if ($i === $page): ?>
                    <span class="active" aria-current="page">
                        <?php echo $i; ?>
                    </span>
                <?php else: ?>
                    <a href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>

    <p>
        <a href="services.php">← Back to Dashboard</a>
    </p>

</main>

<footer class="site-footer">
    Car Parts Inventory Management | CMPS361 Module 8
</footer>

</body>
</html>