# Light MVC Framework

![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)
![License](https://img.shields.io/badge/license-MIT-green)

**A modern, lightweight PHP MVC framework for rapid application development**

## Quick Start

```bash
# Clone the repository
git clone https://github.com/smfcoder17/light-mvc.git
cd light-mvc

# Run automated setup
setup.bat  # Windows
./setup.sh # Linux/Mac

# Start development server
php -S localhost:8000 -t public
```

Visit http://localhost:8000 - You're ready!

---

## Features

- **MVC Architecture** - Clean separation of concerns
- **Advanced Routing** - RESTful routes, dynamic parameters, named routes, middleware
- **Security** - CSRF protection, XSS prevention, SQL injection protection
- **Database Layer** - PDO with prepared statements
- **Template Engine** - Twig integration
- **Environment Config** - `.env` file support

---

## Usage Examples

### Routes (App/routes.php)

```php
<?php
use Core\Router;

return function (Router $router): void {
    $routes = $router->getRoutes();
    
    // Simple route
    $routes->get('/', 'Home', 'index')->name('home');
    
    // Route with parameter
    $routes->get('/user/{id}', 'User', 'show')->name('user.show');
    
    // Protected route with middleware
    $routes->get('/dashboard', 'Dashboard', 'index')
        ->middleware('auth');
    
    // API group with rate limiting
    $routes->group(['prefix' => 'api', 'middleware' => 'rate_limit'], function ($routes) {
        $routes->get('/posts', 'Api\\Posts', 'index');
        $routes->post('/posts', 'Api\\Posts', 'store')->middleware('csrf');
    });
};
```

### Controller (App/Controllers/User.php)

```php
<?php
namespace App\Controllers;

use Core\Controller;
use Core\View;

class User extends Controller
{
    public function showAction(): void
    {
        $id = $this->routeParams['id'];
        
        // Fetch user from database
        $user = $this->model->getUserById($id);
        
        // Render view
        View::renderTemplate('User/show.html', [
            'user' => $user
        ]);
    }
}
```

### View with CSRF (App/Views/contact.html)

```html
<form method="POST" action="<?= route('contact.send') ?>">
    <?= csrf_field() ?>
    
    <input type="email" name="email" required>
    <textarea name="message" required></textarea>
    
    <button type="submit">Send</button>
</form>
```

---

## Project Structure

```
light-mvc/
├── App/              # Your application code
│   ├── Controllers/  # Request handlers
│   ├── Models/       # Business logic & database
│   ├── Views/        # Twig templates
│   └── routes.php    # Route definitions
├── Core/             # Framework core
├── public/           # Web root
│   ├── index.php     # Entry point
│   └── assets/       # CSS, JS, images
└── .env.example      # Environment template
```

## Requirements

- PHP 8.2+
- Composer
- MySQL/MariaDB/PostgreSQL
- Apache/Nginx (or PHP built-in server)

## Configuration

Edit `.env`:

```env
DB_HOST=localhost
DB_NAME=your_database
DB_USER=your_username
DB_PASSWORD=your_password
```

## License

MIT License - see [LICENSE](LICENSE)

## Contact

- Email: contact@smfcoder.com
- GitHub: [smfcoder17/light-mvc](https://github.com/smfcoder17/light-mvc)
