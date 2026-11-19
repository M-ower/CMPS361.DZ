
<?php
$host = 'localhost';
$dbname = 'carpartsdb';
$user = 'postgres';
$port = '5438';

// Read the database password from the environment.
$pass = getenv('CARPARTS_DB_PASSWORD');

if ($pass === false || $pass === '') {
    throw new RuntimeException('Database password is not configured.');
}

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

    $pdo = new PDO($dsn, $user, $pass);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Unable to connect to the database. Please try again later.');
}
