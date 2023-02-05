build:
	docker-compose build --no-cache

up:
	if [ ! -e .env ]; then cp .env.example .env; fi
	docker-compose up -d
	docker-compose exec php-fpm composer install
	docker-compose exec php-fpm php artisan optimize:clear
	docker-compose exec php-fpm php artisan key:generate
	docker-compose exec php-fpm php artisan migrate
	docker-compose exec php-fpm php artisan optimize
	docker-compose exec php-fpm php artisan l5-swagger:generate
	docker-compose exec php-fpm npm run dev

stop:
	docker-compose stop

down:stop
	docker-compose down

exec:
	docker exec -it php-fpm sh

swagger:
	docker-compose exec php-fpm php artisan l5-swagger:generate

optimize:
	docker-compose exec php-fpm php artisan optimize:clear