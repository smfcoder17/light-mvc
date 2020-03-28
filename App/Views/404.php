<?php 
use App\App;

$headContent = "<link rel='stylesheet' href='css/404.css'>";
App::getWebPage()->setTitle("Oups - Page Not Found")
                 ->setCharset("UTF-8")
                 ->setAdditionalHeadContent($headContent);
?>

<div class="container">
     <h1 class="display-1 text-info">Error 404</h1>
     <h2 class="display-4 text-warning">Page Not Found</h2>
</div>
