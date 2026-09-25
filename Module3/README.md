CMPS 361 - Module 3
Module 3 Overview

For Module 3, I worked on connecting PHP with PostgreSQL and connecting PHP to the API that I created in CMPS 262.

1. PostgreSQL Database Driver

At first, PHP was not able to use the PostgreSQL database driver correctly.

I checked my PHP configuration and found that the PostgreSQL extensions needed to be enabled. I updated my php.ini file and added the correct extension directory and PostgreSQL extensions.
extension_dir = "C:\php-8.5.10\ext"
extension=pdo_pgsql
extension=pgsql

After making these changes, I tested the PostgreSQL connection and it worked successfully.

I also created check_pgsql.php to check if the PostgreSQL PDO driver was loaded.

I recorded a video showing the changes I made and the PostgreSQL connection working.

2. Connecting PHP to My CMPS 262 API

For the second part of the module, I connected my PHP program to the API that I created in CMPS 262.

The API endpoint I used was:


http://localhost:5000/api/v1/car_parts


I used PHP cURL in api2.php to connect to the API and receive the data.

The API successfully returned information from my car parts database, including the part name, category, price, stock quantity, sold count, view count, and creation date.

The connection works like this:


PHP api2.php
      ↓
CMPS 262 Express API
      ↓
PostgreSQL Database
      ↓
Car Parts Data
3. API Results

The PHP API connection worked successfully and returned the car parts data.

The screenshot below shows the results from api2.php.




Module 3 Files
api2.php - Connects PHP to my CMPS 262 API using cURL.
conn.php - Tests the PHP connection to PostgreSQL.
check_pgsql.php - Checks if the PostgreSQL PDO driver is loaded.
Screenshot 2026-09-24 223533.png - Screenshot showing the API results.
Conclusion

I was able to fix the PostgreSQL driver issue and successfully connect my PHP program to the API from CMPS 262. The API returned the car parts data successfully.