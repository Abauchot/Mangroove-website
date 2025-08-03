#!/bin/bash

# Si Symfony n’est pas encore installé, on l’installe
if [ ! -f public/index.php ]; then
  echo "Symfony non initialisé, création du projet..."
  composer create-project symfony/skeleton . --no-interaction
  composer require api
  composer require nelmio/api-doc-bundle
  composer require symfony/orm-pack
  composer require symfony/maker-bundle --dev
  composer require doctrine/doctrine-bundle
fi

# Si vendor/ est absent (clone partiel), on installe les dépendances
if [ ! -d vendor ]; then
  echo "Installation des dépendances Composer..."
  composer install
fi

php -S 0.0.0.0:8000 -t public
