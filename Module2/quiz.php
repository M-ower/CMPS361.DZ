<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $number = $_POST["number"];

    if ($number % 2 == 0) {
        echo "<p>The number $number is even.</p>";
    } else {
        echo "<p>The number $number is odd.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Even or Odd Quiz</title>
</head>
<body>

    <h1>Even or Odd Quiz</h1>

    <form method="POST" action="">
        <label for="number">Enter a number:</label>
        <input type="number" id="number" name="number" required>

        <button type="submit">Check Number</button>
    </form>

</body>
</html>