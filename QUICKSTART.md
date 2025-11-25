# Quick Start Guide - New Routing System

## Installation

1. **Run setup script:**

    ```bash
    # Windows
    setup.bat

    # Linux/Mac
    chmod +x setup.sh
    ./setup.sh
    ```

2. **Or manually:**
    ```bash
    composer install
    composer dump-autoload
    cp .env.example .env
    # Edit .env with your configuration
    ```

## Your First Route

### 1. Define Route in `App/routes.php`

```php
<?php

declare(strict_types=1);

use Core\Router;

return function (Router $router): void {
    $routes = $router->getRoutes();

    // Simple route
    $routes->get('/', 'Home', 'index')->name('home');

    // Route with parameter
    $routes->get('/hello/{name}', 'Home', 'hello')->name('hello');
};
```

### 2. Create Controller Method

```php
<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;

class Home extends Controller
{
    public function indexAction(): void
    {
        echo "Welcome to Light-MVC!";
    }

    public function helloAction(): void
    {
        $name = $this->routeParams['name'] ?? 'Guest';
        echo "Hello, {$name}!";
    }
}
```

### 3. Test Your Routes

-   Visit: `http://localhost/` → "Welcome to Light-MVC!"
-   Visit: `http://localhost/hello/John` → "Hello, John!"

## Common Patterns

### Protected Route (Auth Required)

```php
$routes->get('/dashboard', 'Dashboard', 'index')
    ->name('dashboard')
    ->middleware('auth');
```

### Form with CSRF Protection

```php
// Route
$routes->post('/contact', 'Home', 'contact')
    ->middleware('csrf');

// Form
<form method="POST" action="<?= route('contact') ?>">
    <?= csrf_field() ?>
    <input type="text" name="message">
    <button>Send</button>
</form>
```

### RESTful API

```php
$routes->group(['prefix' => 'api', 'middleware' => 'rate_limit'], function ($routes) {
    $routes->get('/posts', 'Api\\Posts', 'index');
    $routes->get('/posts/{id}', 'Api\\Posts', 'show');
    $routes->post('/posts', 'Api\\Posts', 'store')->middleware('auth');
});
```

## What Changed?

### Old Way ❌

```php
return [
    '/' => ['controller' => 'Home', 'action' => 'index']
];
```

### New Way ✅

```php
return function (Router $router): void {
    $routes = $router->getRoutes();
    $routes->get('/', 'Home', 'index')->name('home');
};
```

## Key Features

✅ HTTP method support (GET, POST, PUT, DELETE)  
✅ Dynamic parameters: `/user/{id}`  
✅ Named routes: `route('user.show', ['id' => 123])`  
✅ Middleware: `auth`, `csrf`, `rate_limit`  
✅ Route grouping: prefix + middleware  
✅ Helper functions: `csrf_field()`, `route()`, `redirect()`

## Need Help?

See `ROUTING_GUIDE.md` for full documentation.
