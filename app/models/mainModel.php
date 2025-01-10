<?php

    namespace app\models;

    if (file_exists(__DIR__."/../../config/server.php")) {
        require_once __DIR__."/../../config/server.php";
    }
    
    class mainModel{

        private $Server = DBSERVER;
        private $dbName = DBNAME;
        private $dbUser = DBUSER;
        private $dbPassword = DBPASSWORD;


    }

?>