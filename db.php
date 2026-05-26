<?php
// db.php - Database connection and auto-initialization

$host = 'localhost';
$user = 'suzxlabs';
$pass = 'Susara@200611003614';
$dbname = 'vx_db';

try {
    // 1. Try connecting directly to the database first (essential for shared hosting databases created via cPanel)
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // 2. Fallback: If connecting directly fails, try creating the database (useful for local development / XAMPP)
    try {
        $pdo = new PDO("mysql:host=$host", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Connect to the newly created database
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $ex) {
        die("Database Connection failed: " . $ex->getMessage());
    }
}

try {
    // 3. Create tables if they don't exist
    
    // Create Users Table
    $conn->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) UNIQUE NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `name` VARCHAR(100) NOT NULL,
        `cash_balance` DECIMAL(12,2) DEFAULT 0.00,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");
    
    // Create Products Table
    $conn->exec("CREATE TABLE IF NOT EXISTS `products` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `puff` INT DEFAULT NULL,
        `buying_price` DECIMAL(12,2) NOT NULL,
        `selling_price` DECIMAL(12,2) NOT NULL,
        `stock_quantity` INT NOT NULL,
        `image_path` VARCHAR(255) DEFAULT NULL,
        `status` TINYINT(1) DEFAULT 1, -- 1 = active, 0 = inactive
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");
    
    // Self-healing migration to add 'puff' column if table already exists
    try {
        $conn->exec("ALTER TABLE `products` ADD COLUMN `puff` INT DEFAULT NULL AFTER `name`;");
    } catch (PDOException $e) {
        // Column already exists, safe to ignore
    }
    
    // Create Sales Table
    $conn->exec("CREATE TABLE IF NOT EXISTS `sales` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `total_amount` DECIMAL(12,2) NOT NULL,
        `total_profit` DECIMAL(12,2) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB;");
    
    // Create Sale Items Table
    $conn->exec("CREATE TABLE IF NOT EXISTS `sale_items` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `sale_id` INT NOT NULL,
        `product_id` INT NOT NULL,
        `quantity` INT NOT NULL,
        `buying_price` DECIMAL(12,2) NOT NULL,
        `selling_price` DECIMAL(12,2) NOT NULL,
        FOREIGN KEY (`sale_id`) REFERENCES `sales`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    // 4. Seed the two default users if empty
    $stmt = $conn->query("SELECT COUNT(*) FROM `users`");
    if ($stmt->fetchColumn() == 0) {
        $usersToSeed = [
            [
                'name' => 'Susara Senarathne',
                'username' => 'susar.aa',
                'password' => password_hash('Susara@2006', PASSWORD_DEFAULT),
                'cash_balance' => 0.00
            ],
            [
                'name' => 'Umesha Udayanga',
                'username' => 'umesha',
                'password' => password_hash('Umesha@2001', PASSWORD_DEFAULT),
                'cash_balance' => 0.00
            ]
        ];
        
        $insertStmt = $conn->prepare("INSERT INTO `users` (`name`, `username`, `password`, `cash_balance`) VALUES (:name, :username, :password, :cash_balance)");
        foreach ($usersToSeed as $u) {
            $insertStmt->execute($u);
        }
    }
    
} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage());
}
?>
