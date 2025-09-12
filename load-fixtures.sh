#!/bin/bash

printf "│ %-22s │ %12s │\n" "Utilisateurs" $(docker compose exec backend php bin/console doctrine:query:sql "SELECT COUNT(*) FROM \"user\"" | grep -E '^[[:space:]]*[0-9]+[[:space:]]*$' | tr -d ' ')
printf "│ %-22s │ %12s │\n" "Jams" $(docker compose exec backend php bin/console doctrine:query:sql "SELECT COUNT(*) FROM \"jam\"" | grep -E '^[[:space:]]*[0-9]+[[:space:]]*$' | tr -d ' ')
printf "│ %-22s │ %12s │\n" "GameEntries" $(docker compose exec backend php bin/console doctrine:query:sql "SELECT COUNT(*) FROM \"game_entry\"" | grep -E '^[[:space:]]*[0-9]+[[:space:]]*$' | tr -d ' ')
printf "│ %-22s │ %12s │\n" "Commentaires" $(docker compose exec backend php bin/console doctrine:query:sql "SELECT COUNT(*) FROM \"comment\"" | grep -E '^[[:space:]]*[0-9]+[[:space:]]*$' | tr -d ' ')t des fixtures pour Mangroove
# Usage: ./load-fixtures.sh [dev|test]

ENV=${1:-dev}

echo "╔════════════════════════════════════════╗"
echo "║          CHARGEMENT FIXTURES           ║"
echo "╚════════════════════════════════════════╝"
echo "Environnement: $ENV"

if [ "$ENV" = "test" ]; then
    echo "Mode: Test - Fixtures de base"
    docker compose exec backend php bin/console doctrine:fixtures:load --no-interaction --env=test
else
    echo "Mode: Développement - Fixtures complètes"
    docker compose exec backend php bin/console doctrine:fixtures:load --no-interaction
fi

echo ""
echo "✓ Fixtures chargées avec succès"


echo ""
echo "┌────────────────────────────────────────┐"
echo "│        STATISTIQUES BASE DONNÉES       │"
echo "├────────────────────────────────────────┤"

#
printf "│ %-22s │ %12s │\n" "Utilisateurs" $(docker compose exec backend php bin/console doctrine:query:sql "SELECT COUNT(*) FROM \"user\"" | grep -E '^[[:space:]]*[0-9]+[[:space:]]*$' | tr -d ' ')
printf "│ %-22s │ %12s │\n" "Jams" $(docker compose exec backend php bin/console doctrine:query:sql "SELECT COUNT(*) FROM \"jam\"" | grep -E '^[[:space:]]*[0-9]+[[:space:]]*$' | tr -d ' ')
printf "│ %-22s │ %12s │\n" "GameEntries" $(docker compose exec backend php bin/console doctrine:query:sql "SELECT COUNT(*) FROM \"game_entry\"" | grep -E '^[[:space:]]*[0-9]+[[:space:]]*$' | tr -d ' ')
printf "│ %-22s │ %12s │\n" "Commentaires" $(docker compose exec backend php bin/console doctrine:query:sql "SELECT COUNT(*) FROM \"comment\"" | grep -E '^[[:space:]]*[0-9]+[[:space:]]*$' | tr -d ' ')

echo "└────────────────────────────────────────┘"

echo ""
echo "┌────────────────────────────────────────┐"
echo "│             TESTS POSTMAN              │"
echo "├────────────────────────────────────────┤"
echo "│ Collection: Mangroove_API.postman_     │"
echo "│             collection.json            │"
echo "│ Environment: Mangroove_Local.postman_  │"
echo "│              environment.json          │"
echo "└────────────────────────────────────────┘"
