#!/bin/bash
set -e

# Vérifier si la variable d'environnement POSTGRES_MULTIPLE_DATABASES est définie
if [ -n "$POSTGRES_MULTIPLE_DATABASES" ]; then
    echo "Création des bases de données : $POSTGRES_MULTIPLE_DATABASES"
    for db in $(echo $POSTGRES_MULTIPLE_DATABASES | tr ',' ' '); do
        echo "  Création de la base de données '$db'"
        psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
            CREATE DATABASE "$db";
        EOSQL
    done
    echo "Bases de données créées avec succès."
fi
