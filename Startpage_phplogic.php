<?php
                    require_once "functions.php";
                    $conn = new mysqli('localhost', 'root', '', 'myownchat');
                    if($conn -> connect_error)
                        die($conn -> connect_error);
                    else{
                        if(isset($_POST['name']) && isset($_POST['password'])){
                            $login = security($_POST['name'], $conn);
                            $password = security($_POST['password'], $conn);
                            if(auth($login, $password, $conn)){
                                    session_start();
                                    $_SESSION['login'] = $login;
                                    echo <<< _END
                                        <input type = "button" value = "Добро пожаловать!" onclick = main() class = 'Button'>
                                    _END;
                                }
                        }
                    }





                ?>
