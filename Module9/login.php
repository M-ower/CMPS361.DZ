
<?php
session_start();

if (isset($_SESSION['username'])) {
    header("Location: services.php");
    exit;
}

$error = isset($_GET['error']) && $_GET['error'] === 'invalid';
$loggedOut = isset($_GET['logged_out']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Car Parts Inventory</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="site-header">
    <a class="brand" href="index.php">CAR<span>PARTS</span> INVENTORY</a>
    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
    </nav>
</header>

<main class="container">
    <section class="panel" style="max-width: 480px; margin: 45px auto;">
        <div class="page-title">
            <h1>Welcome Back</h1>
            <p>Log in to manage your car parts inventory.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                Invalid username or password. Please try again.
            </div>
        <?php endif; ?>

        <?php if ($loggedOut): ?>
            <div class="alert alert-success">
                You have successfully logged out.
            </div>
        <?php endif; ?>

        <form action="authentication.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    autocomplete="username"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button type="submit" class="button" style="width: 100%;">
                Login
            </button>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            <a href="index.php">Back to Home</a>
        </p>
    </section>
</main>

<footer class="site-footer">
    Car Parts Inventory Management | CMPS361 Module 8
</footer>

</body>
</html>
