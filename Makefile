start:
	docker compose up -d

stop:
	docker compose down

bash:
	docker exec -ti symfony_php_gestion_conger bash

build:
	docker compose up -d --build && \
	docker exec symfony_php_gestion_conger composer install

cc:
	docker exec symfony_php_gestion_conger php bin/console cache:clear

restart:
	docker compose down && \
	docker compose up -d && \
	docker exec symfony_php_gestion_conger php bin/console cache:clear

