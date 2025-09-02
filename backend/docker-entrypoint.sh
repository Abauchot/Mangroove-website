#!/bin/bash

if [ ! -d vendor ]; then
  echo "📦 Installation des dépendances Composer..."
  composer install
fi

php -S 0.0.0.0:8000 -t public
