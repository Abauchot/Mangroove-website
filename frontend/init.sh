#!/bin/bash

echo "=== INIT.SH ==="

if [ ! -f package.json ]; then
  echo "Initialisation d'un projet Vue..."
  npm create vue@latest . -- --default
  npm install primevue primeicons
else
  echo "Projet déjà initialisé"
fi

npm install
npm run dev -- --host
