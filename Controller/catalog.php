<?php
session_start();
require_once '../Model/database.php';

$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manga Catalog - Manga Meow</title>
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
                <li><a href="../Controller/catalog.php" class="active">Catalog</a></li>
                <li><a href="../Controller/cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
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

    <main class="catalog-page">
        <div class="container">
            <div class="search-section">
                <h2>Browse Manga</h2>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search manga..." autocomplete="off">
                    <button id="searchButton">
                        <i class="fas fa-search"></i>
                    </button>
                    <div id="suggestionsBox" class="suggestions-box"></div>
                </div>
                <div class="filter-options">
                    <select id="sortBy">
                        <option value="popularity">Sort by Popularity</option>
                        <option value="title">Sort by Title</option>
                        <option value="score">Sort by Rating</option>
                    </select>
                </div>
            </div>

            <div id="loadingSpinner" class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading manga...</p>
            </div>

            <div class="manga-grid" id="mangaGrid">
                <!-- Manga items will be loaded here via JavaScript -->
            </div>

            <div class="pagination" id="pagination">
                <!-- Pagination will be added here via JavaScript -->
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
    <script src="../View/js/api.js"></script>
    <script>
        let currentPage = 1;
        let currentSearch = '';
        let currentSort = 'popularity';

        // Initialize the catalog
        document.addEventListener('DOMContentLoaded', () => {
            loadMangaCatalog();

            // Search functionality
            const searchInput = document.getElementById('searchInput');
            const searchButton = document.getElementById('searchButton');
            const sortBy = document.getElementById('sortBy');

            searchButton.addEventListener('click', () => {
                currentSearch = searchInput.value.trim();
                currentPage = 1;
                loadMangaCatalog();
            });

            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    currentSearch = searchInput.value.trim();
                    currentPage = 1;
                    loadMangaCatalog();
                    clearSuggestions(); // Clear suggestions on explicit search
                }
            });

            // Autocomplete functionality
            searchInput.addEventListener('input', async () => {
                const query = searchInput.value.trim();
                const suggestionsBox = document.getElementById('suggestionsBox');

                if (query.length < 2) { // Only search if query is at least 2 characters
                    suggestionsBox.innerHTML = '';
                    suggestionsBox.style.display = 'none';
                    return;
                }

                try {
                    const results = await mangaAPI.searchManga(query);
                    displaySuggestions(results, suggestionsBox);
                } catch (error) {
                    console.error('Error fetching suggestions:', error);
                    suggestionsBox.innerHTML = '<div class="suggestion-item-error">Erreur lors de la récupération des suggestions.</div>';
                    suggestionsBox.style.display = 'block';
                }
            });

            // Hide suggestions when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.search-box')) {
                    clearSuggestions();
                }
            });

            sortBy.addEventListener('change', () => {
                currentSort = sortBy.value;
                loadMangaCatalog();
            });
        });

        async function loadMangaCatalog() {
            const loadingSpinner = document.getElementById('loadingSpinner');
            const mangaGrid = document.getElementById('mangaGrid');
            
            loadingSpinner.style.display = 'flex';
            mangaGrid.innerHTML = '';

            try {
                let manga;
                if (currentSearch) {
                    manga = await mangaAPI.searchManga(currentSearch);
                } else {
                    manga = await mangaAPI.getFeaturedManga();
                }

                // Sort manga based on selected option
                manga.sort((a, b) => {
                    switch (currentSort) {
                        case 'title':
                            return a.title.localeCompare(b.title);
                        case 'score':
                            return (b.score || 0) - (a.score || 0);
                        case 'popularity':
                        default:
                            return (b.members || 0) - (a.members || 0);
                    }
                });

                const mangaHTML = manga.map(createMangaCard).join('');
                mangaGrid.innerHTML = mangaHTML;
            } catch (error) {
                mangaGrid.innerHTML = '<p class="error-message">Error loading manga. Please try again later.</p>';
            } finally {
                loadingSpinner.style.display = 'none';
            }
        }

        function displaySuggestions(results, suggestionsBox) {
            if (!results || results.length === 0) {
                suggestionsBox.innerHTML = '<div class="suggestion-item-none">Aucune suggestion trouvée.</div>';
                suggestionsBox.style.display = 'block';
                return;
            }

            suggestionsBox.innerHTML = ''; // Clear previous suggestions
            results.slice(0, 5).forEach(manga => { // Display top 5 suggestions
                const suggestionItem = document.createElement('div');
                suggestionItem.classList.add('suggestion-item');
                suggestionItem.textContent = manga.title;
                suggestionItem.addEventListener('click', () => {
                    document.getElementById('searchInput').value = manga.title;
                    suggestionsBox.innerHTML = '';
                    suggestionsBox.style.display = 'none';
                    // Optionally, trigger search immediately:
                    currentSearch = manga.title.trim();
                    currentPage = 1;
                    loadMangaCatalog();
                });
                suggestionsBox.appendChild(suggestionItem);
            });
            suggestionsBox.style.display = 'block';
        }

        function clearSuggestions() {
            const suggestionsBox = document.getElementById('suggestionsBox');
            if (suggestionsBox) {
                suggestionsBox.innerHTML = '';
                suggestionsBox.style.display = 'none';
            }
        }
    </script>

    <style>
        .catalog-page {
            padding: 2rem 1rem;
            margin-top: 60px;
        }

        .search-section {
            margin-bottom: 2rem;
            text-align: center;
        }

        .search-box {
            display: flex;
            max-width: 500px;
            margin: 1rem auto;
            position: relative; /* Moved from .search-box input to here */
        }

        .search-box input {
            flex: 1;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px 0 0 5px;
            font-size: 1rem;
            /* position: relative; This was moved */
        }

        .search-box button {
            padding: 0.8rem 1.5rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
        }

        .filter-options {
            margin: 1rem 0;
        }

        .filter-options select {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .loading-spinner {
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .loading-spinner i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .manga-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 2rem;
            padding: 1rem;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination button {
            padding: 0.5rem 1rem;
            border: 1px solid var(--primary-color);
            background: white;
            color: var(--primary-color);
            border-radius: 5px;
            cursor: pointer;
        }

        .pagination button.active {
            background: var(--primary-color);
            color: white;
        }

        .pagination button:hover {
            background: var(--primary-color);
            color: white;
        }

        .manga-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .manga-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .add-to-cart-btn {
            width: 100%;
            padding: 0.8rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 1rem;
            transition: background-color 0.3s ease;
        }

        .add-to-cart-btn:hover {
            background: #ff5252;
        }

        .suggestions-box {
            position: absolute;
            top: 100%; /* Position below the input */
            left: 0;
            right: 0;
            background-color: white;
            border: 1px solid #ddd;
            border-top: none;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none; /* Hidden by default */
        }

        .suggestion-item {
            padding: 0.5rem;
            cursor: pointer;
        }

        .suggestion-item:hover {
            background-color: #f0f0f0;
        }

        .suggestion-item-none,
        .suggestion-item-error {
            padding: 0.5rem;
            color: #777;
            text-align: center;
        }
    </style>
</body>
</html>