#!/bin/bash

echo "🧪 Préparation et exécution des tests Mangroove..."

# Vérifier si Docker Compose est en cours d'exécution
if ! docker compose ps | grep -q "mangroove_backend.*Up"; then
    echo "❌ Le backend n'est pas en cours d'exécution. Démarrez d'abord avec 'docker compose up'"
    exit 1
fi

# Préparer la base de données de test
echo "🗄️  Préparation de la base de données de test..."
docker compose exec backend php bin/console doctrine:database:create --env=test --if-not-exists --no-interaction || true

# Vérifier si le schéma existe et le créer/synchroniser si nécessaire
echo "📋 Synchronisation du schéma de test..."
docker compose exec backend php bin/console doctrine:schema:drop --env=test --force --no-interaction > /dev/null 2>&1 || true
docker compose exec backend php bin/console doctrine:schema:create --env=test --no-interaction || true

# Exécuter les tests
echo "▶️  Lancement des tests..."
docker compose exec -e APP_ENV=test backend vendor/bin/phpunit "$@"

echo "✅ Tests terminés !"
