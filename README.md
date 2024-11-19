# helpflorist-server API Documentation

Welcome to the **helpflorist-server** API documentation. This API is built using **vanilla PHP 8** and provides endpoints for managing applications, users, roles, and authentication. Below you will find all the necessary information to get started, including installation instructions, configuration details, and a comprehensive guide to the available endpoints.

## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Getting Started](#getting-started)
- [Authentication](#authentication)
- [API Endpoints](#api-endpoints)
  - [Health Check](#health-check)
  - [Authentication Endpoints](#authentication-endpoints)
  - [Users](#users)
  - [Roles](#roles)
  - [Applications](#applications)
- [Schemas](#schemas)
- [Responses](#responses)
- [Error Handling](#error-handling)
- [Contributing](#contributing)
- [License](#license)

## Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/yourusername/helpflorist-server.git
   ```

2. **Navigate to the project directory:**

   ```bash
   cd helpflorist-server
   ```

3. **Run Settings.php**

  ```bash
  php settings.php
  ```

4. **Set up your web server:**

   - Ensure that your server is running PHP 8.
   - Configure your web server's document root to point to the `public` directory (if using a `public` directory for public files).
   - Make sure URL rewriting is enabled (e.g., using `.htaccess` for Apache).

## Configuration

A `settings.ini` file is required in the root directory of the project. This file contains configuration options for the environment, database, error logging, and JWT authentication.

Create a `settings.ini` file with the following content:

```ini
[environment]
mode = "development"

[database]
path = "database.db"

[error_logging]
log_errors = On
error_log = "logs/error.log"

[jwt]
secret = "<your_secret_key>"
```

- **Environment Modes:**
  - `development`: Enables verbose error messages and logging.
  - `production`: Disables detailed error messages for security.

- **Database Configuration:**
  - `path`: Path to your SQLite database file.

- **Error Logging:**
  - `log_errors`: Set to `On` to enable error logging.
  - `error_log`: Path to the error log file.

- **JWT Configuration:**
  - `secret`: Secret key used for signing JWT tokens. **Replace `<your_secret_key>` with your actual secret key. Keep this secret and do not share it publicly.**

## Getting Started

1. **Initialize the Database:**

   - Run any database migrations or setup scripts provided to initialize your database.

2. **Start the Server:**

   - Using PHP's built-in server:

     ```bash
     php -S localhost:8000 -t public
     ```

   - Or configure your web server accordingly.

3. **Access the API:**

   - The API will be accessible at `http://localhost:8000/v1/`.

## Authentication

The API uses **JWT (JSON Web Tokens)** for authentication. The JWT token is stored in a cookie named `token`. You need to obtain a token by logging in, and the token will be automatically set in the cookie.

- **Login to Obtain Token:**

  ```http
  POST /v1/auth
  Content-Type: application/json

  {
    "username": "your_username",
    "password": "your_password"
  }
  ```

- **Token Storage:**

  - Upon successful authentication, the server will set a cookie named `token` with the JWT token as its value.
  - The cookie should be included automatically in subsequent requests by your HTTP client or browser.

- **Include Token in Requests:**

  - Ensure that your HTTP client or browser is configured to send cookies when making requests to protected endpoints.

## API Endpoints

### Health Check

#### GET `/v1/health`

- **Description:** Check if the API is running.
- **Protected:** No
- **Required Role:** None
- **Response:**

  ```json
  {
    "message": "OK",
    "data": null
  }
  ```

### Authentication Endpoints

#### POST `/v1/auth`

- **Description:** Login to get an authentication token.
- **Protected:** No
- **Required Role:** None
- **Request Body:**

  ```json
  {
    "username": "string",
    "password": "string"
  }
  ```

- **Response:**

  ```json
  {
    "message": "Login successful",
    "data": null
  }
  ```

- **Notes:**
  - On success, a `token` cookie will be set in the response.

#### PUT `/v1/auth`

- **Description:** Refresh the authentication token.
- **Protected:** Yes
- **Required Role:** 1 (User or Higher)
- **Response:**

  ```json
  {
    "message": "Token refreshed successfully",
    "data": null
  }
  ```

- **Notes:**
  - The server will set a new `token` cookie with the refreshed JWT token.

#### DELETE `/v1/auth`

- **Description:** Logout and remove the authentication token.
- **Protected:** No
- **Required Role:** None
- **Response:**

  ```json
  {
    "message": "Logout successful",
    "data": null
  }
  ```

- **Notes:**
  - The `token` cookie will be cleared.

#### GET `/v1/auth`

- **Description:** Verify the current authentication token.
- **Protected:** Yes
- **Required Role:** 1 (User or Higher)
- **Response:**

  ```json
  {
    "message": "Token verified successfully",
    "data": null
  }
  ```

### Users

#### GET `/v1/users`

- **Description:** Retrieve user information.
- **Protected:** Yes
- **Required Role:** 2 (Moderator or Higher)
- **Query Parameters:**
  - `id=[integer]`: Get a user by ID.
    - Example: `/v1/users?id=1`
  - `id=all` or no `id`: Get all users.
    - Example: `/v1/users?id=all` or `/v1/users`

- **Response:**

  ```json
  {
    "message": "Users retrieved successfully",
    "data": [
      {
        "id": "integer",
        "username": "string",
        "email": "string",
        "role_id": "integer",
        "created_at": "string",
        "updated_at": "string"
      }
    ]
  }
  ```

#### POST `/v1/users`

- **Description:** Create a new user.
- **Protected:** Yes
- **Required Role:** 2 (Moderator or Higher)
- **Request Body:**

  ```json
  {
    "username": "string",
    "email": "string",
    "password": "string",
    "role_id": "integer"
  }
  ```

- **Response:**

  ```json
  {
    "message": "User created successfully",
    "data": {
      "id": "integer",
      "username": "string",
      "email": "string",
      "role_id": "integer",
      "created_at": "string",
      "updated_at": "string"
    }
  }
  ```

#### PUT `/v1/users`

- **Description:** Update an existing user.
- **Protected:** Yes
- **Required Role:** 2 (Moderator or Higher)
- **Request Body:**

  ```json
  {
    "id": "integer",
    "username": "string (optional)",
    "email": "string (optional)",
    "password": "string (optional)",
    "role_id": "integer (optional)"
  }
  ```

- **Response:**

  ```json
  {
    "message": "User updated successfully",
    "data": {
      "id": "integer",
      "username": "string",
      "email": "string",
      "role_id": "integer",
      "created_at": "string",
      "updated_at": "string"
    }
  }
  ```

#### DELETE `/v1/users`

- **Description:** Delete a user.
- **Protected:** Yes
- **Required Role:** 3 (Admin)
- **Request Body:**

  ```json
  {
    "id": "integer"
  }
  ```

- **Response:**

  ```json
  {
    "message": "User deleted successfully",
    "data": null
  }
  ```

### Roles

#### GET `/v1/roles`

- **Description:** Retrieve role information.
- **Protected:** Yes
- **Required Role:** 2 (Moderator or Higher)
- **Query Parameters:**
  - `id=[integer]`: Get a role by ID.
    - Example: `/v1/roles?id=1`
  - `id=all` or no `id`: Get all roles.
    - Example: `/v1/roles?id=all` or `/v1/roles`

- **Response:**

  ```json
  {
    "message": "Roles retrieved successfully",
    "data": [
      {
        "id": "integer",
        "name": "string"
      }
    ]
  }
  ```

#### POST `/v1/roles`

- **Description:** Create a new role.
- **Protected:** Yes
- **Required Role:** 2 (Moderator or Higher)
- **Request Body:**

  ```json
  {
    "name": "string"
  }
  ```

- **Response:**

  ```json
  {
    "message": "Role created successfully",
    "data": {
      "id": "integer",
      "name": "string"
    }
  }
  ```

#### PUT `/v1/roles`

- **Description:** Update an existing role.
- **Protected:** Yes
- **Required Role:** 2 (Moderator or Higher)
- **Request Body:**

  ```json
  {
    "id": "integer",
    "name": "string (optional)"
  }
  ```

- **Response:**

  ```json
  {
    "message": "Role updated successfully",
    "data": {
      "id": "integer",
      "name": "string"
    }
  }
  ```

#### DELETE `/v1/roles`

- **Description:** Delete a role.
- **Protected:** Yes
- **Required Role:** 3 (Admin)
- **Request Body:**

  ```json
  {
    "id": "integer"
  }
  ```

- **Response:**

  ```json
  {
    "message": "Role deleted successfully",
    "data": null
  }
  ```

### Applications

#### GET `/v1/applications`

- **Description:** Retrieve application information.
- **Protected:** No
- **Required Role:** None
- **Query Parameters:**
  - `id=[integer]`: Get an application by ID.
    - Example: `/v1/applications?id=1`
  - `id=all` or no `id`: Get all applications.
    - Example: `/v1/applications?id=all` or `/v1/applications`

- **Response:**

  ```json
  {
    "message": "Applications retrieved successfully",
    "data": [
      {
        "id": "integer",
        "name": "string",
        "version": "string",
        "type": "string",
        "download_url": "string",
        "created_by": "integer",
        "created_at": "string",
        "updated_by": "integer",
        "updated_at": "string"
      }
    ]
  }
  ```

#### POST `/v1/applications`

- **Description:** Create a new application.
- **Protected:** Yes
- **Required Role:** 2 (Moderator or Higher)
- **Request Body:**

  ```json
  {
    "name": "string",
    "version": "string",
    "type": "string",
    "download_url": "string"
  }
  ```

- **Response:**

  ```json
  {
    "message": "Application created successfully",
    "data": {
      "id": "integer",
      "name": "string",
      "version": "string",
      "type": "string",
      "download_url": "string",
      "created_by": "integer",
      "created_at": "string",
      "updated_by": "integer",
      "updated_at": "string"
    }
  }
  ```

#### PUT `/v1/applications`

- **Description:** Update an existing application.
- **Protected:** Yes
- **Required Role:** 2 (Moderator or Higher)
- **Request Body:**

  ```json
  {
    "id": "integer",
    "name": "string (optional)",
    "version": "string (optional)",
    "type": "string (optional)",
    "download_url": "string (optional)"
  }
  ```

- **Response:**

  ```json
  {
    "message": "Application updated successfully",
    "data": {
      "id": "integer",
      "name": "string",
      "version": "string",
      "type": "string",
      "download_url": "string",
      "created_by": "integer",
      "created_at": "string",
      "updated_by": "integer",
      "updated_at": "string"
    }
  }
  ```

#### DELETE `/v1/applications`

- **Description:** Delete an application.
- **Protected:** Yes
- **Required Role:** 3 (Admin)
- **Request Body:**

  ```json
  {
    "id": "integer"
  }
  ```

- **Response:**

  ```json
  {
    "message": "Application deleted successfully",
    "data": null
  }
  ```

## Schemas

### Applications Schema

| Field          | Type     | Description                  |
|----------------|----------|------------------------------|
| `id`           | integer  | Unique identifier            |
| `name`         | string   | Application name             |
| `version`      | string   | Application version          |
| `type`         | string   | Application type             |
| `download_url` | string   | URL to download the app      |
| `created_by`   | integer  | ID of the creator            |
| `created_at`   | string   | Creation timestamp           |
| `updated_by`   | integer  | ID of the last updater       |
| `updated_at`   | string   | Last update timestamp        |

### Users Schema

| Field        | Type     | Description                  |
|--------------|----------|------------------------------|
| `id`         | integer  | Unique identifier            |
| `username`   | string   | Username                     |
| `password`   | string   | Password (hashed)            |
| `email`      | string   | User email                   |
| `role_id`    | integer  | Role ID                      |
| `created_at` | string   | Creation timestamp           |
| `updated_at` | string   | Last update timestamp        |

### Roles Schema

| Field  | Type    | Description       |
|--------|---------|-------------------|
| `id`   | integer | Unique identifier |
| `name` | string  | Role name         |

- **Role Definitions:**
  - **Role 1:** User
  - **Role 2:** Moderator
  - **Role 3:** Admin

## Responses

### Success Responses

- **200 OK:**

  - **Message:** General success message.
  - **Data:** Object or array (optional).

- **201 Created:**

  - **Message:** Resource created successfully.
  - **Data:** Created object.

### Error Responses

- **400 Bad Request:**

  - **Message:** "Bad Request"
  - **Data:** Error message (development only).

- **401 Unauthorized:**

  - **Message:** "Unauthorized"

- **403 Forbidden:**

  - **Message:** "Forbidden"

- **404 Not Found:**

  - **Message:** "Not Found"

- **405 Method Not Allowed:**

  - **Message:** "Method Not Allowed"

- **500 Internal Server Error:**

  - **Message:** "Internal Server Error"
  - **Data:** Error message (development only).

## Error Handling

- **Development Mode:**

  - Detailed error messages and stack traces are provided in the responses and logs.

- **Production Mode:**

  - Error messages are generic to prevent sensitive information leakage.

- **Logging:**

  - Errors are logged to the file specified in the `settings.ini` under `[error_logging]`.

## Contributing

Contributions are welcome! Please submit a pull request or open an issue to discuss your ideas.

## License

This project is licensed under the **MIT License**.

---

**Note:** Replace placeholder URLs, repository information, and `<your_secret_key>` with actual data relevant to your project. Ensure that the secret key remains confidential and is not shared publicly.
