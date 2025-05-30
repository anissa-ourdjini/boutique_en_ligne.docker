<?php
require_once '../autoload.php';
// require_once '../config.php'; // Déjà chargé ci-dessous pour la clé

session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Charger la configuration Stripe pour la clé publique
$config = require '../config.php';
$stripePublishableKey = $config['stripe']['publishable_key'];

// // Récupérer les informations du panier depuis la session (Removed)
// $cart = isset($_SESSION[\'cart\']) ? $_SESSION[\'cart\'] : []; (Removed)
// $totalAmount = array_reduce($cart, function ($sum, $item) { (Removed)
//     return $sum + $item[\'price\'] * $item[\'quantity\']; (Removed)
// }, 0); (Removed)

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Manga Meow</title>
    <link rel="stylesheet" href="../View/css/style.css">
    <link rel="stylesheet" href="../View/css/responsive.css">
    <script src="https://js.stripe.com/v3/"></script>
    <style> /* Added basic style for potential overlap */
        .checkout-page .container {
            padding-top: 20px; /* Add some top padding */
        }
        /* Override fixed position for nav on this page */
        .checkout-body .main-nav {
            position: static; 
        }
    </style>
</head>
<body class="checkout-body">
    <header>
        <nav class="main-nav">
            <div class="nav-brand">
                <a href="../index.php">
                    <h1>Manga Meow</h1>
                </a>
            </div>
            <!-- If the full nav should be here, it needs to be added -->
        </nav>
    </header>

    <main class="checkout-page">
        <div class="container">
            <h2>Checkout</h2>
            <!-- Total Amount display updated -->
            <p>Total Amount: <span id="totalAmountDisplay">$0.00</span></p>

            <form id="paymentForm">
                <label for="cardholderName">Cardholder Name</label>
                <input type="text" id="cardholderName" name="cardholderName" required>

                <label for="cardNumber">Card Number</label>
                <div id="cardNumber" class="stripe-input"></div>

                <label for="expiry">Expiry Date</label>
                <div id="expiry" class="stripe-input"></div>

                <label for="cvc">CVC</label>
                <div id="cvc" class="stripe-input"></div>

                <button type="submit" id="submitPayment">Pay Now</button>
            </form>
        </div>
    </main>

    <script>
        const stripe = Stripe('<?php echo $stripePublishableKey; ?>'); // Utiliser la clé publique de la config
        const elements = stripe.elements();
        let currentTotalAmount = 0; // Variable to store the total in cents

        // Function to calculate total from localStorage
        function calculateTotalFromCart() {
            const cartItems = JSON.parse(localStorage.getItem('cart')) || [];
            let subtotal = 0;
            cartItems.forEach(item => {
                subtotal += item.price * item.quantity;
            });
            const shipping = 5; // Assuming fixed shipping
            const total = subtotal + shipping;
            currentTotalAmount = Math.round(total * 100); // Store total in cents
            document.getElementById('totalAmountDisplay').textContent = `$${total.toFixed(2)}`;
            return currentTotalAmount; // Return total in cents
        }

        document.addEventListener('DOMContentLoaded', () => {
            calculateTotalFromCart(); // Calculate and display total on page load
        });

        const cardNumber = elements.create('cardNumber');
        cardNumber.mount('#cardNumber');

        const cardExpiry = elements.create('cardExpiry');
        cardExpiry.mount('#expiry');

        const cardCvc = elements.create('cardCvc');
        cardCvc.mount('#cvc');

        const paymentForm = document.getElementById('paymentForm');
        paymentForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            // Ensure the total is calculated just before submitting
            const amountInCents = calculateTotalFromCart();
            if (amountInCents <= 0) {
                alert("Your cart is empty or total is invalid.");
                return; // Prevent submission if total is zero or negative
            }

            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardNumber,
                billing_details: {
                    name: document.getElementById('cardholderName').value,
                },
            });

            if (error) {
                alert(error.message);
            } else {
                const response = await fetch('payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        payment_method: paymentMethod.id,
                        // Use the JavaScript variable for amount
                        amount: amountInCents,
                    }),
                });

                const result = await response.json();
                if (result.status === 'success') {
                    alert('Payment successful!');
                    window.location.href = '../index.php';
                } else {
                    alert(result.message);
                }
            }
        });
    </script>
</body>
</html>