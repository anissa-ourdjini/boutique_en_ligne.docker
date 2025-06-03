# Manga Meow - Boutique en ligne de mangas

Manga Meow est une boutique en ligne permettant d’acheter des mangas, de gérer un panier, de s’inscrire, se connecter, et de payer en ligne via Stripe. Le projet utilise PHP, MySQL, Docker, et Stripe.

## Fonctionnalités

- Inscription et connexion utilisateur
- Gestion des rôles (utilisateur, admin)
- Catalogue de mangas (récupération via API Jikan)
- Panier d’achat et gestion des commandes
- Paiement sécurisé avec Stripe
- Interface d’administration pour la gestion des mangas
- Responsive design

## Structure du projet

```
.
├── Controller/         # Contrôleurs PHP (auth, panier, catalogue, etc.)
├── Model/              # Modèles (connexion BDD, requêtes)
├── View/               # Vues (HTML, CSS, JS)
├── asset/              # Images et ressources statiques
├── components/         # Librairies JS tierces (RequireJS, etc.)
├── vendor/             # Dépendances PHP (Stripe, etc.)
├── db-init.sql         # Script d'initialisation de la base de données
├── docker-compose.yml  # Configuration Docker multi-conteneurs
├── Dockerfile          # Image PHP/Apache personnalisée
├── config.php          # Configuration (clés Stripe, etc.)
├── .env                # Variables d'environnement (non versionné)
└── index.php           # Page d'accueil
```

## Prérequis

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

## Installation & Lancement

1. **Cloner le dépôt :**
   ```sh
   git clone https://github.com/votre-utilisateur/manga-meow.git
   cd manga-meow
   ```

2. **Configurer les variables d’environnement :**
   - Copier `.env.example` en `.env` (ou éditer `.env` existant)
   - Adapter les clés Stripe si besoin

3. **Lancer l’application avec Docker :**
   ```sh
   docker-compose up --build
   ```
   - Accéder à l’application sur [http://localhost:8080](http://localhost:8080)
   - PhpMyAdmin disponible sur [http://localhost:8081](http://localhost:8081)

## Utilisation

- Inscrivez-vous, connectez-vous, ajoutez des mangas au panier et payez via Stripe.
- Les admins peuvent ajouter/supprimer des mangas via l’interface d’administration.

## Technologies utilisées

- PHP 8.2
- MySQL 8
- Stripe PHP SDK
- Docker & Docker Compose
- HTML5 / CSS3 / JavaScript (ES6)
- RequireJS

## Licence

Ce projet est sous licence MIT.

---

**Auteur :** [OURDJINI Anissa]
