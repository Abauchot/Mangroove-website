#!/bin/bash

echo "🚀 Démarrage du backend Mangroove..."

if [ ! -d vendor ]; then
  echo "📦 Installation des dépendances Composer..."
  composer install
fi

# Attendre que la base de données soit prête
echo "⏳ Attente de la base de données..."
while ! pg_isready -h db -p 5432 -U symfony > /dev/null 2>&1; do
  sleep 1
done

echo "✅ Base de données prête !"

# Configuration de la base de données de développement
echo "🗄️  Configuration de la base de données de développement..."
php bin/console doctrine:database:create --if-not-exists --no-interaction || true
php bin/console doctrine:migrations:migrate --no-interaction || true

echo "✅ Configuration terminée !"
echo "� Pour exécuter les tests : ./run-tests.sh ou docker compose exec backend vendor/bin/phpunit"

php -S 0.0.0.0:8000 -t public
