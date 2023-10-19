# Crypto Trade Site based on Laravel

This is a cryptocurrency trading platform built using the Laravel PHP framework as a University Project (Doesn't actually support real crypto wallets). The platform allows users to add buy and sell requests for various cryptocurrencies, view their trading history, and manage their account settings.

## Installation

To install this project, follow these steps:

1. Clone the repository to your local machine:

2. Navigate to the project directory:

3. Install the required dependencies:
```
composer install
npm install
```

4. Create a `.env` file by copying the `.env.example` file:
```
cp .env.example .env
```

5. Generate an application key:
```
php artisan key:generate
```

6. Configure your `.env` file with your database credentials and other settings.

7. Run the database migrations:
```
php artisan migrate
```

8. (Optional) Run seeder:
```
php artisan db:seed
```

9. Start the development server (2 Seperate Terminals):
```
php artisan serve
```
```
npm run dev
```

## Features

- User authentication and registration
- Buy and sell trades for cryptocurrencies
- View trading history
- Manage account settings
- Transaction history and balance

## Technologies Used

- Laravel PHP Framework
- MySQL database
- Vue.JS Framework
- Vuetify Library