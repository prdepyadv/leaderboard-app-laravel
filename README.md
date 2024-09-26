# Leaderboard App - Laravel

This is a leaderboard application built with the Laravel framework. The application allows users to be added, updated, and deleted, and scores can be incremented, decremented, reset, and reported.

## Running the Application

To run this application on your local machine, follow these steps:

### 1. Clone the repository

```sh
git clone https://github.com/prdepyadv/leaderboard-app-laravel
cd leaderboard-app-laravel
```

### 2. Install dependencies

```sh
composer install
```

### 3. Set up the environment file

```sh
cp .env.example .env
```

### 4. Generate the application key

```sh
php artisan key:generate
```

### 5. Run migrations

```sh
php artisan migrate
```

### 6. Start the development server

```sh
php artisan serve
```

Your application should now be running at `http://localhost:8000`

## New Features

### 1. Automatic Winner Selection

### 2. QR Code Generation for User Address

## API Routes

This application includes several API routes for managing users and winners. All API routes are prefixed with `/api`.

### Users Routes

- **GET `/api/users`**  
  Fetch all users.

- **POST `/api/users`**  
  Add a new user.

- **GET `/api/users/scores/report`**  
  Fetch a report of user scores.

- **POST `/api/users/scores/reset`**  
  Reset all user scores.

- **PUT `/api/user/{id}/increment`**  
  Increment a user's score by one.

- **PUT `/api/user/{id}/decrement`**  
  Decrement a user's score by one.

- **GET `/api/user/{id}`**  
  Find a specific user by ID.

- **DELETE `/api/user/{id}`**  
  Delete a user by ID.

### Winners Routes

- **GET `/api/winners`**  
  Fetch a list of all winners.
