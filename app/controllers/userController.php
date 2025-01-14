<?php

    namespace app\controllers;
    use app\models\mainModel;

    class userController extends mainModel{
        
        // CONTROLLER TO ADD USERS
        public function addUserController(){
            
            // STORING THE DATA SENT BY THE FORM
            $firstName = $this->cleanRequest($_POST['firstName']);
            $lastName = $this->cleanRequest($_POST['lastName']);
            $userName = $this->cleanRequest($_POST['userName']);
            $userPassword = $this->cleanRequest($_POST['userPassword']);
            
            // VERIFYING IF THE DATA IS EMPTY
            if ($firstName == "" || $lastName == "" || $userName == "" || $userPassword == "") {
                $alert = [
                    "type" => "simple",
                    "icon" => "error",
                    "title" => "¡Error!",
                    "text" => "¡Algunos campos se encuentran vacios!",
                ];
                return json_encode($alert);
                exit();
            }

            // VERIFYING PATTERNS FROM FIRST AND LAST NAME
            if ($this->verifyData("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $firstName)) {
                $alert = [
                    "type" => "simple",
                    "icon" => "error",
                    "title" => "¡Error!",
                    "text" => "¡Formato de nombre invalido!",
                ];
                return json_encode($alert);
                exit();
            }
            
            if ($this->verifyData("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $lastName)) {
                $alert = [
                    "type" => "simple",
                    "icon" => "error",
                    "title" => "¡Error!",
                    "text" => "¡Formato de apellido invalido!",
                ];
                return json_encode($alert);
                exit();
            }

            // VERIFYING USER ON DATABASE
            $checkUser = $this ->dbRequestExecute("SELECT user_userName FROM users WHERE user_userName = '$userName'");
            if ($checkUser -> rowCount()>0) {
                $alert = [
                    "type" => "simple",
                    "icon" => "error",
                    "title" => "¡Error!",
                    "text" => "¡El usuario ingresado ya se encuentra registrado!",
                ];
                return json_encode($alert);
                exit();
            }

            // ARRY TO STORE DATA FROM FORM FIELD TO DATABASE
            $userRegisterData = [
                [
                    "db_FieldName" => "user_FirstName",
                    "db_ValueName" => ":Nombre",
                    "db_realValue" => $firstName
                ],
                [
                    "db_FieldName" => "user_LastName",
                    "db_ValueName" => ":Apellido",
                    "db_realValue" => $lastName
                ],
                [
                    "db_FieldName" => "user_userName",
                    "db_ValueName" => ":Usuario",
                    "db_realValue" => $userName
                ],
                [
                    "db_FieldName" => "user_userPassword",
                    "db_ValueName" => ":Contraseña",
                    "db_realValue" => $userPassword
                ],
            ];

            $addUsers = $this -> saveData("users", $userRegisterData);
            if ($addUsers -> rowCount() >= 1) {
                $alert = [
                    "type" => "clean",
                    "icon" => "success",
                    "title" => "¡Operación Realizada!",
                    "text" => "Usuario " .$firstName. " ".$lastName." creado exitosamente",
                ];
            }else {
                $alert = [
                    "type" => "simple",
                    "icon" => "error",
                    "title" => "¡Error!",
                    "text" => "¡El usuario no pudo ser registrado!",
                ];
            }
            return json_encode($alert);
        }
    }