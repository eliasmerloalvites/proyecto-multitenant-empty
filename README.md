# 📌 Nombre del Proyecto

Breve descripción del proyecto. Explica qué problema resuelve, para qué sirve y quién lo puede usar.

---

## 🚀 Tecnologías Utilizadas

- **Laravel 10+**
- **PHP 8.2+**
- **Composer**
- **MySQL / PostgreSQL / MongoDB** (coloca el que uses)
- **Docker** (opcional)
- **Laravel Sail** (opcional)
- **Redis** (opcional)
- **Queue Workers** (opcional)

---

## 📦 Requisitos

Asegúrate de tener instalado:

- PHP >= 8.2  
- Composer >= 2.x  
- MySQL/MariaDB o PostgreSQL  
- Extensiones necesarias: `OpenSSL`, `PDO`, `Mbstring`, `Tokenizer`, `XML`, `Ctype`, `JSON`  
- Node.js >= 18.x y NPM/Yarn (si el proyecto usa frontend)

---

## 🛠️ Instalación

Clona el repositorio:

```bash
git clone https://github.com/tu-usuario/tu-repo.git
cd tu-repo
```

Instala dependencias de PHP:

```bash
composer install

php artisan migrate:fresh --path=database/migrations/central

php artisan db:seed 

```

Eliminamos la carpeta storage del public y ejecutamos el siguiento comando:

```bash
php artisan storage:link

```

Instala dependencias frontend (si aplica):

```bash
npm install
npm run build
```