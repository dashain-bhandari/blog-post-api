<?php
// Load environment variables
$postgresUser = getenv('DB_USER');
$postgresPassword = getenv('DB_PASSWORD');
$postgresDb = getenv('DB_NAME');
$defaultUser = "superadmin@gmail.com";
$defaultPassword = "superadmin";
$secretKey = getenv('SECRET_KEY');
$host=getenv("DB_HOST");
$port=getenv("DB_PORT");

// Database connection
$dsn =  "pgsql:host=$host;port=$port;dbname=$postgresDb;";
try {
    $pdo = new PDO($dsn, $postgresUser, $postgresPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to the database successfully." . PHP_EOL;
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . PHP_EOL);
}

// Create `users` table if it doesn't exist
$createTableQuery = "
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(255),
    email VARCHAR(255),
    role VARCHAR(50) DEFAULT 'user'
);
";
try {
    $pdo->exec($createTableQuery);
    echo "Table 'users' ensured to exist." . PHP_EOL;
} catch (PDOException $e) {
    die("Failed to create table: " . $e->getMessage() . PHP_EOL);
}

// Hash the default password
$hashedPassword = password_hash($defaultPassword, PASSWORD_BCRYPT);

// Insert the superadmin user if not already present
$insertUserQuery = "
INSERT INTO users (username, password, role)
VALUES (:username, :password, :role)
ON CONFLICT (username) DO NOTHING;
";

try {
    $stmt = $pdo->prepare($insertUserQuery);
    $stmt->execute([
        ':username' => $defaultUser,
        ':password' => $hashedPassword,
        ':role' => 'superadmin'
    ]);

    if ($stmt->rowCount() > 0) {
        echo "Superadmin user seeded successfully." . PHP_EOL;
    } else {
        echo "Superadmin user already exists." . PHP_EOL;
    }
} catch (PDOException $e) {
    die("Failed to insert superadmin: " . $e->getMessage() . PHP_EOL);
}

// Close the database connection
$pdo = null;
?>
