# пересобрать контейнеры
build:
	docker-compose stop
	docker-compose build --no-cache

# удалить все контейнеры и их данные 
remove:
	docker-compose stop
	vendor/bin/sail down -v

# первоначальная установка приложения
install:
	docker-compose stop
	docker run --rm \
        -u "$$(id -u):$$(id -g)" \
        -v $$(pwd):/var/www/html \
        -w /var/www/html \
        laravelsail/php81-composer:latest \
        composer install --ignore-platform-reqs 
	vendor/bin/sail up -d
	vendor/bin/sail artisan key:generate
	vendor/bin/sail artisan storage:link
	vendor/bin/sail artisan migrate
	vendor/bin/sail artisan optimize

# запуск fronend приложения для dev (или локального) окружения
frontend-dev:
	docker exec lms_app npm install
	docker exec lms_app npm run watch

# запуск fronend приложения для dev (или локального) окружения
frontend-prod:
	docker exec lms_app npm install
	docker exec lms_app npm run prod

# запустить приложение
run:
	vendor/bin/sail stop
	vendor/bin/sail up -d
	vendor/bin/sail ps
	vendor/bin/sail composer install
	vendor/bin/sail artisan optimize
	vendor/bin/sail artisan migrate

# оптимизация laravel приложения (установка пакетов, применение миграций, сброс кеша)
optimize:
	vendor/bin/sail composer install
	vendor/bin/sail artisan optimize
	vendor/bin/sail artisan migrate

# посмотреть список и статус контейнеров
ps:
	vendor/bin/sail ps

# остановить все контейнеры
stop:
	vendor/bin/sail stop

# войти в контейнер приложения (laravel)
app_exec:
	docker exec -it lms_app bash

# подключиться к базе данных приложения (mysql)
db_exec:
	docker exec -it lms_db mysql -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE};

# посмотреть логи всех контейнеров
logs:
	vendor/bin/sail logs

# посмотреть логи laravel-контейнера
app_logs:
	vendor/bin/sail logs laravel

# посмотреть логи контейнера базы данных
db_logs:
	vendor/bin/sail logs mysql
