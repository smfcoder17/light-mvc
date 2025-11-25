<?php

declare(strict_types=1);

namespace Core;

/**
 * View class - Template rendering engine
 * 
 * Provides methods for rendering both plain PHP views and Twig templates.
 * Supports passing data to views through associative arrays.
 * 
 * @package Core
 * @author Light-MVC
 * @version 2.0.0
 */
class View
{
    /** @var string Path to views directory */
    protected static string $viewsFolder = ROOT . "/App/Views";

    /**
     * Render a plain PHP view file
     * Extracts array data as variables available in the view
     * 
     * @param string $view View file path relative to views folder
     * @param array<string, mixed> $args Data to pass to the view
     * @return void
     * @throws \Exception If view file not found
     */
    public static function render(string $view, array $args = []): void
    {
        extract($args, EXTR_SKIP);

        $file = View::$viewsFolder . '/' . $view;

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }

    /**
     * Render a Twig template and display it
     * Uses static Twig instance for performance
     * 
     * @param string $template Template file path relative to views folder
     * @param array<string, mixed> $args Data to pass to the template
     * @return void
     */
    public static function renderTemplate(string $template, array $args = []): void
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader(View::$viewsFolder);
            $twig = new \Twig\Environment($loader);
        }

        echo $twig->render($template, $args);
    }

    /**
     * Render a Twig template and return the output
     * Uses static Twig instance for performance
     * 
     * @param string $template Template file path relative to views folder
     * @param array<string, mixed> $args Data to pass to the template
     * @return string Rendered template output
     */
    public static function getRenderTemplate(string $template, array $args = []): string
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader(View::$viewsFolder);
            $twig = new \Twig\Environment($loader);
        }

        return $twig->render($template, $args);
    }
}
