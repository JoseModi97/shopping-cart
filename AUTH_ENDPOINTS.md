# Authentication Endpoints

This document provides detailed information on how to interact with the authentication endpoints.

## Register

To register a new user, send a `POST` request to `/api/auth/register`.

### Request Body

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

### Responses

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

## Login

To log in, send a `POST` request to `/api/auth/login`.

### Request Body

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

### Responses

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
