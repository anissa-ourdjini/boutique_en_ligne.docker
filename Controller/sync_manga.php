<?php
require_once 'database.php';

class MangaSync {
    private $db;
    private $baseUrl = 'https://api.jikan.moe/v4';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function syncFeaturedManga() {
        try {
            // Appel API
            $url = "{$this->baseUrl}/top/manga?filter=bypopularity&limit=8";
            $ch = curl_init(); // Initialisation de cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);

            if (curl_errno($ch)) { // Vérification des erreurs après l'exécution
                throw new Exception(curl_error($ch));
            }
            curl_close($ch);

            $data = json_decode($response, true);
            if (!isset($data['data'])) {
                throw new Exception('Invalid API response');
            }

            // Préparer la requête SQL
            $stmt = $this->db->prepare("
                INSERT INTO mangas (mal_id, title, image_url, synopsis, price)
                VALUES (:mal_id, :title, :image_url, :synopsis, :price)
                ON DUPLICATE KEY UPDATE
                    title = :title,
                    image_url = :image_url,
                    synopsis = :synopsis,
                    price = :price,
                    updated_at = CURRENT_TIMESTAMP
            ");

            // Traiter chaque manga
            foreach ($data['data'] as $manga) {
                $price = round(mt_rand(1000, 3000) / 100, 2); // Prix aléatoire entre 10 et 30
                $stmt->execute([
                    ':mal_id' => $manga['mal_id'],
                    ':title' => $manga['title'],
                    ':image_url' => $manga['images']['jpg']['image_url'],
                    ':synopsis' => $manga['synopsis'] ?? '',
                    ':price' => $price
                ]);
            }

            return ['status' => 'success', 'message' => 'Manga data synced successfully'];
        } catch (Exception $e) {
            error_log("Sync Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}

// Exécuter la synchronisation
$sync = new MangaSync();
$result = $sync->syncFeaturedManga();
echo json_encode($result);
?>