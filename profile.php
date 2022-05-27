    <!DOCTYPE html>
    <html lang = "ru">
        <head>
            <meta charset="utf-8">
            <link href='styles.css' rel='stylesheet'>
            <script src = 'http://code.jquery.com/jquery-1.11.1.min.js'></script>
            <script src = 'functions.js'></script>
            <title>Чатик</title>
        </head>
        <body style = 'background: #eee;'>
        <?php require_once "checkauth.php";?>
            <div class = 'site' style = 'margin: 0 auto;'>

                <div id = 'menu' style = "float: left; ">
                    <input value = 'Главная' class = 'Button' onclick = 'main()' style = 'height: 16vh; border-radius: 0px; box-sizing: border-box; margin: 0rem; text-align: center;'>
                    <input value = 'Профиль' class = 'Button' onclick = 'profile()' style = 'height: 16vh; border-radius: 0px; box-sizing: border-box; margin: 0rem; text-align: center;'>
                    <input value = 'Общий чат' class = 'Button' onclick = 'chat()' style = 'height: 16vh; border-radius: 0px; box-sizing: border-box; margin: 0rem; text-align: center;'>
                    <input value = 'Личные сообщения' class = 'Button' onclick = 'chat_private()' style = 'height: 16vh; border-radius: 0px; box-sizing: border-box; margin: 0rem; text-align: center;'>
                    <input value = 'Друзья' class = 'Button' onclick = 'friends()' style = 'height: 16vh; border-radius: 0px; box-sizing: border-box; margin: 0rem; text-align: center;'>
                    <form action = 'mainpage.php' method = 'post'>
                        <input type = 'hidden' name = 'exit' value = 'yes'>
                        <input type = 'submit' value = 'Выйти' class = 'Button' onclick = 'chat()' style = 'height: 16vh; border-radius: 0px; box-sizing: border-box; margin: 0rem; text-align: center;'>
                    </form>
                </div>

                <div style = 'box-sizing: border-box; padding: 10px; float: left; margin-left: 3rem; margin-right: 3rem; background: #ccc; width: 470px; border: 1px solid black; border-radius: 3px;'>
                    <?php
                        $conn = new mysqli('localhost', 'root', '', 'myownchat');
                        if($conn -> connect_error)
                                    die($conn -> connect_error);

                        if(isset($_POST['textpost'])){
                            if(isset($_POST['title']))
                                $title = $_POST['title'];
                            else
                                $title = '';

                            if(isset($_POST['profile']))
                                $login = $_POST['profile'];
                            else
                                $login = $_SESSION['login'];

                            $text =  $_POST['textpost'];
                            if($text == '')
                                echo "<div class = 'warning' style = 'background: #aaa;'>Пустые сообщения запрещены!</div>";

                            $time = getdate();
                            $day = $time['mday'];
                            $month = $time['mon'];
                            $year = $time['year'];
                            $data = "$day.$month.$year";

                            $hour = $time['hours'] + 1;//Почему-то здесь выходит время на час раньше
                            $min = $time['minutes'];
                            if($min < 10)
                                $min = '0' . $min;
                            $curr_time = "$hour:$min";

                            //Не позволяем пользователю публиковать больше одного поста в минуту. Такое 'индексирование'
                            $query = "SELECT * FROM posts WHERE time = '$curr_time'";
                            $result = $conn -> query($query);

                            if(($result -> num_rows == 0) && ($text != '')){
                                $query = "INSERT INTO posts VALUES('$login', '$title', '$data', '$curr_time', '$text')";
                                $conn -> query($query);
                            }else
                                echo "<div class = 'warning' style = 'background: #aaa;'>Не спешите с отправкой постов</div>";
                        }

                        //Форма для написания постов
                        if(!isset($_POST['profile'])){//Если мы просматриваем чужую страницу, то не можем оставлять на ней записи
                            if(isset($_POST['wantwrite']))//Первоначально на странице форма не видна. Но мы добавляем специальную кнопку для её открытия
                                echo <<< _END
                                    <div id = 'container'>
                                        <form method = 'post' action = 'profile.php' style = 'background: #aaa; border: 1px solid black; border-radius: 3px;'>
                                            <div class = 'text-field'>
                                                <input class = 'text-field__input' type = 'text' name = 'title' id = 'message' style = 'width: 86.6%; margin-top: 1rem; margin-left: 1rem; margin-right: 1rem;' placeholder = 'Заголовок (необязательно)'><br>
                                            </div>
                                            <div class = 'textarea-field'>
                                                <textarea  class = 'textarea-field__input' type = 'text'  name = 'textpost' id = 'message' placeholder = 'Напишите ваше сообщение' style = 'width: 86.6%; margin-left: 1rem; margin-right: 1rem;'></textarea><br>
                                            </div>
                                            <input type = 'submit' value = 'Оставить запись' class = 'Button' style = 'margin: 1rem; width: 92.3%;'>
                                        </form>
                                    </div>
                                _END;
                            else
                                echo <<< _END
                                        <form action = 'profile.php' method = 'post'>
                                            <input type = 'hidden' name = 'wantwrite' value = 'yes'>
                                            <input type = 'submit' class = 'Button' value = 'Оставить запись'>
                                        </form>
                                    _END;
                        }

                        //Выводим посты на экран
                        if(isset($_POST['profile']))
                            $login = $_POST['profile'];
                        else
                            $login = $_SESSION['login'];

                        echo <<< _END
                            <div class = 'post'>
                                <b>Записи пользователя $login</b>
                            </div>
                        _END;

                        $query = "SELECT * FROM posts WHERE login = '$login'";
                        $result = $conn -> query($query);
                        for($i = 0; $i < $result -> num_rows; ++$i){
                            $result -> data_seek($i);
                            $row = $result -> fetch_array(MYSQLI_ASSOC);
                            $title = $row['title']; $text = $row['text']; $data = $row['data']; $t = $row['time'];
                            if($title != '')
                                echo <<< _END
                                    <div class = 'post'>
                                        <div class = 'posttitle'><b>$title</b></div><br>
                                        <div class = 'posttext'><b>$data, $t</b></div>
                                        <div class = 'posttext'>$text</div>
                                    </div>
                                _END;
                            else
                                echo <<< _END
                                    <div class = 'post'>
                                        <div class = 'posttext'><b>$data, $t</b></div>
                                        <div class = 'posttext'>$text</div>
                                    </div>
                                _END;
                        }

                    ?>
                </div>

                <div id = 'profile' style = 'float: right; margin-left: 1rem; width: 345px;'>

                    <div id = 'profile_photo'>
                        <?php
                            if(isset($_POST['profile']))
                                $login = $_POST['profile'];
                            else
                                $login = $_SESSION['login'];

                            $src = 'images/' . $login . '.jpg';

                            if(fotoexist($login, $login))
                                echo <<< _END
                                    <div style = 'width: 310px; height: 310px; background: black; margin: 1rem;'>
                                        <img src = $src style = 'height: 310px;'>
                                    </div>
                                _END;
                            else
                                echo <<< _END
                                    <div style = 'width: 310px; height: 310px; background: black; margin: 1rem;'>
                                        <img src = 'universal_data/Universal.jpg' style = 'height: 310px;'>
                                    </div>
                                _END;

                            function fotoexist($src, $login){
                                $conn = new mysqli('localhost', 'root', '', 'myownchat');
                                if($conn -> connect_error)
                                    die($conn -> connect_error);

                                $query = "SELECT * FROM fotoexist WHERE login = '$login'";
                                $result = $conn -> query($query);
                                if($result -> num_rows == 0){
                                    $query = "INSERT INTO fotoexist VALUES('$login', '0')";
                                    $conn -> query($query);
                                    return 0;
                                }else{
                                    $result -> data_seek(0);
                                    $row = $result -> fetch_array(MYSQLI_ASSOC);
                                    if($row['foto'])
                                        return 1;
                                    else
                                        return 0;
                                }
                            }
                            ?>
                    </div>

                    <div class = 'description' style = 'float: left;'>
                        <?php
                            $conn = new mysqli('localhost', 'root', '', 'myownchat');
                            if($conn -> connect_error)
                                die($conn -> connect_error);

                            if(isset($_POST['profile']))
                                $login = $_POST['profile'];
                            else
                                $login = $_SESSION['login'];

                            if(isset($_POST['redacted'])){ //Пользователь отредактировал данные и сохранил
                                unset($_POST['redact']);
                                $data = $_POST['data'];
                                $sex = $_POST['sex'];
                                $status = $_POST['status'];
                                $description = $_POST['description'];

                                //Проверка на допустимость

                                $query = "UPDATE profiles SET data = '$data' , sex = '$sex' , status = '$status' , description = '$description' WHERE login = '$login'";
                                $conn -> query($query);
                            }

                            if(!isset($_POST['redact'])){
                                $query = "SELECT * FROM profiles WHERE login = '$login'";
                                $result = $conn -> query($query);

                                if($login == $_SESSION['login'])
                                    $login .= ' -- это же Вы!';

                                if($result -> num_rows != 0){
                                    $result -> data_seek(0);
                                    $row = $result -> fetch_array(MYSQLI_ASSOC);
                                    $data = $row['data']; $sex = $row['sex']; $status = $row['status']; $description = $row['description'];
                                    echo <<< _END
                                        <div>
                                         <div style = 'float: left; width: 80px'>
                                            <b>Логин:</b> <br>
                                            <b>Дата р.:</b> <br>
                                            <b>Пол:</b> <br>
                                            <b>Статус:</b> <br>
                                            <b>Описание:</b> <br>
                                        </div>
                                        <div style = 'float: right; width: 210px;'>
                                            $login <br>
                                            $data <br>
                                            $sex <br>
                                            $status <br>
                                            $description <br>
                                        </div>
                                        </div>
                                    _END;
                                }else
                                    echo "Не удалось найти информацию по данному пользователю.<br>";
                            }else{
                                echo <<< _END
                                    <div>
                                        <div style = 'float: left; width: 80px'>
                                            <div style = 'margin-top: 0px; margin-bottom: 5px;'><b>Логин:</b></div><br>
                                            <div style = 'margin-bottom: 15px;'><b>Дата р.:</b></div><br>
                                            <div style = 'margin-bottom: 15px;'><b>Пол:</b></div><br>
                                            <div style = 'margin-bottom: 15px;'><b>Статус:</b></div><br>
                                            <div style = 'margin-bottom: 15px;'><b>Описание:</b></div><br>
                                        </div>

                                        <div style = 'float: right; width: 210px;'>
                                            <div style = 'margin-left: 10px; margin-bottom: 0px;'>$login</div><br>
                                            <form method = "post" action = "profile.php">
                                                <div class = 'text-field' style = 'width: 175px; margin: 10px; margin-bottom: 0px; margin-top: 0px;'>
                                                    <input class = 'text-field__input' style = 'height: 18px;' type = 'text' name = 'data' id = 'message' placeholder = 'Дата рождения'><br>
                                                </div>
                                                <div class = 'text-field' style = 'width: 175px; margin: 10px; margin-bottom: 0px; margin-top: 0px;'>
                                                    <input class = 'text-field__input' style = 'height: 18px;' type = 'text' name = 'sex' id = 'message' placeholder = 'Укажите ваш пол'><br>
                                                </div>
                                                <div class = 'text-field' style = 'width: 175px; margin: 10px; margin-bottom: 0px; margin-top: 0px;'>
                                                    <input class = 'text-field__input' style = 'height: 18px;' type = 'text' name = 'status' id = 'message' placeholder = 'Укажите ваш статус'><br>
                                                </div>
                                                <div class = 'text-field' style = 'width: 175px; margin: 10px; margin-bottom: 0px; margin-top: 0px;'>
                                                    <input class = 'text-field__input' style = 'height: 18px;' type = 'text' name = 'description' id = 'message' placeholder = 'Укажите ваше описание'><br>
                                                </div>
                                                <input type = 'hidden' name = 'redacted' value = 'yes'>
                                                <input type = "submit" value = "Отправить" class = 'Button' style = 'width: 190px; margin: 10px; margin-top: 0px;'>
                                            </form>
                                        </div>
                                        <div style = 'width: 290px; background: red; float: right;'>
                                            <div style = 'background: #ccc; border: 1px solid black; border-radius: 3px;'>
                                                <div style = 'box-sizing: border-box; padding: 10px;'>
                                                    <b>Замена аватарки</b><br>
                                                    Размер изображения не превышает 5120 Кб, пиксели по ширине не более 1500, по высоте не более 1500.
                                                    Фотографии вставлять квадратные, самостоятельно обрезанные! Формат только jpeg.
                                                    <form name = "upload" action = "download_img.php" method = "POST" ENCTYPE = "multipart/form-data">
                                                        Выберите файл для загрузки:
                                                        <input type = "file" name = "userfile">
                                                        <input type = "submit" name = "upload" value = "Загрузить">
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                _END;
                            }

                            //Если пользователь зашёл в свой профиль, он может редактировать описание. Всё, кроме логина
                            if(isset($_POST['profile']))
                                $login = $_POST['profile'];
                            else
                                $login = $_SESSION['login'];

                            if(isset($_POST['unredact']))
                                unset($_POST['redact']);

                            if($login == $_SESSION['login']){
                                if(!isset($_POST['redact']))
                                    echo <<< _END
                                        <form action = 'profile.php' method = 'post' style = 'float: right; margin-right: 1rem;'>
                                            <input type = 'hidden' name = 'redact' value = 'yes'>
                                            <input type = 'submit' value = 'Редактировать' class = 'Button' style = 'width: 91px; height: 30px; font-size: 11px; box-sizing: border-box; padding: 6px;'>
                                        </form>
                                    _END;
                                else
                                    echo <<< _END
                                        <form action = 'profile.php' method = 'post' style = 'float: right; margin-right: 1rem;'>
                                            <input type = 'hidden' name = 'unredact' value = 'yes'>
                                            <input type = 'submit' value = 'Отменить' class = 'Button' style = 'width: 91px; height: 30px; font-size: 11px; box-sizing: border-box; padding: 6px;'>
                                        </form>
                                    _END;
                            }
                        ?>
                    </div>
                </div>
            </div>
        </body>
    </html>