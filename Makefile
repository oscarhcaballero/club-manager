# Variables
DOCKER_COMPOSE = docker-compose
PHP_SERVICE = app

help: ## ayuda
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@echo '--------------------------------------------'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'
	@echo '--------------------------------------------'


build-project: ## Construye las imágenes del Proyecto
	@echo "Construyendo imágenes del Proyecto..."
	$(DOCKER_COMPOSE) down
	$(DOCKER_COMPOSE) build --no-cache
	@echo "Imágenes del Proyecto creadas."

init-project: ## Inicializa el Proyecto
	@echo "Inicializando el proyecto..."
	$(DOCKER_COMPOSE) up -d 
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) composer install
	@echo "Proyecto inicializado."

clear-cache: ## Limpia la caché de la aplicación
	@echo "Limpiando la caché de la aplicación..."
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console cache:clear --env=dev
	@echo "Caché limpia!"

migrate: ## Actualiza el esquema de la base de datos
	@echo "Actualizando esquema de la base de datos..."
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console make:migration --no-interaction
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console doctrine:migrations:migrate --no-interaction
	@echo "Esquema de base de datos actualizado."

fixtures: ## Carga los fixtures en la base de datos
	@echo "Carga datos de prueba en la base de datos..."
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console doctrine:fixtures:load --no-interaction

ssh: ## bash dentro del contenedor de la web
	$(DOCKER_COMPOSE) exec -it ${PHP_SERVICE} bash

routes: ## Muestra las rutas del proyecto
	@echo "Mostrando las rutas del proyecto..."
	$(DOCKER_COMPOSE) exec $(PHP_SERVICE) php bin/console debug:router
	@echo "Rutas mostradas."
