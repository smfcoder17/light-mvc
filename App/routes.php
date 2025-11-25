<?php

declare(strict_types=1);

use Core\Router;

return function (Router $router): void {
    $routes = $router->getRoutes();

    // Home routes
    $routes->get('/', 'Home', 'index')->name('home');
    $routes->post('/contact', 'Home', 'contact')
        ->name('contact')
        ->middleware('csrf');

    // Admin routes with authentication
    $routes->group(['prefix' => 'admin', 'middleware' => 'auth'], function ($routes) {
        $routes->get('/', 'Admin\\Dashboard', 'index')->name('admin.dashboard');
        $routes->get('/test', 'Admin\\Users', 'test')->name('admin.test');
    });

    // Example: User management routes with dynamic parameters
    // $routes->get('/user/{id}', 'User', 'show')->name('user.show');
    // $routes->get('/user/{id}/edit', 'User', 'edit')
    //     ->name('user.edit')
    //     ->middleware('auth');
    
    // Example: API routes with rate limiting
    // $routes->group(['prefix' => 'api', 'middleware' => 'rate_limit'], function ($routes) {
    //     $routes->get('/posts', 'Api\\Posts', 'index')->name('api.posts');
    //     $routes->get('/posts/{id}', 'Api\\Posts', 'show')->name('api.posts.show');
    // });
};
