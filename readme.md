REST API Sample
=================

Sample REST API for working with two entities — **articles** and **users**, with authorization support.

## 🐳 Docker Setup

This project uses the following basic environment setup:

- **PHP 8.2 CLI** with extensions: `pdo`, `pdo_mysql`, `mysqli`
- MySQL 8.0 database with user, password, and database name as defined in `docker-compose.yml`

For detailed configuration, please see the `Dockerfile` and `docker-compose.yml` files.

### Important

If you are **not running the project via Docker**, you need to configure your environment to match these settings as closely as possible. This includes:

- Installing the required PHP version and extensions
- Setting up a MySQL 8.0 database
- Mounting or placing project files in the correct path

Additionally, **you might need to adjust URLs or paths in tests** to reflect your local environment if it differs from the Docker setup.

Keeping environments aligned ensures consistent behavior and successful test runs.

## ⚙️ Installed Extensions and Framework

This project runs on the **Nette Framework** and uses the following key Composer packages:

| Package                 | Purpose                          |
|-------------------------|---------------------------------|
| `nettrine/orm`          | ORM integration for Nette        |
| `nettrine/migrations`   | Database migrations support      |
| `contributte/console`   | Console commands integration     |
| `firebase/php-jwt`      | JWT (JSON Web Token) authentication |


## 🚀 Startup Instructions

1. **Clone the repository:**

```bash
git clone https://github.com/jan-krcal/rest-api-sample.git
```

2. **Install dependencies using Composer:**

```bash
composer install
```

3. **Start the Docker containers:**

```bash
docker-compose up --build
```

4. **Check and run migrations:**

```bash
php bin/console.php migrations:status
php bin/console.php migrations:migrate -n
```

## ✅ Tests

The project contains 4 tests and uses [Nette Tester](https://tester.nette.org/) for testing.

**Run tests with:**

```bash
php vendor/bin/tester tests/
```

or run specific test

```bash
php vendor/bin/tester tests/<filename>.php
```

**List of tests**

| Path                     | Testing framework | Description                                                                                      |
|--------------------------|-------------------|------------------------------------------------------------------------------------------------|
| `tests/AdminTest.php`      | Nette Tester      | Checks registration, login, user access, creating and deleting articles, reading articles      |
| `tests/AuthorTest.php`     | Nette Tester      | Checks registration, login, prevents access to users, creating and deleting own articles, reading articles |
| `tests/ReaderTest.php`     | Nette Tester      | Checks registration, login, prevents access to users, prevents article creation, reading articles |
| `tests/BadRequestTest.php` | Nette Tester      | Checks 404 response for non-existing URLs                                                      |

## 📚 API Endpoints with Request Body Fields

### Authentication

| Method | Endpoint         | Description                             | Request Body Fields             |
|--------|------------------|---------------------------------------|--------------------------------|
| POST   | `/auth/register` | Register a new user                    | `email`, `password`, `role`, `name` |
| POST   | `/auth/login`    | Login and receive authorization token | `email`, `password`             |

---

### User Management (admin only)

| Method | Endpoint         | Description                           | Request Body Fields                                   |
|--------|------------------|-------------------------------------|------------------------------------------------------|
| GET    | `/users`         | List all users                      | —                                                    |
| GET    | `/users/{id}`    | Get data for a specific user        | —                                                    |
| POST   | `/users`         | Create a new user                   | `email`, `password`, `role`, `name`                   |
| PUT    | `/users/{id}`    | Update user data                   | `email` (optional), `name` (optional), `password` (optional), `role` (optional) |
| DELETE | `/users/{id}`    | Delete a user                      | —                                                    |

---

### Article Management

| Method | Endpoint           | Description                     | Request Body Fields            |
|--------|--------------------|--------------------------------|-------------------------------|
| GET    | `/articles`        | List all articles              | —                             |
| GET    | `/articles/{id}`   | Get article details by ID      | —                             |
| POST   | `/articles`        | Create an article              | `title`, `content`             |
| PUT    | `/articles/{id}`   | Update an article              | `title` (optional), `content` (optional) |

---

## 🐘 Example: Register a new user with PHP cURL (Docker environment)

This example demonstrates how to register a new user by sending a POST request to the `/auth/register` endpoint using PHP cURL. It assumes the app is running inside Docker as described in the setup.

```php
<?php
$ch = curl_init('http://localhost:8000/auth/register');

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'email' => 'admin@example.com',
        'password' => 'password',
        'role' => 'admin',
        'name' => 'Tester',
    ],
]);

$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code !== 201) {
    die("Unexpected HTTP status code: $code");
}

$data = json_decode($response, true);

if (!isset($data['id'])) {
    die("Response JSON missing 'id'");
}

$userId = (int) $data['id'];

echo "User registered with ID: $userId\n";
```

This example shows how to log in a user by sending a POST request to the `/auth/login` endpoint using PHP cURL. It assumes the app is running inside Docker as described in the setup.

```php
<?php
$ch = curl_init('http://localhost:8000/auth/login');

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'email' => 'admin@example.com',
        'password' => 'password',
    ],
]);

$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code !== 200) {
    die("Unexpected HTTP status code: $code");
}

$data = json_decode($response, true);

if (!isset($data['token'])) {
    die("Response JSON missing 'token'");
}

$token = $data['token'];

echo "User logged in, token: $token\n";
```

This example shows how to fetch the list of articles by sending a GET request to `/articles/` with a Bearer token authorization. It assumes the app is running inside Docker as described in the setup.

```php
<?php
$auth = 'Authorization: Bearer ' . $token; // Auth token

$ch = curl_init('http://localhost:8000/articles/');

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        $auth,
    ],
]);

$response = curl_exec($ch); // JSON with list of articles
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code !== 200) {
    die("Unexpected HTTP status code: $code");
}

echo "Articles fetched successfully.\n";
```

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

