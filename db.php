<?php

// Database configuration
$host = 'localhost';
$db = 'paymongon'; // Change this
$user = 'root'; // Change this
$pass = ''; // Change this
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = null;
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Log error or display a user-friendly message
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}

/**
 * Insert a new order into the database.
 * @param string $paymentId PayMongo payment or source ID.
 * @param int $amount Amount in centavos.
 * @param string $status Initial status (e.g., 'pending').
 * @return int|false The inserted order ID on success, false on failure.
 */
function insertOrder($paymentId, $amount, $status)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO orders (payment_id, amount, status) VALUES (?, ?, ?)");
    if ($stmt->execute([$paymentId, $amount, $status])) {
        return $pdo->lastInsertId();
    } else {
        error_log("Error inserting order: " . print_r($stmt->errorInfo(), true));
        return false;
    }
}

/**
 * Update the status of an order based on PayMongo payment/source ID.
 * @param string $paymentId PayMongo payment or source ID.
 * @param string $status The new status (e.g., 'paid', 'failed').
 * @return bool True on success, false on failure.
 */
function updateOrderStatus($paymentId, $status)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE payment_id = ?");
    if ($stmt->execute([$status, $paymentId])) {
        return $stmt->rowCount() > 0;
    } else {
        error_log("Error updating order status for payment_id " . $paymentId . ": " . print_r($stmt->errorInfo(), true));
        return false;
    }
}

?> 