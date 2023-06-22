setup:
	@make build
	@make up
	@make composer-update
build:
	docker-compose build --no-cache --force-rm
stop:
	docker-compose stop
up:
	docker-compose up -d
composer-update:
	docker exec Serve bash -c "composer update"
data:
	docker exec Serve bash -c "composer require encore/laravel-admin:1.*"
	docker exec Serve bash -c "php artisan vendor:publish --provider="Encore\Admin\AdminServiceProvider""
	docker exec Serve bash -c "php artisan admin:install"
	docker exec Serve bash -c "php artisan migrate"
	docker exec Serve bash -c "php artisan db:seed"
	docker exec Serve bash -c "php artisan db:seed --class=AdminMenuSeeder"
