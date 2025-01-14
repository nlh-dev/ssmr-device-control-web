<?php

namespace app\controllers;

use app\models\mainModel;

// LOGIN CONTROLLER TO SING IN
class loginController extends mainModel
{
    public function singInController()
    {
        // CATCHING UP THE DATA FROM LOGIN
        $loginUser = $this->cleanRequest($_POST['loginUser']);
        $loginPassword = $this->cleanRequest($_POST['loginPassword']);

        if ($loginUser == "" || $loginPassword == "") {
            echo "
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Los campos están vacios',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#3085d6',
                    });
                </script>";
        }
    }
}
