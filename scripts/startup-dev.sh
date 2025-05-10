#!/bin/bash

# Drop database if exists
php bin/console d:d:d --force --env=dev --if-exists
php bin/console d:d:d --force --env=test --if-exists

# Create the database
php bin/console d:d:c --env=dev
php bin/console d:d:c --env=test

# Update the database schema
php bin/console d:s:u --force --env=dev
php bin/console d:s:u --force --env=test

# Create the OAuth2 client
php bin/console league:oauth2-server:create-client test test test

# Create a user for testing
php bin/console app:create-user test test@test.com test test test true ROLE_ADMIN ROLE_USER

php bin/console doctrine:query:sql "$(< scripts/populate.sql)"

# Create a bucket for development
curl -X POST \
  -H "Content-Type: application/json" \
  -d '{"name": "bucket-storage"}' \
  http://fake-gcs-server:4443/storage/v1/b

# run symfony server
symfony local:server:start --port=8000 --allow-all-ip --no-tls