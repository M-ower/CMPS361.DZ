<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $age = $_POST["age"];

    echo "Hello, $name! You are $age years old.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMPS 361 - PHP Form</title>
</head>
<body>

    <h1>PHP Form</h1>

    <form method="POST" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <br><br>

        <label for="age">Age:</label>
        <input type="text" id="age" name="age" required>

        <br><br>

        <button type="submit">Submit</button>
    </form>

</body>
</html>