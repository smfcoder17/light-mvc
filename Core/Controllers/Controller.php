<?php

namespace Core\Controllers;

class Controller{

     protected $viewPath;
     protected $template;

     public function render($view)
     {
          ob_start();
          require( $this->viewPath . str_replace('.', '/', $view) . '.php');
          $content = ob_get_clean();
          require($this->viewPath . $this->template . '.php');
     }
}