# Subscription Manager

Application de gestion d'abonnements développée avec Symfony 7.

## Fonctionnalités

- Gestion des abonnements
- Gestion des catégories
- Gestion des fournisseurs
- Tableau de bord avec statistiques
- Suivi des renouvellements

## Technologies utilisées

- Symfony 7
- Doctrine ORM
- Twig
- Bootstrap 5.3
- Font Awesome 6.4

## Installation

# Clonez le repository

git clone [url-du-repo]

# Installez les dépendances

composer install

# Configurez votre base de données dans .env.local

DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"

# Créez la base de données

php bin/console doctrine:database:create

# Effectuez les migrations

php bin/console doctrine:migrations:migrate
