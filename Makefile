up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose up --build -d

assets-install:
	docker-compose exec node yarn install

assets-rebuild:
	docker-compose exec node npm rebuild node-sass --force

assets-dev:
	docker-compose exec node yarn run dev

assets-watch:
	docker-compose exec node yarn run watch

test:
	docker-compose exec app vendor/bin/phpunit

perm:
	sudo chgrp -R www-data storage bootstrap/cache
	sudo chmod -R ug+rwx storage bootstrap/cache

artisan:
	docker-compose exec app artisan
