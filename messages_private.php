<?php
    require_once "functions.php";

    $conn = new mysqli('localhost', 'root', '', 'myownchat');
    if($conn -> connect_error)
        die($conn -> connect_error);
    else{
        if(isset($_POST['txt']) && isset($_POST['dialogwith'])){
            $access = $_POST['dialogwith'] . $_SESSION['login'];
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


                insertMessage($name, $txt, $conn, $access);
                //Сохраняем сообщения в базу данных. Слеш-команды админов не печатаются в чате

                if($txt == '/deletechat'){
                    if($access != 'ALL'){
                        //user1user2 и user2user1 - удалить надо все сообщения с таким доступом
                        $access1 = str_replace($_SESSION['login'],"", $access) . $_SESSION['login'];
                        $access2 = $_SESSION['login'] . str_replace($_SESSION['login'], '', $access);
                    }else{
                        $access1 = $access2 = $access;
                    }

                    $deleted_rows = 0;

                    $login1 = $_SESSION['login'];
                    $login2 = str_replace($login1, '', $access);

                    $query = "DELETE FROM dialogs WHERE name1 = '$login1' AND name2 = '$login2'";
                    $result = $conn -> query($query);

                    $query = "DELETE FROM dialogs WHERE name1 = '$login2' AND name2 = '$login1'";
                    $result = $conn -> query($query);

                    $query = "SELECT * FROM messages WHERE access = '$access1'";
                    $result = $conn -> query($query);
                    $deleted_rows += $result -> num_rows;
                    $query = "DELETE FROM messages WHERE access = '$access1'";
                    $result = $conn -> query($query);

                    $query = "SELECT * FROM messages WHERE access = '$access2'";
                    $result = $conn -> query($query);
                    $deleted_rows += $result -> num_rows;
                    $query = "DELETE FROM messages WHERE access = '$access2'";
                    $result = $conn -> query($query);

                    if($access == 'ALL'){
                        $query = "INSERT INTO messages VALUES('count', '0', '-1')"; //Техническая строка, хранит количество сообщений
                        $result = $conn -> query($query);
                    }
                    //Возвращаем правильные значения количеству сообщений
                    $query = "SELECT * FROM messages WHERE id = '-1'";
                    $result = $conn -> query($query);
                    $result -> data_seek(0);
                    $row = $result -> fetch_array(MYSQLI_ASSOC);

                    $old_num_msg = $row['message']; //До удаления личного диалога всего хранилось столько сообщений (вообще любых, не только из переписки)
                    $num_msg = $row['message'] - $deleted_rows; //Число реально хранящихся сообщений

                    $query = "UPDATE messages SET message = '$num_msg' WHERE id = '-1'";
                    $result = $conn -> query($query);

                    echo "<div class = 'warning'>Вся история вашей переписки удалена!</div><br>";

                    //Возвращаем правильную нумерацию сообщениям
                    $query = "SELECT * FROM messages";
                    $result = $conn -> query($query);

                    for($i = 0; $i < $num_msg + 1; ++$i){ //+1 так как будет найдено техническое сообщение (с id = -1)
                        $result -> data_seek($i);
                        $row = $result -> fetch_array(MYSQLI_ASSOC);
                        $id = $row['id'];
                        if(($id != $i - 1) && ($i != 0)){
                            $newid = $i - 1;
                            $query = "UPDATE messages SET id = '$newid' WHERE id = '$id'";
                            $conn -> query($query);
                        }
                    }
                }



                if(str_starts_with($txt, '/colorname')){
                    //Меняет цвет имён. Статусы не проверяются
                    $row = explode('|', $txt);

                    if((!isset($row[1])) || (!isset($row[2])))
                        echo "<div class = 'warning'>Введите команду в формате /colorname |name|??????</div>";
                    else{
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

                    if((!isset($row[1])) || (!isset($row[2])))
                        echo "<div class = 'warning'>Введите команду в формате /colortext |name|??????</div>";
                    else{
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

            //Собираем историю чата в одну переменную. Если пользователь выбрал, с кем он хочет общаться. Иначе не выводим ничего в диалоге
            if(isset($_POST['dialogwith'])){

                $dialogwith = $_POST['dialogwith'];
                $access = $_POST['dialogwith'] . $_SESSION['login'];

                $chat_history = writeHistory($conn, $access);

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
                                На данный момент переписка с пользователем $dialogwith пуста. Сделайте первый шаг!
                            </div>
                    _END;
            }

        }

?>