<?php
namespace Core;

class View
{
    static protected $viewsFolder = ROOT . "/App/Views";

    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = View::$viewsFolder .'/'. $view;

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }

    /**
     * Render a view template using Twig and display it.
     * @param string $template the template file
     * @param array $args Associative array of data passed to the view (optional)
     */
    public static function renderTemplate($template, $args = [])
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader(View::$viewsFolder);
            $twig = new \Twig\Environment($loader);
        }

        echo $twig->render($template, $args);
    }

    /**
     * Render a view template using Twig and return it
     * @param string $template the template file
     * @param array $args Associative array of data passed to the view (optional)
     * @return string the rendered template
     */
    public static function getRenderTemplate($template, $args = [])
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader(View::$viewsFolder);
            $twig = new \Twig\Environment($loader);
        }

        return $twig->render($template, $args);
    }
}