<?php
    require_once "functions.php";
    require_once "checkauth.php"; //Если пользователь без регистрации пройдёт по ссылке, то он увидит общий чат в сети

    $chat_history = "";
    $conn = new mysqli('localhost', 'root', '', 'myownchat');
    if($conn -> connect_error)
        die($conn -> connect_error);
    else{
        if(isset($_POST['txt'])){
            if(checkText($_POST['txt'], $conn)){
            //Проверка текста на допустимость
            $txt = security($_POST['txt'], $conn);
            $name = $_SESSION['login'];
            //Тут мы реагируем на то, что через форму было отправлено сообщение
            //Добавляем его в нашу базу и увеличиваем счётчик количества сообщений
            //В этот момент всё, что введено в поля, тщательно проверено

            $query = "SELECT * FROM users WHERE login = '$name'";
            $result = $conn -> query($query);
            $result -> data_seek(0);
            $row = $result -> fetch_array(MYSQLI_ASSOC);
            $status = $row['status'];

                    if(($status == 'member') || ($status == 'admin') || ($status == 'owner')){
                    //У заблокированных пользователей статус не пройдёт эту проверку
                        insertMessage($name, $txt, $conn, "ALL");
                        //Сохраняем сообщения в базу данных. Слеш-команды админов не печатаются в чате
                        if($status != 'member'){//Тут проверяем всякие фишки, которые может творить админ/владелец

                            if(str_starts_with($txt,'/ban')){ //Админ может забанить пользователя чата
                               $res = explode('|', $txt); //Тут проблемка: в 1:09 $h считалось равным 0 (без добавки res)
                               if(isset($res[1]))
                                    if(!isadmin($res[1], $conn)){//Админ не может заблокировать другого админа/владельца
                                        if(isset($res[2])){//Блокировка на часы
                                            $date_time_array = getdate(time());
                                            $m = $date_time_array['mon'];
                                            $d = $date_time_array['mday'];
                                            $h = $date_time_array['hours'] + $res[2];

                                            //Тотал = сколько часов прошло с начала месяца
                                            $total = ($d - 1) * 24 + $h;

                                            $status = $m . $total;

                                            $query = "UPDATE users SET status = '$status' WHERE login = '$res[1]'";
                                            $result = $conn -> query($query);

                                            insertMessage('[admin]', "$res[1], Вы забанены на $res[2] часов!", $conn, "ALL");
                                        }else{//Бессрочная блокировка
                                            $query = "UPDATE users SET status = 'banned' WHERE login = '$res[1]'";
                                            $result = $conn -> query($query);
                                            insertMessage('[admin]', "$res[1], Вы забанены!", $conn, "ALL");
                                        }
                                    }
                               else{
                                    echo "<div class = 'warning'>Введите команду в формате /ban |name|(time)</div>";
                               }
                            }

                            if(str_starts_with($txt, '/unban')){ //Админ может снять блокировку
                                $res = explode('|', $txt);
                                if(!isadmin($res[1], $conn)){
                                    $query = "UPDATE users SET status = 'member' WHERE login = '$res[1]'";
                                    $result = $conn -> query($query);
                                }
                            }

                            if($status == 'owner'){
                            //Владелец чата может дать права или отнять их. Остальные команды админа доступны
                                if(str_starts_with($txt, '/giveadmin')){
                                    $res = explode('|', $txt);
                                    $query = "UPDATE users SET status = 'admin' WHERE login = '$res[1]'";
                                    $result = $conn -> query($query);
                                }
                                if(str_starts_with($txt, '/deleteadmin')){
                                    $res = explode('|', $txt);
                                    $query = "UPDATE users SET status = 'member' WHERE login = '$res[1]'";
                                    $result = $conn -> query($query);
                                }
                            }

                            if($txt == '/deletechat'){
                                $query = "DELETE FROM messages WHERE access = 'ALL'";
                                $conn -> query($query);

                                $query = "INSERT INTO messages VALUES('count', '0', '-1', 'ALL')";
                                $result = $conn -> query($query);

                                //Возвращаем правильную нумерацию сообщениям
                                $query = "SELECT * FROM messages";
                                $result = $conn -> query($query);
                                $num_msg = $result -> num_rows - 1;

                                for($i = 0; $i < $num_msg; ++$i){ //+1 так как будет найдено техническое сообщение (с id = -1)
                                    $result -> data_seek($i);
                                    $row = $result -> fetch_array(MYSQLI_ASSOC);
                                    $id = $row['id'];
                                    if($id != $i){
                                        $query = "UPDATE messages SET id = '$i' WHERE id = '$id'";
                                        $conn -> query($query);
                                    }
                                }

                                //Возвращаем правильное количество сообщений
                                $query = "UPDATE messages SET message = '$num_msg' WHERE id = '-1'";
                                $conn -> query($query);

                                echo "<div class = 'warning'>Вся история вашей переписки удалена!</div><br>";

                            }

                            if(str_starts_with($txt, '/deletelastmessage')){
                                //Удаляет последнее сообщение, сравнивая статусы
                                $count = countMessages($conn) - 1;

                                $query = "SELECT * FROM messages WHERE id = '$count'";
                                $result = $conn -> query($query);
                                $result -> data_seek(0);
                                $row0 = $result -> fetch_array(MYSQLI_ASSOC);
                                $name = $row0['name'];//Узнали, как зовут того, чьё сообщение мы пытаемся удалить

                                $query = "SELECT * FROM users WHERE login = '$name'";
                                $result = $conn -> query($query);
                                $row = $result -> fetch_array(MYSQLI_ASSOC);


                                if(($status == 'owner') || (($status == 'admin') && ($row['status'] == 'member'))){
                                    $query = "UPDATE messages SET message = '$count' WHERE id = '-1'";
                                    $result = $conn -> query($query);

                                    $query = "DELETE FROM messages WHERE id = '$count'";
                                    $result = $conn -> query($query);
                                }else{
                                    echo "<div class = 'warning'>У вас недостаточно прав для этого действия!</div>";
                                }
                            }

                            if(str_starts_with($txt, '/deletemessage')){
                                //Удаляет сообщение по его id, который можно узнать в базе данных
                                //Принимается формат только /deletemessage|id. Иначе команда не делает ничего
                                //Тоже смотрим на статусы
                                $row = explode('|', $txt);

                                if(!isset($row[1]))
                                    echo "<div class = 'warning'>Введите команду в формате /deletemessage | id</div>";
                                else{
                                    $count = countMessages($conn) - 1;
                                    $id = (int)($row[1]);

                                    $query = "SELECT * FROM messages WHERE id = '$id'";
                                    $result = $conn -> query($query);
                                    $result -> data_seek(0);
                                    $row0 = $result -> fetch_array(MYSQLI_ASSOC);
                                    $name = $row0['name'];//Узнали, как зовут того, чьё сообщение мы пытаемся удалить

                                    $query = "SELECT * FROM users WHERE login = '$name'";
                                    $result = $conn -> query($query);
                                    $row = $result -> fetch_array(MYSQLI_ASSOC);

                                    if(($status == 'owner') || (($status == 'admin') && ($row['status'] == 'member'))){
                                        $query = "UPDATE messages SET message = '$count' WHERE id = '-1'";
                                        $result = $conn -> query($query);

                                        $query = "DELETE FROM messages WHERE id = '$id'";
                                        $result = $conn -> query($query);

                                        //Обновляем нумерацию id
                                        for($i = $id + 1; $i < $count + 1; ++$i){
                                            $a = $i - 1;
                                            $query = "UPDATE messages SET id = '$a' WHERE id = '$i'";
                                            $result = $conn -> query($query);
                                        }
                                    }else{
                                        echo "<div class = 'warning'>У вас недостаточно прав для этого действия!</div>";
                                    }
                                }
                            }

                            if(str_starts_with($txt, '/colorname')){
                                //Меняет цвет имён. Статусы не проверяются
                                $row = explode('|', $txt);

                                if((!isset($row[1])) || (!isset($row[2]))){
                                    echo "<div class = 'warning'>Введите команду в формате /colorname |name|??????</div>";
                                }else{
                                    $name = $row[1];
                                    $color = $row[2];

                                    $query = "SELECT * FROM colorname WHERE name = '$name'";
                                    $result = $conn -> query($query);

                                    if($result -> num_rows == 0){
                                        $query = "INSERT INTO colorname VALUES('$name', '$color')";
                                        $result = $conn -> query($query);
                                    }else{
                                        $query = "UPDATE colorname SET color = '$color' WHERE name = '$name'";
                                        $result = $conn -> query($query);
                                    }
                                }
                            }

                            if(str_starts_with($txt, '/colortext')){
                                //Меняет цвет текста. Статусы не проверяются
                                $row = explode('|', $txt);

                                if((!isset($row[1])) || (!isset($row[2]))){
                                    echo "<div class = 'warning'>Введите команду в формате /colortext |name|??????</div>";
                                }else{
                                    $name = $row[1];
                                    $color = $row[2];

                                    $query = "SELECT * FROM colortext WHERE name = '$name'";
                                    $result = $conn -> query($query);

                                    if($result -> num_rows == 0){
                                        $query = "INSERT INTO colortext VALUES('$name', '$color')";
                                        $result = $conn -> query($query);
                                    }else{
                                        $query = "UPDATE colortext SET color = '$color' WHERE name = '$name'";
                                        $result = $conn -> query($query);
                                    }
                                }
                            }
                        }
                    }
                    else{
                        if($status == 'banned')
                            echo "<div class = 'warning'>$name, ваш аккаунт заблокирован!</div><br>";
                        else{
                            if(banned($name, $status, $conn)){
                                $t = ban($status);
                                echo "<div class = 'warning'>$name, ваш аккаунт будет разблокирован $t!</div>";
                            }else
                                insertMessage($name, $txt, $conn, "ALL");
                        }
                    }
                  }

            }

            //Собираем историю чата в одну переменную
            $count = countMessages($conn);

            for($i = 0; $i < $count; ++$i){
                $query = "SELECT * FROM messages WHERE id = '$i' AND access = 'ALL'"; //Это общий чат, поэтому из базы данных отобразится не всё
                $result = $conn -> query($query);

                if(!$result)
                    die($conn -> error);
                else{
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
            }

        }

        //Печатаем переписку
        if($chat_history != '')
            echo <<< _END
                <div id = 'hist'>
                    $chat_history
                </div>
            _END;
        else
            echo <<< _END
                    <div id = 'hist'>
                        На данный момент в чате нет сообщений. Вы можете быть первым!
                    </div>
                _END;
?>