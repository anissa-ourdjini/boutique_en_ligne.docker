<?php
require_once '../vendor/autoload.php'; // Assuming Stripe PHP library is via Composer
require_once '../config.php';

session_start();

header('Content-Type: application/json');

// Charger la configuration Stripe
$config = require '../config.php';
$stripeSecretKey = $config['stripe']['secret_key'];

if (empty($stripeSecretKey)) {
    echo json_encode(['status' => 'error', 'message' => 'Stripe secret key is not configured.']);
    exit;
}

// \Stripe\Stripe::setApiKey("sk_test_51RNwAyPMlf4mSTwfokuMaCATHvDuPGesJ0WKmmGHDV9mefEYQlOrA6mGJlD0EleL5wwD4ztOBMAcON8GeA8gUpEn00tYR3hA8g");
\Stripe\Stripe::setApiKey($stripeSecretKey);

// Get the raw POST data
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str);

if (!$json_obj || !isset($json_obj->payment_method) || !isset($json_obj->amount)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid payment data received.']);
    exit;
}

$payment_method_id = $json_obj->payment_method;
$amount_in_cents = $json_obj->amount;

// You might want to add more validation for the amount here
if ($amount_in_cents <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid amount.']);
    exit;
}

try {
    // Create a PaymentIntent with amount and currency
    $intent = \Stripe\PaymentIntent::create([
        'amount' => $amount_in_cents,
        'currency' => 'eur', // Change to your desired currency
        'payment_method' => $payment_method_id,
        'confirmation_method' => 'manual', // We will confirm it manually
        'confirm' => true, // Confirm the PaymentIntent immediately
        'return_url' => 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/../index.php', // Optional: For 3D Secure
        // 'automatic_payment_methods' => ['enabled' => true], // Alternative if you let Stripe handle payment method types
    ]);

    if ($intent->status == 'succeeded') {
        // Payment successful
        // Here you can save order details to your database, clear the cart, etc.
        // For example, unset($_SESSION['cart']);

        echo json_encode(['status' => 'success']);

    } else if ($intent->status == 'requires_action' || $intent->status == 'requires_source_action') {
        // Requires additional authentication (e.g., 3D Secure)
        echo json_encode([
            'status' => 'requires_action',
            'client_secret' => $intent->client_secret
        ]);
    } else {
        // Other statuses (e.g., requires_payment_method, canceled)
        error_log('Stripe PaymentIntent status: ' . $intent->status);
        echo json_encode(['status' => 'error', 'message' => 'Payment failed. Status: ' . $intent->status]);
    }

} catch (\Stripe\Exception\ApiErrorException $e) {
    error_log('Stripe API Error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} catch (Exception $e) {
    error_log('General Error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred.']);
}

?> 