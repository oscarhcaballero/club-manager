# Club Manager

Este es un proyecto Symfony para gestionar clubes, jugadores y entrenadores.


## Instalación

Sigue estos pasos para instalar y configurar el proyecto:


**Clona el repositorio**

    git clone https://github.com/oscarhcaballero/club-manager.git
    cd club-manager


**Construye las imágenes de Docker:**

    make build-project


**Inicializa el proyecto:**

    make init-project

**Crea las tablas de la base de datos:**

    make migrate

**Carga los datos de prueba:**

    make fixtures


## Acceso a phpMyAdmin
Puedes acceder a phpMyAdmin en http://localhost:8080 para gestionar la base de datos MySQL. Utiliza las credenciales definidas en el archivo .env


## Base de datos de Prueba ##
La puedes encontrar en:  build/mysql/dbmanager.sql


## Colección Postman 
La puedes encontrar en el directorio build/postman
Importa el archivo en Postman para tener disponible todos los endpoints de la Api
