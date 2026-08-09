.PHONY: up down import-db bbash migrate seed

up:
	docker compose up -d

down:
	docker compose down

import-db:
	test -f database.sql || (echo "Fichier database.sql introuvable à la racine du projet." && exit 1)
	docker compose exec -T postgres psql -U "$${DB_USERNAME:-laravel}" -d "$${DB_DATABASE:-laravel}" < database.sql

bbash:
	docker compose exec php bash

migrate:
	docker compose exec php php artisan migrate

seed:
	docker compose exec php php artisan db:seed
