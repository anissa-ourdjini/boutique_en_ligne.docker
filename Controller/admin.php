<?php
session_start();
echo __DIR__;
require_once '../Model/database.php';

// Vérifier si l'utilisateur est administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Gestion des mangas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $title = $_POST['title'] ?? '';
        $image_url = $_POST['image_url'] ?? '';
        $synopsis = $_POST['synopsis'] ?? '';
        $price = $_POST['price'] ?? 0;

        $stmt = $db->prepare("INSERT INTO mangas (title, image_url, synopsis, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $image_url, $synopsis, $price]);
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;

        $stmt = $db->prepare("DELETE FROM mangas WHERE id = ?");
        $stmt->execute([$id]);
    }
}

// Récupérer tous les mangas
$stmt = $db->prepare("SELECT * FROM mangas");
$stmt->execute();
$mangas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Manga Meow</title>
    <link rel="stylesheet" href="../View/css/style.css">
    <link rel="stylesheet" href="../View/css/responsive.css">
</head>
<body>
    <header>
        <nav class="main-nav">
            <div class="nav-brand">
                <a href="../index.php">
                    <h1>Manga Meow</h1>
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="../Controller/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="admin-page">
        <div class="container">
            <h2>Admin Panel</h2>

            <section class="add-manga">
                <h3>Add New Manga</h3>
                <form method="POST" action="admin.php">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="image_url">Image URL:</label>
                        <input type="text" id="image_url" name="image_url" required>
                    </div>
                    <div class="form-group">
                        <label for="synopsis">Synopsis:</label>
                        <textarea id="synopsis" name="synopsis" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="price">Price:</label>
                        <input type="number" id="price" name="price" step="0.01" required>
                    </div>
                    <button type="submit" class="auth-button">Add Manga</button>
                </form>
            </section>

            <section class="manage-manga">
                <h3>Manage Manga</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mangas as $manga): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($manga['id']); ?></td>
                                <td><?php echo htmlspecialchars($manga['title']); ?></td>
                                <td>$<?php echo htmlspecialchars($manga['price']); ?></td>
                                <td>
                                    <form method="POST" action="admin.php" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $manga['id']; ?>">
                                        <button type="submit" class="remove-item">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <p>&copy; 2025 Manga Meow. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>