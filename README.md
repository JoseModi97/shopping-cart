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

This document provides detailed information on how to interact with the API endpoints.

## Authentication Endpoints

### Register

To register a new user, send a `POST` request to `/api/auth/register`.

#### Request Body

The request body must be a JSON object with the following parameters:

| Parameter  | Type   | Description                |
| ---------- | ------ | -------------------------- |
| `username` | string | The desired username.      |
| `email`    | string | The user's email address.  |
| `password` | string | The user's password.       |

**Example:**

```json
{
  "username": "testuser",
  "email": "test@example.com",
  "password": "password123"
}
```

#### Responses

- **201 Created:** The user was successfully registered. The response body will contain a success message and the new user's information (excluding the password).

  **Example:**

  ```json
  {
    "message": "User registered successfully.",
    "user": {
      "id": 1,
      "username": "testuser",
      "email": "test@example.com"
    }
  }
  ```

- **409 Conflict:** The username or email address already exists.

  **Example:**

  ```json
  {
    "error": "Username or email already exists."
  }
  ```

### Login

To log in, send a `POST` request to `/api/auth/login`.

#### Request Body

The request body must be a JSON object with the following parameters:

| Parameter  | Type   | Description          |
| ---------- | ------ | -------------------- |
| `username` | string | The user's username. |
| `password` | string | The user's password. |

**Example:**

```json
{
  "username": "testuser",
  "password": "password123"
}
```

#### Responses

- **200 OK:** The user was successfully logged in. The response body will contain a JWT token and the user's information (excluding the password).

  **Example:**

  ```json
  {
    "token": "your.jwt.token",
    "user": {
      "id": 1,
      "username": "testuser",
      "email": "test@example.com"
    }
  }
  ```

- **401 Unauthorized:** Invalid username or password.

  **Example:**

  ```json
  {
    "error": "Invalid username or password."
  }
  ```

## AI

### Ask a question about a product

To ask a question about a product, send a `POST` request to `/api/ai/ask`.

**Authentication:** This endpoint requires a valid JWT token in the `Authorization` header.

#### Request Body

The request body must be a JSON object with the following parameters:

| Parameter   | Type    | Description                  |
| ----------- | ------- | ---------------------------- |
| `productId` | integer | The ID of the product.       |
| `question`  | string  | The question about the product. |

**Example:**

```json
{
  "productId": 1,
  "question": "What are the specifications of this product?"
}
```

#### Responses

- **200 OK:** The request was successful. The response body will contain the answer to the question.

  **Example:**

  ```json
  {
    "answer": "This is a simulated answer about Product Name. For detailed information, please consult the official documentation."
  }
  ```

- **404 Not Found:** The product with the specified ID was not found.

  **Example:**

  ```json
  {
    "error": "Product not found"
  }
  ```

## Profile

All profile endpoints require a valid JWT token in the `Authorization` header.

### Get user profile

To get the user's profile information, send a `GET` request to `/api/profile`.

#### Responses

- **200 OK:** The request was successful. The response body will contain the user's profile information.

  **Example:**

  ```json
  {
    "id": 1,
    "username": "testuser",
    "email": "test@example.com",
    "name": {
      "first": "Test",
      "last": "User"
    },
    "phone": "123-456-7890",
    "address": {
      "street": "123 Main St",
      "city": "Anytown",
      "state": "CA",
      "zip": "12345"
    }
  }
  ```

### Update user profile

To update the user's profile information, send a `POST` request to `/api/profile` with `multipart/form-data`.

#### Request Body

| Parameter | Type   | Description                  |
| --------- | ------ | ---------------------------- |
| `name`    | string | The user's name.             |
| `phone`   | string | The user's phone number.     |
| `address` | string | The user's address.          |
| `image`   | file   | (Optional) The user's profile image. |

**Example:**

```bash
curl -X POST -H "Authorization: Bearer <token>" \
-F "name=Test User" \
-F "phone=123-456-7890" \
-F "address=123 Main St" \
-F "image=@/path/to/image.jpg" \
http://localhost:8000/api/profile
```

#### Responses

- **200 OK:** The profile was successfully updated. The response body will contain the updated profile information.

## Cart

All cart endpoints require a valid JWT token in the `Authorization` header.

### Get cart contents

To get the contents of the user's cart, send a `GET` request to `/api/cart`.

#### Responses

- **200 OK:** The request was successful. The response body will contain the items in the cart and the subtotal.

  **Example:**

  ```json
  {
    "items": [
      {
        "id": 1,
        "productName": "Product Name",
        "quantity": 2,
        "price": 99.99
      }
    ],
    "subtotal": 199.98
  }
  ```

### Add item to cart

To add an item to the cart, send a `POST` request to `/api/cart`.

#### Request Body

The request body must be a JSON object with the following parameters:

| Parameter   | Type    | Description                  |
| ----------- | ------- | ---------------------------- |
| `variantId` | integer | The ID of the product variant. |
| `quantity`  | integer | The quantity to add.         |

**Example:**

```json
{
  "variantId": 1,
  "quantity": 1
}
```

#### Responses

- **200 OK:** The item was successfully added to the cart. The response body will be the same as the "Get cart contents" endpoint.

### Remove item from cart

To remove an item from the cart, send a `DELETE` request to `/api/cart/{itemId}`.

#### Responses

- **204 No Content:** The item was successfully removed from the cart.

## Orders

All order endpoints require a valid JWT token in the `Authorization` header.

### Place an order

To place an order, send a `POST` request to `/api/orders`.

#### Responses

- **201 Created:** The order was successfully placed. The response body will contain a success message and the order ID.

  **Example:**

  ```json
  {
    "message": "Order placed successfully!",
    "orderId": "ORD-1"
  }
  ```

- **400 Bad Request:** The cart is empty.

  **Example:**

  ```json
  {
    "error": "Cart is empty."
  }
  ```

## Products

### Create a new product

To create a new product, send a `POST` request to `/api/products` with `multipart/form-data`.

#### Request Body

| Parameter   | Type   | Description                  |
| ----------- | ------ | ---------------------------- |
| `name`      | string | The product's name.          |
| `description` | string | The product's description.   |
| `price`     | float  | The product's price.         |
| `category`  | string | The product's category.      |
| `stock`     | integer| The product's stock quantity.|
| `image`     | file   | (Optional) The product's image. |

**Example:**

```bash
curl -X POST \
-F "name=Test Product" \
-F "description=This is a test product." \
-F "price=99.99" \
-F "category=Electronics" \
-F "stock=100" \
-F "image=@/path/to/image.jpg" \
http://localhost:8000/api/products
```

#### Responses

- **201 Created:** The product was successfully created. The response body will contain the created product object.

### Get all products

To get a list of all products, send a `GET` request to `/api/products`.

#### Responses

- **200 OK:** The request was successful. The response body will contain an array of product objects.

  **Example:**

  ```json
  [
    {
      "id": 1,
      "name": "Product Name",
      "description": "Product description.",
      "price": 99.99,
      "image_url": "https://example.com/product.jpg"
    }
  ]
  ```

### Get a single product

To get a single product by its ID, send a `GET` request to `/api/products/{productId}`.

#### Responses

- **200 OK:** The request was successful. The response body will contain the product object.

  **Example:**

  ```json
  {
    "id": 1,
    "name": "Product Name",
    "description": "Product description.",
    "price": 99.99,
    "image_url": "https://example.com/product.jpg"
  }
  ```

- **404 Not Found:** The product with the specified ID was not found.

  **Example:**

  ```json
  {
    "error": "Product not found"
  }
  ```
