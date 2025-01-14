<?php

    namespace app\controllers;
    use app\models\mainModel;
    
    // LOGIN CONTROLLER TO SING IN
    class loginController extends mainModel{
        public function singInController(){
            
            // CATCHING UP THE DATA FROM LOGIN
            $loginUser = $this->cleanRequest($_POST['loginUser']);
            $loginPassword = $this->cleanRequest($_POST['loginPassword']);

            if ($loginUser == "" || $loginPassword == "") {
                
            }
        }
    }

?>