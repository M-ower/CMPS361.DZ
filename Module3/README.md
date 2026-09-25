# CMPS 361 - Module 3

## Module 3

For this module, I worked on connecting PHP with PostgreSQL and connecting PHP to the API I created in CMPS 262.

## 1. PostgreSQL Database Driver

At first, PHP was not able to connect to PostgreSQL because the PostgreSQL driver was not enabled correctly.

I checked my PHP configuration and updated my `php.ini` file. I changed the extension directory and enabled the PostgreSQL extensions.

```ini
extension_dir = "C:\php-8.5.10\ext"
extension=pdo_pgsql
extension=pgsql
```

After making the changes, I tested the connection and it worked successfully.

I also included a video showing the changes I made and the PostgreSQL connection working.

## 2. Connecting PHP to My CMPS 262 API

For this part, I connected my PHP program to the API I created in CMPS 262.

The API endpoint I used was:

```text
http://localhost:5000/api/v1/car_parts
```

I used PHP cURL in `api2.php` to connect to the API and receive the data.

The API successfully returned my car parts information, including the name, category, price, stock quantity, and other information.

The connection works like this:

```text
PHP api2.php
      ↓
CMPS 262 API
      ↓
PostgreSQL Database
      ↓
Car Parts Data
```

## 3. API Results

The screenshot below shows the results from my PHP API connection.

![API Results](api-results.png)

## Files

* `api2.php` - Connects PHP to my CMPS 262 API.
* `conn.php` - Tests the PHP connection to PostgreSQL.
* `check_pgsql.php` - Checks if the PostgreSQL PDO driver is loaded.
* `api-results.png` - Screenshot showing the API results.

## Conclusion

I was able to fix the PostgreSQL driver issue and successfully connect my PHP program to the API from CMPS 262. The API returned the car parts data successfully.