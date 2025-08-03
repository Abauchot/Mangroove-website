#!/bin/bash

if [ ! -f public/index.php ]; then
  echo "Symfony non initialisé, création du projet..."
  composer create-project symfony/skeleton . --no-interaction
  composer require api
  composer require nelmio/api-doc-bundle
  composer require symfony/orm-pack
  composer require symfony/maker-bundle --dev
  composer require doctrine/doctrine-bundle
fi

# Assure que vendor/ est bien là
if [ ! -d vendor ]; then
  echo "Installation des dépendances..."
  composer install
fi

php -S 0.0.0.0:8000 -t public
