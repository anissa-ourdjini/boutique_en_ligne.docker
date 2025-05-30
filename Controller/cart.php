<?php
session_start();
require_once '../Model/database.php';

// Check if user is logged in for checkout
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Manga Meow</title>
    <link rel="stylesheet" href="../View/css/style.css">
    <link rel="stylesheet" href="../View/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <header>
        <nav class="main-nav">
            <div class="nav-brand">
                <a href="../index.php">
                    <h1>Manga Meow</h1>
                </a>
            </div>
            <div class="nav-toggle" id="navToggle">
                <i class="fas fa-bars"></i>
            </div>
            <ul class="nav-links" id="navLinks">
                <li><a href="../index.php">Home</a></li>
                <li><a href="../Controller/catalog.php">Catalog</a></li>
                <li><a href="../Controller/cart.php" class="active"><i class="fas fa-shopping-cart"></i> Cart</a></li>
                <?php if ($is_logged_in): ?>
                    <li><a href="../Controller/profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="../Controller/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="../Controller/login.php">Login</a></li>
                    <li><a href="../Controller/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="cart-page">
        <div class="container">
            <h2>Your Shopping Cart</h2>
            <div id="cartItems" class="cart-items">
                <!-- Cart items will be loaded here via JavaScript -->
            </div>
            <div class="cart-summary">
                <div class="summary-box">
                    <h3>Order Summary</h3>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="subtotal">$0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span id="shipping">$5.00</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span id="total">$0.00</span>
                    </div>
                    <?php if ($is_logged_in): ?>
                        <button id="checkoutBtn" class="checkout-button">Proceed to Checkout</button>
                    <?php else: ?>
                        <p class="login-message">Please <a href="login.php">login</a> to checkout</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Manga Meow</h3>
                <p>Your trusted source for the best manga collection</p>
            </div>
            <div class="footer-section">
               
            </div>
            <div class="footer-section">
               
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Manga Meow. All rights reserved.</p>
        </div>
    </footer>

    <script src="../View/js/main.js"></script>
    <script>
        // Cart functionality
        document.addEventListener('DOMContentLoaded', () => {
            updateCart();
        });

        function updateCart() {
            const cartItems = JSON.parse(localStorage.getItem('cart')) || [];
            const cartContainer = document.getElementById('cartItems');
            const subtotalElement = document.getElementById('subtotal');
            const totalElement = document.getElementById('total');

            if (cartItems.length === 0) {
                cartContainer.innerHTML = '<p class="empty-cart">Your cart is empty</p>';
                subtotalElement.textContent = '$0.00';
                totalElement.textContent = '$5.00';
                return;
            }

            let subtotal = 0;
            const cartHTML = cartItems.map(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                return `
                    <div class="cart-item">
                        <div class="item-info">
                            <h3>${item.title}</h3>
                            <p>Price: $${item.price}</p>
                        </div>
                        <div class="item-quantity">
                            <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                            <span>${item.quantity}</span>
                            <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                        </div>
                        <div class="item-total">
                            <p>$${itemTotal.toFixed(2)}</p>
                            <button class="remove-item" onclick="removeItem(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');

            cartContainer.innerHTML = cartHTML;
            subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
            totalElement.textContent = `$${(subtotal + 5).toFixed(2)}`;
        }

        function updateQuantity(id, newQuantity) {
            if (newQuantity < 1) return;

            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            const itemIndex = cart.findIndex(item => item.id === id);

            if (itemIndex !== -1) {
                cart[itemIndex].quantity = newQuantity;
                localStorage.setItem('cart', JSON.stringify(cart));
                updateCart();
                updateCartCount();
            }
        }

        function removeItem(id) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            cart = cart.filter(item => item.id !== id);
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCart();
            updateCartCount();
        }

        // Checkout button functionality
        const checkoutBtn = document.getElementById('checkoutBtn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', () => {
                window.location.href = 'checkout.php';
            });
        }
    </script>
</body>
</html>