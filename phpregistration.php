<?php
                $conn = new mysqli('localhost', 'root', '', 'myownchat');

                if(isset($_POST['name']) && isset($_POST['password'])){
                    if(new_user($_POST['name'], $conn)){
                        if(checkName($_POST['name'], $conn) && checkPassword($_POST['password'], $conn)){
                                add_user($_POST['name'], $_POST['password'], $conn);
                                echo "Ура, вы зарегистрированы!";
                            }else{
                                echo "Ваш логин и/или пароль не соответствует нашим требованиям!";
                            }
                    }else{
                        echo "<div class = 'error'>Имя пользователя уже занято!</div><br>";
                    }
                }else{
                    echo "Добро пожаловать! Пожалуйста, ознакомьтесь с правилами и подтвердите своё согласие с каждым из них";
                }

                function new_user($name, $conn){//Проверяет, действительно ли пользователь новый
                    $query = "SELECT * FROM users WHERE login = '$name'";
                    $result = $conn -> query($query);
                    if($result -> num_rows == 0)
                        return true;
                    return false;
                }

                function add_user($login, $password, $conn){//Регистрирует нового пользователя
                    $password = salt($password);
                    $query = "INSERT INTO users VALUES('$login', '$password', 'member')";
                    $result = $conn -> query($query);
                }

                function salt($text){
                    $salt1 = "%$#$^&343t$#";
                    $salt2 = "rgrg#%#";
                    $token = hash('ripemd128', "$salt1$text$salt2");
                    return $token;
                }

                function checkName($text, $conn){
                    if($text == "")
                        return false;

                    $query = "SELECT * FROM rudewords WHERE word = '$text'";
                    $result = $conn -> query($query);
                    if($result -> num_rows != 0)
                        return false;

                    if(strpos($text, '|') != false)
                        return false;

                    if(strlen($text) > 20)
                        return false;

                    return true;
                }

                function checkPassword($text){
                    if((strlen($text) < 6) || (strlen($text) > 20))
                        return false;
                    return true;
                }

        ?>