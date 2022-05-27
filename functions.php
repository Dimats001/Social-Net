<?php
    //Тут лежат вообще все функции. И для работы общего чата, и для бесед, и для личных сообщений
    function countMessages($conn){//Выдаёт количество сообщений в истории чата
                $query = "SELECT * FROM messages WHERE id = '-1'";
                $result = $conn -> query($query);
                $result -> data_seek(0);
                $arr = $result -> fetch_array(MYSQLI_ASSOC);
                $count = $arr['message'];
                return $count;
    }

    function insertMessage($name, $text, $conn, $access){//Сохраняет введённое сообщение в базе данных
        $count = countMessages($conn);

        if(($text != '/deletechat') && ($text != '/deletelastmessage') && (!str_starts_with($text, '/deletemessage'))
        && (!str_starts_with($text, '/color'))){ //Команды в чате не сохраняем, кроме /ban
             $query = "INSERT INTO messages VALUES('" . $name . "', '" . $text . "', '$count', '$access')";
             $result = $conn -> query($query);

            $count += 1;

            $query = "UPDATE messages SET message = '$count' WHERE id = '-1'";
            $result = $conn -> query($query);

            if($access != 'ALL'){//Чтобы пользователь при входе во вкладку "Личные сообщения" видел, с кем он общался,
                                 //мы добавляем пару 'отправитель - адресат' в таблицу dialogs

                $dialogwith = $_POST['dialogwith'];
                $login = $_SESSION['login'];

                $exist = 0;

                $query = "SELECT * FROM dialogs WHERE name1 = '$dialogwith' AND name2 = '$login'";
                $result = $conn -> query($query);
                $exist += $result -> num_rows;

                $query = "SELECT * FROM dialogs WHERE name1 = '$login' AND name2 = '$dialogwith'";
                $result = $conn -> query($query);
                $exist += $result -> num_rows;

                if($exist == 0){ //Ранее данные о существовании переписки между 2 пользователями не были внесены в таблицу
                    $query = "INSERT INTO dialogs VALUES('$dialogwith', '$login')";
                    $result = $conn -> query($query);
                }
            }
        }
    }

    function checkText($text, $conn){
        if($text == ""){
            echo "<div class = 'warning'>Ни одно из полей не может быть пустым!</div>";
            return false;
        }

        $query = "SELECT * FROM rudewords WHERE word = '$text'";
        $result = $conn -> query($query);
        if($result -> num_rows != 0){
            echo "<div class = 'warning'>Мат в тексте запрещён!</div>";
            return false;
        }

        return true;
    }

    function auth($login, $password, $conn){ //Всевозможные проверки пароля и логина на адекватность и правильность
                        if(($login === '') || ($password === '')){
                            echo "<div class = 'warning' style = 'background: #aaa;'>Ни одно из полей не может быть пустым!</div>";
                            return false;
                        }

                        //Строки могут состоять из одних пробелов, поэтому проверки выше недостаточно!
                        if((isspace($login)) || (isspace($password)))
                            return false;

                        $query = "SELECT * FROM users WHERE login = '$login'";
                        $result = $conn -> query($query);

                        if($result -> num_rows == 0){
                            echo "<div class = 'warning' style = 'background: #aaa;'>Вы не зарегистрированы!</div>";
                            return false;
                        }else{
                            $row = $result -> fetch_array(MYSQLI_ASSOC);
                            if($row['password'] !== salt($password)){
                                echo "<div class = 'warning' style = 'background: #aaa;'>Неверная комбинация 'Логин - пароль'!</div>";
                                return false;
                            }
                        }

                        return true;
    }

    function salt($text){
        $salt1 = "%$#$^&343t$#";
        $salt2 = "rgrg#%#";
        $token = hash('ripemd128', "$salt1$text$salt2");
        return $token;
    }

    function ban($time){
    //05024 - будет означать, что блокировку нужно снять через 24 часа с начала мая (те 2 мая в 00:00)
        $month = ((int)($time / 1000));
        $day = (int)(($time - 1000 * $month) / 24) + 1;
        $hour = $time - 1000 * $month - ($day - 1) * 24;
        return "$day.$month.22 в $hour:00";
    }

    function banned($name, $status, $conn){
        //По статусу аккаунта определяет, актуальна ли блокировка.
        //Если нет, снимает её

        $month = ((int)($status / 1000));
        $day = (int)(($status - 1000 * $month) / 24) + 1;
        $hour = $status - 1000 * $month - ($day - 1) * 24;

        $date_time_array = getdate(time());

        if($month > $date_time_array['mon']){
            return true;
            }
        else
            if($day > $date_time_array['mday'])
                return true;
            else
                if($hour > $date_time_array['hours'])
                    return true;

        $query = "UPDATE users SET status = 'member' WHERE login = '$name'";
        $result = $conn -> query($query);

        return false;
    }

    function isadmin($name, $conn){//Проверка на то, админ ли это (владелец тоже считается)
        $query = "SELECT * FROM users WHERE login = '$name'";
        $result = $conn -> query($query);
        $result -> data_seek(0);
        $row = $result -> fetch_array(MYSQLI_ASSOC);
        if($row['status'] == 'admin')
            return true;
        if($row['status'] == 'owner')
            return true;
        return false;
    }

    function coloredname($name, $conn){//Возвращает цвет имени
        $query = "SELECT * FROM colorname WHERE name = '$name'";
        $result = $conn -> query($query);

        if($result -> num_rows != 0){
            $result -> data_seek(0);
            $row = $result -> fetch_array(MYSQLI_ASSOC);
            return $row['color'];
        }else
            return "000";
    }

    function coloredtext($name, $conn){//Возвращает цвет текста
        $query = "SELECT * FROM colortext WHERE name = '$name'";
        $result = $conn -> query($query);

        if($result -> num_rows != 0){
            $result -> data_seek(0);
            $row = $result -> fetch_array(MYSQLI_ASSOC);
            return $row['color'];
        }else
            return "000";
    }

    function writeHistory($conn, $access){ //Выводит историю чата, учитывая доступ к сообщениям
                $chat_history = '';

                if(($access != 'ALL') && (str_contains($access, $_SESSION['login']))){
                    $login = $_SESSION['login'];
                    $access = str_replace($login, "", $access);

                    $access1 = $access . $_SESSION['login'];
                    $access2 = $_SESSION['login'] . $access;
                }else
                    $access1 = $access2 = $access;

                $count = countMessages($conn);

                for($i = 0; $i < $count; ++$i){
                    $query = "SELECT * FROM messages WHERE id = '$i' AND access = '$access1'";
                    $result = $conn -> query($query);

                    if($result -> num_rows == 0){
                        $query = "SELECT * FROM messages WHERE id = '$i' AND access = '$access2'";
                        $result = $conn -> query($query);
                    }


                    if(!$result)
                        die($conn -> error);
                    else
                        if($result -> num_rows != 0){
                            $result -> data_seek(0);
                            $row = $result -> fetch_array(MYSQLI_ASSOC);
                            $result -> close();
                            $name = $row['name'];
                            $msg = $row['message'];
                            $colorn = coloredname($name, $conn);
                            $colort = coloredtext($name, $conn);

                            $chat_history .= "<b><font color = '#$colorn'>$name</font></b>: <font color = '#$colort'>$msg</font><br>";
                        }
                }

                return $chat_history;
    }

    function security($text, $conn){//Защита от внедрения чего-либо пользователем
        $text = mysqli_escape_string($conn, $text);
        $text = htmlspecialchars($text);
        $text = strip_tags($text);
        return $text;
    }

    function isspace($text){
                            $length = strlen($text);
                            $space = 0;
                            for($i = 0; $i < $length; ++$i){
                                if($text[$i] === ' ')
                                    ++$space;
                            }
                            if($space == $length){
                                echo "<div class = 'warning' style = 'background: #aaa;'>Ни одно из полей не может быть пустым!</div>";
                                return true;
                            }
                            return false;
    }

?>