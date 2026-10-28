
<?php
session_start();

// Only accept form submissions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

// Database connection
$host = 'localhost';
$db = 'carpartsdb';
$dbUser = 'postgres';
$dbPassword = getenv('CARPARTS_DB_PASSWORD');
$port = '5438';

if ($dbPassword === false || $dbPassword === '') {
    http_response_code(500);
    exit("Database password is not configured.");
}

$conn = pg_connect(
    "host=$host port=$port dbname=$db user=$dbUser password=$dbPassword"
);

if (!$conn) {
    http_response_code(500);
    exit("Database connection failed.");
}

// Get submitted credentials
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    pg_close($conn);
    exit("Please enter both username and password.");
}

// Find the account safely
$sql = "SELECT username, password FROM users WHERE username = $1";
$result = pg_query_params($conn, $sql, array($username));

if ($result === false) {
    pg_close($conn);
    http_response_code(500);
    exit("Unable to check the login. Please try again.");
}

if (pg_num_rows($result) > 0) {
    $user = pg_fetch_assoc($result);

    if (hash_equals(
        $user['password'],
        crypt($password, $user['password'])
    )) {
        session_regenerate_id(true);
        $_SESSION['username'] = $user['username'];

        pg_free_result($result);
        pg_close($conn);

        header("Location: services.php");
        exit;
    }
}

// Don't reveal whether the username or password was incorrect
pg_free_result($result);
pg_close($conn);

header("Location: login.php?error=invalid");
exit;
?>
