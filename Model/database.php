<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn = null;

    public function __construct() {
        $this->host = getenv('MYSQL_HOST') ?: 'localhost';
        $this->db_name = getenv('MYSQL_DATABASE') ?: 'manga_meow';
        $this->username = getenv('MYSQL_USER') ?: 'root';
        $this->password = getenv('MYSQL_PASSWORD') ?: '';
    }

    public function getConnection() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
            die("Erreur de connexion à la base de données.");
        }
        return $this->conn;
    }
}
?>
