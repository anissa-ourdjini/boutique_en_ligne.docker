<?php
session_start();
require_once '../Model/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $database = new Database();
        $db = $database->getConnection();

        $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirection en fonction du rôle
            if ($user['role'] === 'admin') {
                header("Location: admin.php"); // Redirection vers le tableau admin
            } else {
                header("Location: ../index.php");
            }
            exit();
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $error = "Please fill in all fields";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Manga Meow</title>
    <link rel="stylesheet" href="../View/css/style.css">
    <link rel="stylesheet" href="../View/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h2><i class="fas fa-paw"></i> Login to Manga Meow</h2>
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST" action="login.php" class="auth-form">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="auth-button">Login</button>
            </form>
            <div class="auth-links">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
                <p><a href="forgot-password.php">Forgot your password?</a></p>
            </div>
            <div class="back-to-home">
                <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </div>
    <script src="../View/js/main.js"></script>
</body>
</html>