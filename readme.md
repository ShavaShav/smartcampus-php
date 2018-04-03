## Setup

### Install Apache/MySQL
MyWeb's Laravel is pinned to version 5.1, so need to use version PHP 5.6.34!

See https://github.com/SmartCampusUWindsor/smart-campus/wiki/Setting-up-local-dev-environment for information

### Install Laravel and composer
Composer 
Download and install from https://getcomposer.org/download/

Laravel
```
composer global require "laravel/installer"
```

### Clone and install dependencies

```
git clone https://github.com/SmartCampusUWindsor/smartcampus-api.git
cd smartcampus-api
composer install
```

## Running dev server

### Start MySQL
Create a database if nonexistent, and place credentials in `.env`:
```
DB_HOST=localhost
DB_DATABASE=smartcampus
DB_USERNAME=root
DB_PASSWORD=
```

Create tables:
```
php artisan migrate
```

Seed with mock data (for testing):
```
php artisan db:seed
```

### Start developer server

```
php artisan serve
```


## Making requests

Use curl/[Postman](https://www.getpostman.com/) to make requests to localhost:8000/api

```
curl -H "Content-Type: application/json" \
     -X POST \
     -d '{"username": "zachshaver",email":"zach@uwindsor.ca","password":"password1234"}' \
     http://localhost:8000/api/user/register
```

API spec can be found [here](https://github.com/SmartCampusUWindsor/smartcampus-api/wiki/API-Spec)

## Deploying to myWeb

Upload contents of repo to server's `/public_html` folder (different domain than front-end)