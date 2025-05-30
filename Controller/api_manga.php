<?php
require_once 'database.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->prepare("SELECT mal_id, title, image_url, synopsis, price FROM mangas ORDER BY updated_at DESC LIMIT 8");
    $stmt->execute();
    $mangas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['data' => $mangas]);
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    echo json_encode(['data' => [], 'error' => 'Failed to fetch manga data']);
}
?>