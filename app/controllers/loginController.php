<?php

namespace app\controllers;

use app\models\mainModel;


class loginController extends mainModel
{
    // LOGIN CONTROLLER TO SING IN
    public function singInController(){
        // CATCHING UP THE DATA FROM LOGIN
        $loginUser = $this->cleanRequest($_POST['loginUser']);
        $loginPassword = $this->cleanRequest($_POST['loginPassword']);

        if ($loginUser == "" || $loginPassword == "") {
            echo "
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Algunos campos están vacios',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#3085d6'});
                </script>";
        } else {
            if ($this->verifyData("[a-zA-Z0-9]{4,20}", $loginUser)) {
                echo "
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Formato de Usuario no Permitido',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#3085d6'});
                </script>
                ";
            } else {
                if ($this->verifyData("[a-zA-Z0-9$@.-]{7,50}", $loginPassword)) {
                    echo "
                    <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Formato de Contraseña no Permitido',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#3085d6'});
                    </script>
                    ";
                } else {
                    $checkUser = $this->dbRequestExecute("SELECT * FROM users WHERE user_userName = '$loginUser'");
                    if ($checkUser->rowCount() == 1) {
                        $checkUser = $checkUser->fetch();
                        if ($checkUser['user_userName'] == $loginUser && $checkUser['user_Password'] == $loginPassword) {
                            $_SESSION['ID'] = $checkUser['user_ID'];
                            $_SESSION['firstName'] = $checkUser['user_FirstName'];
                            $_SESSION['lastName'] = $checkUser['user_LastName'];
                            $_SESSION['userName'] = $checkUser['user_userName'];
                            $_SESSION['password'] = $checkUser['user_Password'];

                            if (headers_sent()) {
                                echo "
                                <script> window.location.href='".APPURL."home/'; </script>
                                ";
                            }else {
                                header("Location: ".APPURL."home/");
                            }
                        } else {
                            echo "
                            <script>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Usuario o Contraseña Incorrecto',
                                    confirmButtonText: 'Aceptar',
                                    confirmButtonColor: '#3085d6'});
                            </script>
                            ";
                        }
                    } else {
                        echo "
                    <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Usuario o Contraseña Incorrecto',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#3085d6'});
                    </script>
                        ";
                    }
                }
            }
        }
    }

    // LOGIN CONTROLLER TO SING OUT
    public function singOutController(){

        session_destroy();

        if (headers_sent()) {
            echo "
            <script> window.location.href='".APPURL."login/'; </script>
            ";
        }else {
            header("Location: ".APPURL."login/");
        }
    }


}
