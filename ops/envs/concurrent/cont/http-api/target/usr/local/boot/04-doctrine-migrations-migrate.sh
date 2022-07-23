#!/bin/sh
if [ -f vendor/bin/doctrine-migrations ]; then
    runuser -l hostuser -c "APPLICATION_ENVIRONMENT="$APPLICATION_ENVIRONMENT" DB_HOST="$DB_HOST" DB_USER="$DB_USER" DB_PASSWORD="$DB_PASSWORD" DB_NAME="$DB_NAME" vendor/bin/doctrine-migrations migrate --no-interaction"
fi