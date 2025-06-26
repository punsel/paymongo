<?php

require 'db.php'; // Include the database connection file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, 'api.env');
$dotenv->load();
// --- Paste your PayMongo test secret key here ---
$paymongoSecretKey = $_ENV['paymongoSecretKey']; // Replace with your actual test secret key
// -------------------------------------------------

// Get amount from form and convert to centavos
$amount = isset($_POST['amount']) ? (float)$_POST['amount'] * 100 : 100000; // Default to 1000 PHP if not set
$amount = (int)$amount; // Convert to integer for centavos

$currency = 'PHP';
$paymentMethod = 'gcash';

// Get customer details from the form
$customerEmail = $_POST['customerEmail'] ?? 'test@example.com';
$customerName = $_POST['customerName'] ?? 'Test Customer';

// URLs for redirection
$baseUrl = "http://localhost/paymongo.test"; // Base URL for your project
$successUrl = $baseUrl . "/success.php";
$failureUrl = $baseUrl . "/failed.php";
$webhookUrl = "http://localhost/paymongo.test/webhook.php"; // Replace with your actual ngrok URL

// Prepare the data for PayMongo API
$data = json_encode([
    'data' => [
        'attributes' => [
            'type' => 'gcash',
            'amount' => $amount,
            'currency' => $currency,
            'redirect' => [
                'success' => $successUrl,
                'failed' => $failureUrl,
            ],
            'billing' => [
                'email' => $customerEmail,
                'name' => $customerName,
            ],
            'description' => 'Order #' . uniqid(), // Unique description for the order
            'metadata' => [
                'order_id' => uniqid('order_'), // Optional: Attach internal order ID if needed
            ]
        ]
    ]
]);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.paymongo.com/v1/sources');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . base64_encode($paymongoSecretKey . ':'),
    'Content-Type: application/json',
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    $errorMsg = 'Curl error: ' . curl_error($ch);
    error_log($errorMsg);
    die("Payment initiation failed. Please try again later. Error: " . $errorMsg);
}

curl_close($ch);

$responseData = json_decode($response, true);

// Check for API errors
if (isset($responseData['errors'])) {
    $errorMsg = 'PayMongo API error: ' . print_r($responseData['errors'], true);
    error_log($errorMsg);
    die("PayMongo API error. Please try again later. Details: " . $errorMsg);
}

// Check if the source creation was successful and get the redirect URL
if (isset($responseData['data']['attributes']['redirect']['checkout_url']) && isset($responseData['data']['id'])) {
    $checkoutUrl = $responseData['data']['attributes']['redirect']['checkout_url'];
    $sourceId = $responseData['data']['id'];

    // Insert the initial order into the database
    $orderId = insertOrder($sourceId, $amount, 'pending');

    if ($orderId !== false) {
        // Redirect the user to the PayMongo checkout page
        header('Location: ' . $checkoutUrl);
        exit;
    } else {
        // Database insertion failed
        die("Failed to save order details.");
    }
} else {
    // Handle unexpected API response structure
    $errorMsg = 'Unexpected PayMongo API response: ' . print_r($responseData, true);
    error_log($errorMsg);
    die("Failed to get checkout URL from PayMongo. Unexpected response.");
}
