
<?php
session_start();

// Only logged-in users can access this page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
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
    <title>Car Parts App - Services</title>
</head>
<body>
    <h1>Welcome, <?php echo $username; ?>!</h1>
    <p>You have successfully logged in to the Car Parts App.</p>

    <h2>Services</h2>
    <p>You are signed in and can access the application services.</p>

    <a href="logout.php">Logout</a>
</body>
</html>
