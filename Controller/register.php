<?php
session_start();
require_once '../Model/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim(filter_input(INPUT_POST, 'username', FILTER_DEFAULT)), ENT_QUOTES, 'UTF-8');
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($username) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            if (strlen($password) >= 8) {
                $database = new Database();
                $db = $database->getConnection();

                // Check if username or email already exists
                $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $email]);
                
                if ($stmt->rowCount() > 0) {
                    $error = "Username or email already exists";
                } else {
                    // Create new user
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
                    
                    try {
                        $stmt->execute([$username, $email, $hashed_password]);
                        $success = "Registration successful! Please login.";
                        header("refresh:2;url=login.php");
                    } catch (PDOException $e) {
                        $error = "Registration failed. Please try again.";
                    }
                }
            } else {
                $error = "Password must be at least 8 characters long";
            }
        } else {
            $error = "Passwords do not match";
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
    <title>Register - Manga Meow</title>
    <link rel="stylesheet" href="../View/css/style.css">
    <link rel="stylesheet" href="../View/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h2><i class="fas fa-paw"></i> Register for Manga Meow</h2>
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <form method="POST" action="register.php" class="auth-form">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required minlength="3" maxlength="50">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required minlength="8">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                </div>
                <button type="submit" class="auth-button">Register</button>
            </form>
            <div class="auth-links">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
            <div class="back-to-home">
                <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
            </div>
        </div>
    </div>
    <script src="../View/js/main.js"></script>
</body>
</html>