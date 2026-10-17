
<?php
// Module 3: Get car parts from the API.
$url = "http://localhost:5000/api/v1/car_parts";

$session = curl_init();
curl_setopt($session, CURLOPT_URL, $url);
curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($session);

if ($response === false) {
    die("Error connecting to API: " . curl_error($session));
}

$httpCode = curl_getinfo($session, CURLINFO_HTTP_CODE);
curl_close($session);

if ($httpCode < 200 || $httpCode >= 300) {
    die("The API returned HTTP status: " . $httpCode);
}

$data = json_decode($response, true);

if (!is_array($data)) {
    die("The API did not return valid JSON data.");
}

if (isset($data["data"]) && is_array($data["data"])) {
    $data = $data["data"];
}

$data = array_values(array_filter($data, "is_array"));

// Sorting setup.
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

$sortColumn = isset($_GET["sort"]) &&
    in_array($_GET["sort"], $allowedColumns, true)
    ? $_GET["sort"]
    : "name";

$sortOrder = isset($_GET["order"]) &&
    strtolower($_GET["order"]) === "desc"
    ? "desc"
    : "asc";

// Sort the data.
usort($data, function ($a, $b) use ($sortColumn, $sortOrder) {
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
$totalRecords = count($data);
$totalPages = (int)ceil($totalRecords / $limit);

$currentPage = isset($_GET["page"])
    ? (int)$_GET["page"]
    : 1;

$currentPage = max(1, min($currentPage, max(1, $totalPages)));

$startIndex = ($currentPage - 1) * $limit;
$pageData = array_slice($data, $startIndex, $limit);

// Toggle sorting order.
function toggleOrder($currentOrder) {
    return $currentOrder === "asc" ? "desc" : "asc";
}

// Keep sorting settings in pagination links.
function pageUrl($page, $column, $order) {
    return "?page=" . $page
        . "&sort=" . urlencode($column)
        . "&order=" . urlencode($order);
}

// GridView columns.
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

echo "<h1>Car Parts GridView - Module 3</h1>";

echo "<table border='1' cellpadding='10'>";
echo "<thead><tr>";

foreach ($columns as $column => $label) {
    $nextOrder = ($sortColumn === $column)
        ? toggleOrder($sortOrder)
        : "asc";

    $link = pageUrl($currentPage, $column, $nextOrder);

    echo "<th><a href='" .
        htmlspecialchars($link, ENT_QUOTES, "UTF-8") .
        "'>" .
        htmlspecialchars($label, ENT_QUOTES, "UTF-8") .
        "</a></th>";
}

echo "</tr></thead><tbody>";

foreach ($pageData as $post) {
    echo "<tr>";

    foreach ($columns as $column => $label) {
        $value = $post[$column] ?? "";

        echo "<td>" .
            htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8") .
            "</td>";
    }

    echo "</tr>";
}

echo "</tbody></table>";

// Pagination links.
echo "<div style='margin-top: 20px;'>";

if ($currentPage > 1) {
    echo "<a href='" .
        htmlspecialchars(
            pageUrl($currentPage - 1, $sortColumn, $sortOrder),
            ENT_QUOTES,
            "UTF-8"
        ) .
        "'>Previous</a> ";
}

for ($i = 1; $i <= $totalPages; $i++) {
    if ($i === $currentPage) {
        echo "<strong>" . $i . "</strong> ";
    } else {
        echo "<a href='" .
            htmlspecialchars(
                pageUrl($i, $sortColumn, $sortOrder),
                ENT_QUOTES,
                "UTF-8"
            ) .
            "'>" . $i . "</a> ";
    }
}

if ($currentPage < $totalPages) {
    echo "<a href='" .
        htmlspecialchars(
            pageUrl($currentPage + 1, $sortColumn, $sortOrder),
            ENT_QUOTES,
            "UTF-8"
        ) .
        "'>Next</a>";
}

echo "</div>";

echo "<p><strong>Total Records: " .
    $totalRecords .
    "</strong></p>";
?>