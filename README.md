# Hand2Hand

Plateforme web de dons (projet universitaire) qui met en relation des **donateurs**, des **associations** et un **administrateur**.

## Auteurs
Projet réalisé en binôme par :
- **Yasmine Ben Mefteh** 
- **Tasnim Baccouri** 

## Fonctionnalités
- **Utilisateur (donateur)** : inscription, connexion, don d'objets avec photo, modification/suppression tant que le don est `pending`, publication d'offres d'emploi, suivi des statuts.
- **Administrateur** : approuve ou refuse les dons et les offres.
- **Association** : consulte les dons et offres approuvés (recherche + filtre par catégorie) et les réserve.

Cycle d'un don : `pending` → `approved` / `refused` → `reserved`.

## Technologies
PHP 8 (PDO), MySQL / MariaDB, HTML, CSS, JavaScript, Tailwind CSS (CDN).

## Installation avec XAMPP
1. Copier le dossier du projet dans `C:\xampp\htdocs\hand2hand`
2. Démarrer **Apache** et **MySQL** dans le panneau XAMPP
3. Copier `db_config.example.php` en **`db_config.php`** (les valeurs par défaut de XAMPP conviennent)
4. Ouvrir http://localhost/phpmyadmin → **Importer** → choisir `database.sql` → **Exécuter**
5. Ouvrir http://localhost/hand2hand/create_admin.php, créer le compte admin, puis **supprimer `create_admin.php`**
6. Ouvrir http://localhost/hand2hand/index.html

## Scénario de test
1. **Sign Up** : créer un compte *User* (téléphone de 8 chiffres) et un compte *Association*
2. Connexion *User* → **New Donation** (avec photo) et **Job Offer** → statut `pending`
3. Connexion *Admin* → **Approuver** le don et l'offre
4. Connexion *Association* → **réserver** le don et l'offre
5. Connexion *User* → les statuts passent à `reserved`

## Structure
| Fichier | Rôle |
|---|---|
| `index.html`, `login.html`, `SignUp.html`, `style.css` | pages publiques |
| `process_login.php`, `process_signup.php`, `logout.php` | authentification |
| `dashboard.php`, `donate.php`, `edit_donation.php`, `offretravail.php` | espace utilisateur |
| `admin_panel.php`, `update_status.php` | espace admin |
| `association_dashboard.php`, `reserve_item.php` | espace association |
| `auth.php`, `header.php`, `db_config.php` | code partagé |
| `database.sql`, `create_admin.php` | installation |
| `uploads/` | photos envoyées (non versionnées) |

## Sécurité en place
Mots de passe hachés (bcrypt), requêtes préparées (PDO), contrôle des rôles sur chaque page, validation côté serveur, envoi d'images restreint aux vrais fichiers image avec nom aléatoire, exécution de PHP interdite dans `uploads/`.

## Limites connues
- Le formulaire de contact de la page d'accueil n'est pas relié à un envoi d'email
- Pas de protection CSRF ni de limitation des tentatives de connexion
- Les liens Approuver/Refuser utilisent des requêtes GET
- Projet pédagogique : à renforcer avant toute mise en production

## Crédits
Les photos de la page d'accueil (`hands.avif`, `donation.jpg`) et les images Unsplash chargées depuis les pages appartiennent à leurs auteurs respectifs.
