<?php

require 'db.php'; // Include the database connection file

// Log raw webhook data for debugging
$logFile = 'webhook.log';
$requestBody = file_get_contents('php://input');
file_put_contents($logFile, date('[Y-m-d H:i:s]') . " Received webhook: " . $requestBody . "\n", FILE_APPEND);

// Parse the JSON payload
$data = json_decode($requestBody, true);

// Check if the payload is valid and contains event data
if (!isset($data['data']['id']) || !isset($data['data']['attributes'])) {
    // Invalid payload
    http_response_code(400); // Bad Request
    file_put_contents($logFile, date('[Y-m-d H:i:s]') . " Invalid webhook payload.\n", FILE_APPEND);
    exit();
}

$eventId = $data['data']['id'];
$eventType = $data['data']['attributes']['type']; // e.g., 'source.chargeable', 'payment.paid'
$eventData = $data['data']['attributes']['data']['attributes']; // Contains the actual object (source or payment)

file_put_contents($logFile, date('[Y-m-d H:i:s]') . " Processinng event: " . $eventType . " with ID " . $eventId . "\n", FILE_APPEND);

// Process only relevant events, e.g., `source.chargeable` for sources or `payment.paid` for payments
// Since we are creating a source and redirecting, PayMongo will eventually send a `payment.paid` or `payment.failed` webhook event

// You might also want to verify the webhook signature for security, but that's omitted for this basic example.
// See PayMongo documentation for webhook signature verification: https://developers.paymongo.com/docs/webhooks

if ($eventType === 'payment.paid') {
    $paymentId = $eventData['id']; // This is the Payment ID
    $status = $eventData['status']; // Should be 'paid'
    $sourceId = $eventData['source']['id']; // Get the related source ID

    file_put_contents($logFile, date('[Y-m-d H:i:s]') . " Processing payment.paid for Payment ID: " . $paymentId . " related Source ID: " . $sourceId . "\n", FILE_APPEND);

    // Update the order status in the database using the Source ID
    if (updateOrderStatus($sourceId, $status)) {
         file_put_contents($logFile, date('[Y-m-d H:i:s]') . " Successfully updated order status for Source ID: " . $sourceId . " to " . $status . "\n", FILE_APPEND);
        http_response_code(200); // OK
    } else {
         file_put_contents($logFile, date('[Y-m-d H:i:s]') . " Failed to update order status for Source ID: " . $sourceId . "\n", FILE_APPEND);
        http_response_code(500); // Internal Server Error
    }

} elseif ($eventType === 'payment.failed') {
    $paymentId = $eventData['id']; // This is the Payment ID
    $status = $eventData['status']; // Should be 'failed'
    $sourceId = $eventData['source']['id']; // Get the related source ID

     file_put_contents($logFile, date('[Y-m-d H:i:s]') . " Processing payment.failed for Payment ID: " . $paymentId . " related Source ID: " . $sourceId . "\n", FILE_APPEND);

     // Update the order status in the database using the Source ID
    if (updateOrderStatus($sourceId, $status)) {
        file_put_contents($logFile, date('[Y-m-d H:i:s]') . " Successfully updated order status for Source ID: " . $sourceId . " to " . $status . "\n", FILE_APPEND);
        http_response_code(200); // OK
    } else {
        file_put_contents($logFile, date('[Y-m-d H:i:s]') . " Failed to update order status for Source ID: " . $sourceId . "\n", FILE_APPEND);
        http_response_code(500); // Internal Server Error
    }

} else {
    // Ignore other event types for this example
    file_put_contents($logFile, date('[Y-m-d H:i:s]') . " Ignoring event type: " . $eventType . "\n", FILE_APPEND);
    http_response_code(200); // Still return 200 for ignored events
}

?> 