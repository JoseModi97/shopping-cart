# ElectroMart API

This is the backend API for the ElectroMart application, built with PHP and SQLite.

## Features

-   **RESTful API:** Standard HTTP methods for resource manipulation.
-   **Authentication:** JWT-based authentication for secure endpoints.
-   **User Profiles:** Manage user information.
-   **Product Catalog:** Browse products.
-   **Shopping Cart:** Add and manage items in the cart.
-   **Orders:** Place orders from the cart.
-   **AI Assistant:** Get product information via an AI-powered assistant.

## Setup

### Prerequisites

-   PHP >= 7.4
-   SQLite3
-   Composer

### Local Development

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/your-username/electromart-api.git
    cd electromart-api
    ```

2.  **Install dependencies:**
    ```bash
    composer install
    ```

3.  **Initialize the database:**
    ```bash
    php api/db/init.php
    ```

4.  **Start the development server:**
    ```bash
    php -S localhost:8000 -t api
    ```

The API will be available at `http://localhost:8000`.

### Apache Setup

1.  **Configure Apache:**
    -   Point your virtual host's `DocumentRoot` to the `api` directory.
    -   Enable `mod_rewrite` and use an `.htaccess` file to route all requests to `index.php`.

2.  **Create `.htaccess` in the `api` directory:**
    ```apache
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [QSA,L]
    ```

## API Endpoints

-   `POST /api/auth/register`
-   `POST /api/auth/login`
-   `GET /api/profile`
-   `PUT /api/profile`
-   `GET /api/products`
-   `GET /api/products/:id`
-   `GET /api/cart`
-   `POST /api/cart`
-   `DELETE /api/cart/:itemId`
-   `POST /api/orders`
-   `POST /api/ai/ask`
