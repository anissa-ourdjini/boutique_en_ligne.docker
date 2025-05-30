<?php
session_start();
require_once '../autoload.php';
require_once '../Model/database.php';
require_once '../config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur depuis la base de données
$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT username, email, created_at FROM users WHERE id = :id");
$stmt->bindParam(':id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Gérer le cas où l'utilisateur n'est pas trouvé (peu probable si l'ID de session est correct)
    echo "Erreur : Utilisateur non trouvé.";
    exit();
}

// Inclure la vue du profil
require_once '../View/profile_view.php';
?> 