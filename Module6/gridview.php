
<?php
// Module 6: Styled, searchable Car Parts GridView.
$apiUrl = "http://localhost:5000/api/v1/car_parts";

// Fetch data from the API.
$session = curl_init();

curl_setopt($session, CURLOPT_URL, $apiUrl);
curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
curl_setopt($session, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($session, CURLOPT_TIMEOUT, 15);

$response = curl_exec($session);

if ($response === false) {
    $error = curl_error($session);
    curl_close($session);
    die("Error connecting to API: " . htmlspecialchars($error));
}

$httpCode = curl_getinfo($session, CURLINFO_HTTP_CODE);
curl_close($session);

if ($httpCode < 200 || $httpCode >= 300) {
    die("The API returned HTTP status: " . $httpCode);
}

// Decode the JSON response.
$data = json_decode($response, true);

if (!is_array($data)) {
    die("The API did not return valid JSON data.");
}

// Support an API response wrapped in a data property.
if (isset($data["data"]) && is_array($data["data"])) {
    $data = $data["data"];
}

$data = array_values(array_filter($data, "is_array"));

// Allowed columns for sorting and display.
$allowedColumns = [
    "id",
    "name",
    "category",
    "price",
    "stock_quantity",
    "sold_count",
    "view_count",
    "created_at"
];

// Read the search and filter options.
$search = trim($_GET["search"] ?? "");
$selectedCategory = trim($_GET["category"] ?? "");
$lowStockOnly = isset($_GET["low_stock"])
    && $_GET["low_stock"] === "1";

// Get the available categories for the dropdown.
$categories = [];

foreach ($data as $part) {
    if (isset($part["category"]) && $part["category"] !== "") {
        $categories[] = (string)$part["category"];
    }
}

$categories = array_values(array_unique($categories));
sort($categories, SORT_NATURAL | SORT_FLAG_CASE);

// Filter the data using the search menu.
$filteredData = array_filter($data, function ($part) use (
    $search,
    $selectedCategory,
    $lowStockOnly
) {
    // Search across the car-part fields.
    if ($search !== "") {
        $searchMatch = false;

        foreach ($part as $value) {
            if (
                is_scalar($value)
                && stripos((string)$value, $search) !== false
            ) {
                $searchMatch = true;
                break;
            }
        }

        if (!$searchMatch) {
            return false;
        }
    }

    // Filter by category.
    if (
        $selectedCategory !== ""
        && ($part["category"] ?? "") !== $selectedCategory
    ) {
        return false;
    }

    // Original feature: show only parts with low stock.
    if (
        $lowStockOnly
        && (float)($part["stock_quantity"] ?? 0) > 10
    ) {
        return false;
    }

    return true;
});

$filteredData = array_values($filteredData);

// Sorting setup.
$sortColumn = isset($_GET["sort"])
    && in_array($_GET["sort"], $allowedColumns, true)
    ? $_GET["sort"]
    : "name";

$sortOrder = isset($_GET["order"])
    && strtolower($_GET["order"]) === "desc"
    ? "desc"
    : "asc";

// Sort the filtered results.
usort($filteredData, function ($a, $b) use ($sortColumn, $sortOrder) {
    $valueA = $a[$sortColumn] ?? "";
    $valueB = $b[$sortColumn] ?? "";

    if (is_numeric($valueA) && is_numeric($valueB)) {
        $comparison = (float)$valueA <=> (float)$valueB;
    } else {
        $comparison = strnatcasecmp(
            (string)$valueA,
            (string)$valueB
        );
    }

    return $sortOrder === "asc" ? $comparison : -$comparison;
});

// Pagination setup.
$limit = 10;
$totalRecords = count($filteredData);
$totalPages = max(1, (int)ceil($totalRecords / $limit));

$currentPage = isset($_GET["page"])
    ? (int)$_GET["page"]
    : 1;

$currentPage = max(1, min($currentPage, $totalPages));

$startIndex = ($currentPage - 1) * $limit;
$pageData = array_slice($filteredData, $startIndex, $limit);

// Toggle the sort order.
function toggleOrder($currentOrder)
{
    return $currentOrder === "asc" ? "desc" : "asc";
}

// Build links while preserving search and filter settings.
function buildPageUrl($page, $sortColumn, $sortOrder)
{
    $params = [
        "page" => $page,
        "sort" => $sortColumn,
        "order" => $sortOrder,
        "search" => trim($_GET["search"] ?? ""),
        "category" => trim($_GET["category"] ?? "")
    ];

    if (isset($_GET["low_stock"]) && $_GET["low_stock"] === "1") {
        $params["low_stock"] = "1";
    }

    return "?" . http_build_query($params);
}

// Column labels for the gridview.
$columns = [
    "id" => "ID",
    "name" => "Part Name",
    "category" => "Category",
    "price" => "Price",
    "stock_quantity" => "Stock Quantity",
    "sold_count" => "Sold Count",
    "view_count" => "View Count",
    "created_at" => "Created At"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Parts GridView - Module 6</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">

    <header class="page-header">
        <h1>Car Parts Inventory</h1>
        <p>Search, filter, sort, and manage your car parts data.</p>
    </header>

    <section class="search-panel">
        <h2>Search Menu</h2>

        <form method="get" action="gridview.php" class="search-form">

            <div class="form-group search-field">
                <label for="search">Search car parts</label>
                <input
                    type="text"
                    id="search"
                    name="search"
                    placeholder="Enter a name, category, or value..."
                    value="<?php echo htmlspecialchars($search, ENT_QUOTES, "UTF-8"); ?>"
                >
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="">All Categories</option>

                    <?php foreach ($categories as $category): ?>
                        <option
                            value="<?php echo htmlspecialchars($category, ENT_QUOTES, "UTF-8"); ?>"
                            <?php echo $selectedCategory === $category ? "selected" : ""; ?>
                        >
                            <?php echo htmlspecialchars($category, ENT_QUOTES, "UTF-8"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group checkbox-group">
                <label for="low_stock">
                    <input
                        type="checkbox"
                        id="low_stock"
                        name="low_stock"
                        value="1"
                        <?php echo $lowStockOnly ? "checked" : ""; ?>
                    >
                    Low stock only (10 or fewer)
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Search
                </button>

                <a href="gridview.php" class="btn btn-secondary">
                    Clear Filters
                </a>
            </div>

        </form>
    </section>

    <section class="results-panel">
        <div class="results-heading">
            <h2>Car Parts GridView</h2>
            <span class="record-count">
                Matching Records: <?php echo $totalRecords; ?>
            </span>
        </div>

        <?php if (count($pageData) > 0): ?>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <?php foreach ($columns as $column => $label): ?>
                                <?php
                                $nextOrder = $sortColumn === $column
                                    ? toggleOrder($sortOrder)
                                    : "asc";

                                $sortUrl = buildPageUrl(
                                    $currentPage,
                                    $column,
                                    $nextOrder
                                );
                                ?>
                                <th>
                                    <a href="<?php echo htmlspecialchars($sortUrl, ENT_QUOTES, "UTF-8"); ?>">
                                        <?php echo htmlspecialchars($label, ENT_QUOTES, "UTF-8"); ?>

                                        <?php if ($sortColumn === $column): ?>
                                            <?php echo $sortOrder === "asc" ? "▲" : "▼"; ?>
                                        <?php endif; ?>
                                    </a>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($pageData as $part): ?>
                            <?php
                            $stock = (float)($part["stock_quantity"] ?? 0);
                            $rowClass = $stock <= 10 ? "low-stock" : "";
                            ?>
                            <tr class="<?php echo $rowClass; ?>">

                                <?php foreach ($columns as $column => $label): ?>
                                    <td>
                                        <?php
                                        $value = $part[$column] ?? "";

                                        if ($column === "price" && is_numeric($value)) {
                                            echo "$" . number_format((float)$value, 2);
                                        } else {
                                            echo htmlspecialchars(
                                                (string)$value,
                                                ENT_QUOTES,
                                                "UTF-8"
                                            );
                                        }

                                        if ($column === "stock_quantity" && $stock <= 10) {
                                            echo ' <span class="stock-badge">Low Stock</span>';
                                        }
                                        ?>
                                    </td>
                                <?php endforeach; ?>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <nav class="pagination" aria-label="Gridview pages">

                <?php if ($currentPage > 1): ?>
                    <a href="<?php echo htmlspecialchars(buildPageUrl($currentPage - 1, $sortColumn, $sortOrder), ENT_QUOTES, "UTF-8"); ?>">
                        Previous
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i === $currentPage): ?>
                        <strong><?php echo $i; ?></strong>
                    <?php else: ?>
                        <a href="<?php echo htmlspecialchars(buildPageUrl($i, $sortColumn, $sortOrder), ENT_QUOTES, "UTF-8"); ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?php echo htmlspecialchars(buildPageUrl($currentPage + 1, $sortColumn, $sortOrder), ENT_QUOTES, "UTF-8"); ?>">
                        Next
                    </a>
                <?php endif; ?>

            </nav>

        <?php else: ?>

            <p class="no-results">
                No car parts match your search or filter choices.
            </p>

        <?php endif; ?>

        <p class="total-records">
            Total matching records: <?php echo $totalRecords; ?>
        </p>

        <p class="feature-note">
            Original feature: Low-stock parts are highlighted so they are
            easier to identify for restocking.
        </p>

    </section>

</div>

</body>
</html>