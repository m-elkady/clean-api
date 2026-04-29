start:
	docker-compose up -d

stop:
	docker-compose stop

restart:
	docker-compose restart

frontend-build:
	docker-compose build --no-cache frontend

init:
	docker-compose build
	docker-compose up -d
	docker exec backend-clean-api /usr/bin/composer install
	docker exec backend-clean-api php bin/console doctrine:migrations:migrate --no-interaction
	docker exec backend-clean-api php bin/console doctrine:fixtures:load --no-interaction

# Run backend commands
console:
	docker exec backend-clean-api php bin/console $(filter-out $@,$(MAKECMDGOALS))

# Run tests
test:
	docker exec backend-clean-api php bin/phpunit