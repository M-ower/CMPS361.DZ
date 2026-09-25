<?php

echo "PHP Version: " . PHP_VERSION . "<br><br>";

if (extension_loaded("pdo_pgsql")) {
    echo "PDO PostgreSQL driver is loaded.";
} else {
    echo "PDO PostgreSQL driver is NOT loaded.";
}

?>