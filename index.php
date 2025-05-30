<?php
// Démarrer la session AVANT tout affichage ou espace
session_start();
require_once 'autoload.php';
$config = require 'config.php'; // Charger la configuration
$stripePublishableKey = $config['stripe']['publishable_key']; // Récupérer la clé publique
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manga Meow - Your Favorite Manga Store</title>
    <link rel="stylesheet" href="View/css/style.css">
    <link rel="stylesheet" href="View/css/responsive.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <header>
        <nav class="main-nav">
            <div class="nav-brand">
                <a href="index.php">
                    <h1>Manga Meow</h1>
                </a>
            </div>
            <div class="nav-toggle" id="navToggle">
                <i class="fas fa-bars"></i>
            </div>
            <ul class="nav-links" id="navLinks">
                <li><a href="index.php">Home</a></li>
                <li><a href="Controller/catalog.php">Catalog</a></li>
                <li><a href="Controller/cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                <?php
                if(isset($_SESSION['user_id'])) {
                    echo '<li><a href="Controller/profile.php"><i class="fas fa-user"></i> Profile</a></li>';
                    echo '<li><a href="Controller/logout.php">Logout</a></li>';
                } else {
                    echo '<li><a href="Controller/login.php">Login</a></li>';
                    echo '<li><a href="Controller/register.php">Register</a></li>';
                }
                ?>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h2>Welcome to Manga Meow</h2>
                <p>Discover your next favorite manga series!</p>
                <a href="Controller/catalog.php" class="cta-button">Browse Catalog</a>
            </div>
        </section>

        <section class="featured-manga">
            <h2>Featured Manga</h2>
            <div class="manga-grid" id="featuredManga">
                <!-- Manga items will be loaded here via JavaScript -->
            </div>
        </section>


    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Manga Meow</h3>
                <p>Your trusted source for the best manga collection</p>
            </div>
            <div class="footer-section">
             
          
        <div class="footer-bottom">
            <p>&copy; 2024 Manga Meow. All rights reserved.</p>
        </div>
    </footer>

    <script src="View/js/main.js"></script>
    <script src="View/js/api.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('<?php echo $stripePublishableKey; ?>');
        const elements = stripe.elements();

        const cardNumber = elements.create('cardNumber');
        cardNumber.mount('#cardNumber');

        const cardExpiry = elements.create('cardExpiry');
        cardExpiry.mount('#expiry');

        const cardCvc = elements.create('cardCvc');
        cardCvc.mount('#cvc');

        const paymentForm = document.getElementById('paymentForm');
        paymentForm.addEventListener('submit', async (event) => {
            event.preventDefault();

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
                const response = await fetch('Controller/payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        payment_method: paymentMethod.id,
                        amount: 5000, // Exemple : 50.00 USD
                    }),
                });

                const result = await response.json();
                if (result.status === 'success') {
                    alert('Payment successful!');
                } else {
                    alert(result.message);
                }
            }
        });
    </script>
</body>
</html>