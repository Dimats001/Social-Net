<!DOCTYPE html>
<html lang = "ru">
    <head>
        <meta charset="utf-8">
        <link href='styles.css' rel='stylesheet'>
        <script src = 'http://code.jquery.com/jquery-1.11.1.min.js'></script>
        <script src = 'functions.js'></script>
        <title>Соцсеть</title>
    </head>
    <body style = 'background: #eee;'>
    <?php require_once "checkauth.php";?>
        <div>
            <div id = 'menu' style = 'float: left;'>
                <input value = 'Главная' class = 'Button' onclick = 'main()' style = 'height: 16vh; border-radius: 0px; box-sizing: border-box; margin: 0rem; text-align: center;'>
                <input value = 'Профиль' class = 'Button' onclick = 'profile()' style = 'height: 16vh; border-radius: 0px; box-sizing: border-box; margin: 0rem; text-align: center;'>
                <input value = 'Общий чат' class = 'Button' onclick = 'chat()' style = 'height: 16vh; border-radius: 0px; box-sizing: border-box; margin: 0rem; text-align: center;'>
                <input value = 'Личные сообщения' class = 'Button' onclick = 'chat()' style = 'height: 16vh; border-radius: 0px; box-sizing: border-box; margin: 0rem; text-align: center;'>
                <input value = 'Друзья' class = 'Button' onclick = 'friends()' style = 'height: 16vh; border-radius: 0px; box-sizing: border-box; margin: 0rem; text-align: center;'>
                <form action = 'mainpage.php' method = 'post'>
                    <input type = 'hidden' name = 'exit' value = 'yes'>
                    <input type = 'submit' value = 'Выйти' class = 'Button' onclick = 'chat()' style = 'height: 16vh; border-radius: 0px; box-sizing: border-box; margin: 0rem; text-align: center;'>
                </form>
            </div>
            <div id = 'news' style = 'float: left; margin-left: 3rem; background: #ccc; border: 1px solid black; border-radius: 3px; width: 470px;'>
                <div class = 'posttitle' style = 'margin: 1rem;'>
                    <b>Добро пожаловать в нашу социальную сеть!</b>
                </div>

                <div class = 'post'>
                    <div class = 'posttitle' style = 'margin: 1rem;'>
                        <b>Открытое тестирование 24.05.2022, 20:55</b>
                    </div>
                    <div class = 'posttext' style = 'width: 93%; margin: 1rem; box-sizing: border-box; padding: 10px;'>
                        <?php
                        echo <<< _END
                            Рады сообщить о начале открытого тестирования нашего проекта!<br><br>

                            Сеть маленькая, экспериментальная. Рассчитана на небольшое количество людей.
                            Это, конечно, не Инстаграмм, зато все свои!
                            <br><br>
                            Ваш админ
                            _END;
                        ?>
                    </div>
                </div>

                <div class = 'post'>
                    <div class = 'posttitle' style = 'margin: 1rem;'>
                        <b>Профиль 24.05.2022, 19:30</b>
                    </div>
                    <div class = 'posttext' style = 'width: 93%; margin: 1rem; box-sizing: border-box; padding: 10px;'>
                        <?php
                        echo <<< _END
                            Раздел профиль полностью завершён!<br><br>

                            Теперь вы можете зафиксировать информацию о себе, начать вести блог, а так же заходить друг к другу в гости!
                            Закрытых профилей нет, поэтому думайте, какую информацию вы публикуете
                            <br><br>
                            Ваш админ
                            _END;
                        ?>
                    </div>
                </div>

                <div class = 'post'>
                    <div class = 'posttitle' style = 'margin: 1rem;'>
                        <b>Заработали личные сообщения 23.05.2022, 13:28</b>
                    </div>
                    <div class = 'posttext' style = 'width: 93%; margin: 1rem; box-sizing: border-box; padding: 10px;'>
                        <?php
                        echo <<< _END
                            Рады сообщить о том, что раздел "Личные сообщения" наконец-то доступен для полноценного испольвозвания!<br><br>

                            Теперь можно писать друг другу лично. Писать может кто угодно и кому угодно. Вы всегда можете удалить ваш диалог, если не хотите больше общаться
                            <br><br>
                            Ваш админ
                            _END;
                        ?>
                    </div>
                </div>
            </div>
            <div style = 'float: left; margin-left: 3rem;'>
                    <?php
                        //Реализован поиск пользователей по логину
                        if(isset($_POST['nosearch']))
                            unset($_POST['search']);

                        if(!isset($_POST['search']))
                            echo <<< _END
                                <div style = 'float: right;'>
                                    <form action = 'mainpage.php' method = 'post'>
                                        <input type = 'hidden' name = 'search' value = 'yes'>
                                        <input type = 'submit' class = 'Button' value = 'Поиск по логину'>
                                    </form>
                                </div>
                            _END;
                        else{
                            echo <<< _END
                                <div id = 'search' style = 'float: right; width: 100%;'>
                                    <form method = 'post' action = 'mainpage.php'>
                                        <input type = 'hidden' name = 'nosearch' value = 'yes'>
                                        <input type = 'submit' class = 'Button' value = 'Закрыть' style = 'width: 88%; margin: 1rem;'>
                                    </form>
                                    <div>
                                    <form method = "post" action = "profile.php">
                                        <div class = 'text-field'>
                                            <input class = 'text-field__input' type = 'text' name = 'profile' id = 'message' placeholder = 'Напишите логин' style = "width: 88%; margin: 1rem; margin-bottom: 0rem; box-sizing: border-box;"><br>
                                        </div>
                                        <input type = "submit" value = "Искать" class = 'Button' style = "margin: 1rem; margin-top: 0rem; width: 88%;">
                                    </form>
                                    </div>
                                </div>
                            _END;
                        }
                    ?>
            </div>
        </div>
    </body>
</html>