start:
	docker-compose up -d

stop:
	docker-compose stop

init:
	docker-compose build
	docker-compose up -d
	docker exec backend-clean-api /usr/bin/composer install
	docker exec backend-clean-api php bin/console doctrine:migrations:migrate --no-interaction
	docker exec backend-clean-api php bin/console doctrine:fixtures:load
	cd frontend/ && npm install 
	cd frontend/ && npm run dev 
