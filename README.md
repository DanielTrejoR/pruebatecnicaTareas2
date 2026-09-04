# Prueba Técnica - Gestor de Tareas

Aplicación web desarrollada con Laravel, MySQL y Vue 3 para la gestión de usuarios y tareas.

## Tecnologías

- PHP 8.5+
- Laravel 13
- MySQL 8
- Laravel Sanctum
- Vue 3
- TypeScript
- Vite
- Fetch API
- ES6 Modules

## Requisitos

- PHP 8.5 o superior
- Composer
- Node.js y npm
- MySQL 8 o superior

## Instalación

Clonar el repositorio:
```
git clone <URL_DEL_REPOSITORIO>
cd pruebatecnicaTareas2
```
Instalar las dependencias de PHP:
```
composer install
```
## Instalar las dependencias de JavaScript:
```
npm install
```
Crear el archivo de entorno:
```
cp .env.example .env
```
Generar la clave de la aplicación:
```
php artisan key:generate
```
## Configuración de la base de datos

Configurar las credenciales de MySQL en el archivo .env:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pruebatecnica_tareas
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```
Crear la base de datos en MySQL si todavía no existe.

Migraciones y Seeder

Ejecutar:

php artisan migrate --seed

El seeder crea un usuario de prueba:
```
Email: demo@example.com
Password: password123
```

Ejecución del proyecto


En una terminal ejecutar:
```
php artisan serve
```
En otra terminal ejecutar:
```
npm run dev
```
La aplicación estará disponible en:
```
http://localhost:8000
```
Autenticación

La API utiliza Laravel Sanctum mediante personal access tokens.

Para iniciar sesión:
```
POST /api/login

Ejemplo de petición:

{
    "email": "demo@example.com",
    "password": "password123"
}
```
La respuesta contiene un token que el frontend utiliza para autenticarse en las demás peticiones:

Authorization: Bearer <token>

El token se almacena temporalmente en sessionStorage del navegador.

API
Usuarios

Listar usuarios:
```
GET /api/users
```
Crear usuario:
```
POST /api/users

Ejemplo:

{
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "password": "password123"
}
```

Tareas

Listar tareas de un usuario:
```
GET /api/users/{user}/tasks
```
Crear una tarea:
```
POST /api/users/{user}/tasks

Ejemplo:

{
    "title": "Preparar reporte",
    "description": "Preparar el reporte mensual del proyecto."
}
```
Completar una tarea:
```
PATCH /api/tasks/{task}/complete
```
Eliminar una tarea:
```
DELETE /api/tasks/{task}
```
Filtros y ordenamiento

Las tareas pueden filtrarse por estado.

Tareas completadas:
```
GET /api/users/{user}/tasks?completed=true
```
Tareas pendientes:
```
GET /api/users/{user}/tasks?completed=false
```
También pueden ordenarse por título:
```
GET /api/users/{user}/tasks?sort=title
```
o por fecha:
```
GET /api/users/{user}/tasks?sort=date
```
Validación y manejo de errores

Las peticiones de creación utilizan Laravel FormRequest para validar los datos de entrada.

La API contempla respuestas HTTP para diferentes situaciones, entre ellas:

- 201 para recursos creados correctamente.
- 403 para operaciones no autorizadas.
- 404 cuando el recurso solicitado no existe.
- 422 cuando los datos enviados no cumplen las reglas de validación.
- 500 para errores internos no controlados.
- Arquitectura
- Laravel expone una API REST protegida mediante Sanctum.
- Los Controllers reciben las peticiones y delegan la lógica.
- Los FormRequests centralizan la validación de entrada.
- AuthService encapsula la autenticación y generación de tokens.
- UserService encapsula las operaciones relacionadas con usuarios.
- TaskManager concentra las operaciones relacionadas con tareas.
- Eloquent administra los modelos y la relación User-Task.
- Vue 3 consume la API mediante Fetch, async/await y módulos ES6.
- Frontend

La interfaz está desarrollada con Vue 3 y TypeScript.

Las peticiones HTTP se realizan mediante la Fetch API y se encuentran centralizadas en:

resources/js/services/api.ts

Los tipos utilizados por el frontend se encuentran en:

resources/js/types/task.ts

## La interfaz permite:

Iniciar sesión.
Listar usuarios.
Crear usuarios.
Seleccionar un usuario.
Listar sus tareas.
Crear tareas.
Completar tareas.
Eliminar tareas.
Filtrar tareas completadas y pendientes.
Ordenar tareas por título o fecha.
Estructura principal
```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   └── Requests/
├── Models/
└── Services/

database/
├── migrations/
└── seeders/

resources/
├── js/
│   ├── pages/
│   ├── services/
│   └── types/
└── views/


routes/
└── api.php
```

Credenciales de prueba
```
Usuario creado automáticamente por el seeder:

Email: demo@example.com
Password: password123
```
Estas credenciales son únicamente para facilitar la ejecución y demostración del proyecto.

Autor

Prueba Prueba Técnica – Programador
Laravel + OOP + ECMAScript + MySQL
