```bash
docker-compose up -d --build
docker-compose exec app php -d memory-limit=-1 /usr/local/bin/composer install
docker-compose exec app artisan php artisan key:generate
docker-compose exec app artisan migrate
docker-compose exec app artisan ide-helper:generate #чтобы в ide не подсвечивало классы
curl localhost:8580 #проверка, что проект работает, должен вернуть html
```
