# Advanced Routing System - Documentation

## 🚀 Overview

The Light-MVC framework now includes a professional routing system with support for:

- ✅ HTTP Method Support (GET, POST, PUT, DELETE, PATCH, ANY)
- ✅ Dynamic Route Parameters: `/user/{id}/edit`
- ✅ Named Routes for URL generation
- ✅ Route Grouping with prefixes
- ✅ Middleware System (Auth, CSRF, Rate Limiting)
- ✅ Custom Middleware Support

## 📖 Basic Usage

### Simple Routes

```php
// In App/routes.php
return function (Router $router): void {
    $routes = $router->getRoutes();
    
    $routes->get('/', 'Home', 'index');
    $routes->post('/contact', 'Contact', 'send');
    $routes->put('/user/{id}', 'User', 'update');
    $routes->delete('/post/{id}', 'Post', 'destroy');
};
```

### Named Routes

Named routes allow you to generate URLs programmatically:

```php
// Define route with name
$routes->get('/user/{id}', 'User', 'show')->name('user.show');

// Generate URL
$url = route('user.show', ['id' => 123]); // Returns: /user/123
```

### Dynamic Parameters

Routes can contain dynamic segments enclosed in curly braces:

```php
$routes->get('/post/{slug}/comment/{id}', 'Comment', 'show');
```

Parameters are automatically extracted and passed to your controller via `$this->routeParams`.

## 🔐 Middleware System

### Built-in Middlewares

#### 1. Authentication Middleware (`auth`)
Ensures the user is logged in (checks for `$_SESSION['user_id']`).

```php
$routes->get('/dashboard', 'Dashboard', 'index')
    ->middleware('auth');
```

#### 2. CSRF Protection (`csrf`)
Validates CSRF token for POST, PUT, and DELETE requests.

```php
$routes->post('/user/{id}', 'User', 'update')
    ->middleware('csrf');
```

In your forms:
```html
<form method="POST" action="/user/123">
    <?= csrf_field() ?>
    <!-- form fields -->
</form>
```

#### 3. Rate Limiting (`rate_limit`)
Limits requests to 60 per minute per IP address.

```php
$routes->get('/api/posts', 'Api\\Posts', 'index')
    ->middleware('rate_limit');
```

### Multiple Middlewares

Apply multiple middlewares to a single route:

```php
$routes->post('/user/{id}', 'User', 'update')
    ->middleware(['auth', 'csrf']);
```

### Custom Middleware

Create your own middleware by implementing `MiddlewareInterface`:

```php
<?php

namespace Core\Middleware;

use Core\MiddlewareInterface;

class CustomMiddleware implements MiddlewareInterface
{
    public function handle(callable $next): mixed
    {
        // Before action
        if (!someCondition()) {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
        
        $result = $next();
        
        // After action
        
        return $result;
    }
}
```

Register in `Core/Router.php`:
```php
$this->middlewareAliases['custom'] = Middleware\CustomMiddleware::class;
```

## 📂 Route Grouping

### Prefix Groups

Group routes with a common prefix:

```php
$routes->group(['prefix' => 'admin'], function ($routes) {
    // URL: /admin/users
    $routes->get('/users', 'Admin\\Users', 'index');
    
    // URL: /admin/settings
    $routes->get('/settings', 'Admin\\Settings', 'index');
});
```

### Middleware Groups

Apply middleware to all routes in a group:

```php
$routes->group(['middleware' => 'auth'], function ($routes) {
    $routes->get('/profile', 'Profile', 'show');
    $routes->post('/profile', 'Profile', 'update');
});
```

### Combined Groups

Combine prefix and middleware:

```php
$routes->group([
    'prefix' => 'api',
    'middleware' => ['auth', 'rate_limit']
], function ($routes) {
    $routes->get('/posts', 'Api\\Posts', 'index');
    $routes->post('/posts', 'Api\\Posts', 'store');
});
```

## 🎮 Controller Usage

### Accessing Route Parameters

```php
<?php

namespace App\Controllers;

use Core\Controller;
use Core\View;

class User extends Controller
{
    public function showAction(): void
    {
        $id = $this->routeParams['id'] ?? null;
        
        if ($id === null) {
            throw new \Exception('User ID required', 400);
        }
        
        // Fetch user data
        View::renderTemplate('User/show.html', ['userId' => $id]);
    }
}
```

## 🌐 HTTP Methods

All available methods:

