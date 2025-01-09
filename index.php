<?php

require_once "./config/app.php";
require_once "./autoload.php";
require_once "./app/views/includes/sessions_start.php";

if (isset($_GET['views'])) {
    $url = explode("/", $_GET['views']);
} else {
    $url = ["login"];
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <!-- HEAD IMPORTS -->
    <?php require_once "./app/views/includes/head.php" ?>
</head>

<body>
    <?php

    use app\controllers\viewsController;

    $viewsController = new viewsController();
    $views = $viewsController->obtainViews($url[0]);

    if ($views == "login" || $views == "404") {
        require_once "./app/views/content/" . $views . "-view.php";
    } else {
        require_once "./app/views/layouts/sidebar.php";
        require_once $views;
    }

    require_once "./app/views/includes/script.php";
    ?>
</body>

</html>