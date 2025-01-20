<?php

namespace app\controllers;

use app\models\mainModel;

class userController extends mainModel
{

    // CONTROLLER TO ADD USERS
    public function addUserController()
    {

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
        $checkUser = $this->dbRequestExecute("SELECT user_userName FROM users WHERE user_userName = '$userName'");
        if ($checkUser->rowCount() > 0) {
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
                "db_ValueName" => ":FirstName",
                "db_realValue" => $firstName
            ],
            [
                "db_FieldName" => "user_LastName",
                "db_ValueName" => ":LastName",
                "db_realValue" => $lastName
            ],
            [
                "db_FieldName" => "user_userName",
                "db_ValueName" => ":User",
                "db_realValue" => $userName
            ],
            [
                "db_FieldName" => "user_Password",
                "db_ValueName" => ":Password",
                "db_realValue" => $userPassword
            ],
        ];

        $addUsers = $this->saveData("users", $userRegisterData);
        if ($addUsers->rowCount() >= 1) {
            $alert = [
                "type" => "clean",
                "icon" => "success",
                "title" => "¡Operación Realizada!",
                "text" => "Usuario " . $firstName . " " . $lastName . " creado exitosamente",
            ];
        } else {
            $alert = [
                "type" => "simple",
                "icon" => "error",
                "title" => "¡Error!",
                "text" => "¡El usuario no pudo ser registrado!",
            ];
        }
        return json_encode($alert);
    }

    // USER LIST CONTROLLER
    public function userTableListController($page, $register, $url, $search)
    {
        $page = $this->cleanRequest($page);
        $register = $this->cleanRequest($register);

        $url = $this->cleanRequest($url);
        $url = APPURL . $url . "/";

        $search = $this->cleanRequest($search);
        $table = "";

        $page = (isset($page) && $page > 0) ? (int) $page : 1;
        $start = ($page > 0) ? (($page * $register) - $register) : 0;

        if (isset($search) && $search != "") {

            $dataRequest = "SELECT * FROM users WHERE ((user_ID!='" . $_SESSION['ID'] . "' AND user_ID!='1') AND (user_FirstName LIKE '%$search%' OR user_LastName LIKE '%$search%' OR usuario_usuario LIKE '%$search%')) ORDER BY user_FirstName ASC LIMIT $start, $register";;

            $total_dataRequest = "SELECT COUNT(user_ID) FROM usuario WHERE ((user_ID!='" . $_SESSION['ID'] . "' AND user_ID!='1') AND (user_FirstName LIKE '%$$search%' OR user_LastName LIKE '%$$search%' OR user_userName LIKE '%$$search%'))";
        } else {

            $dataRequest = "SELECT * FROM users WHERE user_ID!='" . $_SESSION['ID'] . "' AND user_ID!='1' ORDER BY user_FirstName ASC LIMIT $start,$register";

            $total_dataRequest = "SELECT COUNT(user_ID) FROM users WHERE user_ID != '" . $_SESSION['ID'] . "' AND user_ID != '1'";
        }

        $dataArray = $this->dbRequestExecute($dataRequest);
        $dataArray = $dataArray->fetchAll();

        $totalArray = $this->dbRequestExecute($total_dataRequest);
        $totalArray = (int) $totalArray->fetchColumn();

        $numPages = ceil($totalArray / $register);

        $table .= '<div class="relative overflow-x-auto shadow-md sm:rounded-lg mb-3">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                            <thead class="text-base text-white uppercase bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        #
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Nombre y Apellido
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Usuario
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Contraseña
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        <span class="sr-only">Edit</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>';

        if ($totalArray >= 1 && $page <= $numPages) {
            $pageCounter = $start + 1;
            $startPage = $start + 1;
            foreach ($dataArray as $rows) {
                $table .= '
                    <tr class="bg-white border-b hover:bg-gray-200">
                        <td class="px-6 py-4">
                            '.$pageCounter.'
                        </td>
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900">
                            '.$rows['user_FirstName'].' '.$rows['user_LastName'].' 
                        </th>
                        <td class="px-6 py-4">
                            '.$rows['user_userName'].'
                        </td>
                        <td class="px-6 py-4">
                            '.$rows['user_Password'].'
                        </td>
                        <td class="px-6 py-4 text-center">
                            
                            <a href="" class="text-white bg-gray-700 hover:bg-grey-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-full text-base p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            
                            <a href="'.APPURL.'userUpdate/'.$rows['user_ID'].'" class="bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-full text-base p-2.5 text-center inline-flex items-center me-2">
                                <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M11.32 6.176H5c-1.105 0-2 .949-2 2.118v10.588C3 20.052 3.895 21 5 21h11c1.105 0 2-.948 2-2.118v-7.75l-3.914 4.144A2.46 2.46 0 0 1 12.81 16l-2.681.568c-1.75.37-3.292-1.263-2.942-3.115l.536-2.839c.097-.512.335-.983.684-1.352l2.914-3.086Z" clip-rule="evenodd" />
                                    <path fill-rule="evenodd" d="M19.846 4.318a2.148 2.148 0 0 0-.437-.692 2.014 2.014 0 0 0-.654-.463 1.92 1.92 0 0 0-1.544 0 2.014 2.014 0 0 0-.654.463l-.546.578 2.852 3.02.546-.579a2.14 2.14 0 0 0 .437-.692 2.244 2.244 0 0 0 0-1.635ZM17.45 8.721 14.597 5.7 9.82 10.76a.54.54 0 0 0-.137.27l-.536 2.84c-.07.37.239.696.588.622l2.682-.567a.492.492 0 0 0 .255-.145l4.778-5.06Z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            
                            
                            <form class="AjaxForm" action="'.APPURL.'app/ajax/userAjax.php" method="POST">

                                <input type="hidden" name="userModule" id="userModule" value="deleteUser">

                                <input type="hidden" name="user_ID" id="user_ID" value="'.$rows['user_ID'].'">

                                <button type="submit" href="" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-full text-base p-2.5 text-center inline-flex items-center me-2">
                                    <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M8.586 2.586A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4a2 2 0 0 1 .586-1.414ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                ';
                $pageCounter++;
            }
            $finalPage = $pageCounter - 1;
        } else {
            if ($totalArray >= 1) {
                $table .= '
                <tr class="bg-white border-b hover:bg-gray-200" >
			        <td class= "px-6 py-4">
			            <a href="'.$url.'1/" class="button is-link is-rounded is-small mt-4 mb-4">
			                Haga clic acá para recargar el listado
			            </a>
			        </td>
			    </tr>
                ';
            } else {
                $table .= '
                <tr class="bg-white border-b hover:bg-gray-200 justify-center">
			        <td class= "px-6 py-4" colspan="5">
			            No hay registros en el sistema
			        </td>
			    </tr>
                ';
            }   
        }
        
        $table .= '</tbody></table></div>';

        if ($totalArray >= 1 && $page <= $numPages) {
            $table .= '
            <div class="flex justify-end items-center">
                <p>Mostrando <strong>'.$startPage.'</strong> registros de Usuarios al '.$finalPage.', con un total de <strong>.'.$totalArray.'.</strong> registros</p>
            </div>
            ';

            $table = $this -> paginationData($page, $numPages, $url, 5);

        }
        return $table;
    }
}
