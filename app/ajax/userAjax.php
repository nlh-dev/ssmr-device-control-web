<?php
    require_once "../../config/app.php";
    require_once "../views/includes/sessions_start.php";
    require_once "../../autoload.php";

    use app\controllers\userController;

    if (isset($_POST['userModule'])) {
        
        $instanceUser = new userController();

        if ($_POST['userModule'] == "addUser") {
            echo $instanceUser->addUserController();
        }
        
    } else {
        session_destroy();
        header("Location: " . APPURL . "login/");
    }
