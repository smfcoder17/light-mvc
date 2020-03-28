<?php use App\App; ?>
<!DOCTYPE html>
<html lang="<?= App::getWebPage()->language ?? 'fr' ?>">
<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <meta http-equiv="X-UA-Compatible" content="ie=edge">
     <meta name="description" content="<?= App::getWebPage()->description ?? '' ?>">
     <meta name="author" content="<?= App::getWebPage()->author ?? '' ?>">
     <!-- Bootstrap 4 -->
     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
     <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
     <?= App::getWebPage()->additionalHeadContent ?? '' ?>
     <title><?= App::getWebPage()->title ?? "Default Page" ?></title>
</head>
<body>
     <?php include(VIEWS_PATH . "/layouts/header.php"); ?>
     <?= App::getWebPage()->content ?? '' ?>
     <?php include(VIEWS_PATH . "/layouts/footer.php"); ?>
</body>
</html>