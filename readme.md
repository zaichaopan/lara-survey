# Lara Survey

It is a Laravel 5.5 app.

The application allows users to sign up and create surveys. Survey authors can send invitations to recipients to answers the questions on the survey. Survey author can also see the summaries of their surveys.

## Installation

### Step 1

```bash
git clone git@github.com:zaichaopan/lara-survey.git
cd lara-survey && composer install
mv .env.example .env
php artisan key:generate
```

### Step 2

Next, create a new database and reference its name and username/password within the project's .env file. In the example below, we've named the database, "lara_survey".

```.env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lara_survey
DB_USERNAME=root
DB_PASSWORD=
```

### Step 3

Then, migrate your database to create tables.

```bash
php artisan migrate
```

### Step 4

Configure mail settings in .env. A simple way is to create a mailtrap account and place your username and password in .env

```
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=yourUseName
MAIL_PASSWORD=yourPassword
MAIL_ENCRYPTION=null
```

## Testing

The app is built using TDD. To run the all tests, in project's root directory

```bash
vendor/bin/phpunit
```
