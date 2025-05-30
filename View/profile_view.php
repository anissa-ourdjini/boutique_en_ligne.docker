<?php
// Le contrôleur (Controller/profile.php) doit avoir défini la variable $user
if (!isset($user)) {
    echo "Les données de l'utilisateur ne sont pas disponibles.";
    // Peut-être rediriger ou afficher un message d'erreur plus convivial
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Manga Meow</title>
    <link rel="stylesheet" href="../View/css/style.css"> <!-- Assurez-vous que le chemin est correct -->
    <link rel="stylesheet" href="../View/css/responsive.css"> <!-- Assurez-vous que le chemin est correct -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <header>
        <nav class="main-nav">
            <div class="nav-brand">
                <a href="../index.php"> <!-- Lien corrigé pour remonter d'un niveau -->
                    <h1>Manga Meow</h1>
                </a>
            </div>
            <div class="nav-toggle" id="navToggle">
                <i class="fas fa-bars"></i>
            </div>
            <ul class="nav-links" id="navLinks">
                <li><a href="../index.php">Home</a></li> <!-- Lien corrigé -->
                <li><a href="../Controller/catalog.php">Catalog</a></li> <!-- Lien corrigé -->
                <li><a href="../Controller/cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li> <!-- Lien corrigé -->
                <?php
                // session_start(); // Déjà démarrée dans le contrôleur normalement
                if(isset($_SESSION['user_id'])) {
                    echo '<li><a href="../Controller/profile.php" class="active"><i class="fas fa-user"></i> Profile</a></li>'; // Lien corrigé
                    echo '<li><a href="../Controller/logout.php">Logout</a></li>'; // Lien corrigé
                } else {
                    echo '<li><a href="../Controller/login.php">Login</a></li>'; // Lien corrigé
                    echo '<li><a href="../Controller/register.php">Register</a></li>'; // Lien corrigé
                }
                ?>
            </ul>
        </nav>
    </header>

    <main>
        <div class="profile-container">
            <h2>My Profile</h2>
            <div class="profile-info">
                <p><strong>Nom d'utilisateur :</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Membre depuis :</strong> <?php echo htmlspecialchars(date("d/m/Y", strtotime($user['created_at']))); ?></p>
                <!-- Ajoutez d'autres informations de profil ici si nécessaire -->
            </div>
            <!-- Section pour modifier le profil (exemple) -->
            <!-- 
            <div class="edit-profile-section">
                <h3>Modifier mes informations</h3>
                <form action="../Controller/update_profile.php" method="POST">
                    <div>
                        <label for="new_email">Nouvel Email :</label>
                        <input type="email" id="new_email" name="new_email">
                    </div>
                    <div>
                        <label for="new_password">Nouveau mot de passe :</label>
                        <input type="password" id="new_password" name="new_password">
                    </div>
                    <button type="submit">Mettre à jour</button>
                </form>
            </div>
            -->
        </div>
    </main>

    <footer>
      <!-- Contenu du footer copié de index.php, chemins CSS/JS ajustés si nécessaire -->
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
            <p>&copy; <?php echo date("Y"); ?> Manga Meow. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/main.js"></script> <!-- Assurez-vous que le chemin est correct -->
</body>
</html> 