```php
$routes->get($pattern, $controller, $action);      // GET
$routes->post($pattern, $controller, $action);     // POST
$routes->put($pattern, $controller, $action);      // PUT
$routes->delete($pattern, $controller, $action);   // DELETE
$routes->any($pattern, $controller, $action);      // All methods
$routes->match(['GET', 'POST'], $pattern, ...);   // Specific methods
```

## 🔧 Helper Functions

### `route()`
Generate URL for named route:
```php
$url = route('user.show', ['id' => 123]);
```

### `csrf_field()`
Generate hidden CSRF token input:
```php
<?= csrf_field() ?>
```

### `csrf_token()`
Get CSRF token value:
```php
$token = csrf_token();
```

### `old()`
Retrieve old input value (useful after validation errors):
```php
<input name="email" value="<?= old('email') ?>">
```

### `redirect()`
Redirect to URL:
```php
redirect('/login');
```

### `back()`
Redirect to previous page:
```php
back();
```

## 📝 Complete Example

### RESTful Resource

```php
// Routes
$routes->get('/posts', 'Post', 'index')->name('posts.index');
$routes->get('/posts/create', 'Post', 'create')->name('posts.create');
$routes->post('/posts', 'Post', 'store')
    ->name('posts.store')
    ->middleware('csrf');
    
$routes->get('/posts/{id}', 'Post', 'show')->name('posts.show');
$routes->get('/posts/{id}/edit', 'Post', 'edit')
    ->name('posts.edit')
    ->middleware('auth');
    
$routes->put('/posts/{id}', 'Post', 'update')
    ->name('posts.update')
    ->middleware(['auth', 'csrf']);
    
$routes->delete('/posts/{id}', 'Post', 'destroy')
    ->name('posts.destroy')
    ->middleware(['auth', 'csrf']);
```

### API Routes

```php
$routes->group([
    'prefix' => 'api/v1',
    'middleware' => 'rate_limit'
], function ($routes) {
    $routes->get('/users', 'Api\\Users', 'index');
    $routes->get('/users/{id}', 'Api\\Users', 'show');
    
    $routes->post('/users', 'Api\\Users', 'store')
        ->middleware('auth');
        
    $routes->put('/users/{id}', 'Api\\Users', 'update')
        ->middleware('auth');
});
```

### View Usage

```html
<!-- Generate route URLs -->
<a href="<?= route('user.show', ['id' => $user->id]) ?>">View Profile</a>

<!-- Form with CSRF protection -->
<form method="POST" action="<?= route('posts.store') ?>">
    <?= csrf_field() ?>
    
    <input type="text" name="title" value="<?= old('title') ?>">
    <textarea name="content"><?= old('content') ?></textarea>
    
    <button type="submit">Create Post</button>
</form>

<!-- Link to edit -->
<a href="<?= route('posts.edit', ['id' => $post->id]) ?>">Edit</a>
```

## 🔄 Migration from Old System

### Before (Old System)
```php
return [
    '/' => ['controller' => 'Home', 'action' => 'index'],
    '/{controller}/{action}' => []
];
```

### After (New System)
```php
return function (Router $router): void {
    $routes = $router->getRoutes();
    
    $routes->get('/', 'Home', 'index')->name('home');
    $routes->get('/{controller}/{action}', 'Dynamic', 'handle');
};
```

## 🎯 Best Practices

1. **Always name your routes** for easy URL generation
2. **Use CSRF middleware** for all state-changing operations (POST, PUT, DELETE)
3. **Group related routes** for better organization
4. **Apply authentication middleware** to protected routes
5. **Use rate limiting** for public API endpoints
6. **Keep route parameters simple** (avoid complex regex)

## 🐛 Troubleshooting

### Route not matching?
- Check HTTP method matches (GET vs POST)
- Verify parameter names match in URL pattern
- Ensure no trailing slashes unless intended

### Middleware not working?
- Verify middleware is registered in Router's `$middlewareAliases`
- Check session is started (done automatically in App.php)
- Ensure middleware is applied before route group closes

### CSRF token mismatch?
- Ensure form method is POST/PUT/DELETE
- Include `<?= csrf_field() ?>` in your form
- Check session is working properly

## 📚 Additional Resources

- Review `Core/Route.php` for route matching logic
- See `Core/RouteCollection.php` for collection methods
- Check `Core/Router.php` for dispatcher implementation
- Examine middleware examples in `Core/Middleware/`

Happy routing! 🚀
