<!--
Релизация личных сообщений. Написать можно кому угодно. Из команд: удаление, и игры с цветом имени/текста
!-->
<!DOCTYPE html>
<html lang = "ru">
    <head>
        <meta charset="utf-8">
        <link href='styles.css' rel='stylesheet'>
        <script src = 'http://code.jquery.com/jquery-1.11.1.min.js'></script>
        <script src = 'functions.js'></script>
        <title>Чатик</title>
        <style>
            #listofdialogs{
                background: #aaa;
                border: 1px solid black;
                margin-left: 40px;
            }
        </style>
    </head>
    <body style = 'background: #eee;'>
        <?php require_once "checkauth.php"; ?>
        <div class = 'site'>

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

            <div id = 'listofdialogs' style = 'float: left; width: 480px; margin: 1rem; margin-right: 0rem'>
                <?php require_once "chat_private_logic.php";?>
                <form action = 'chat_private.php' method = 'post'>
                    <div class = 'text-field'>
                        <label class = 'text-field__label' for = 'msg' style = 'font-size: 19.1px; width: 93%; margin-top: 0.5rem; margin-left: 0.9rem;'>С кем ещё вы хотите начать диалог?</label>
                        <input class = 'text-field__input' type = 'text' name = 'dialogwith' id = 'message' placeholder = 'Напишите логин адресата' style = 'width: 91%; margin: 0.5rem;'><br>
                    </div>
                    <input type = 'submit' class = 'Button' value = 'Начать диалог' style = 'margin-left: 1rem; margin-right: 1rem; width: 93.5%;'>
                </form>
            </div>

            <?php
            require_once "functions.php";
            if(!isset($_POST['dialogwith'])) exit(); //Пока пользователь не выбрал, с кем хочет общаться, чат ему не будет показан ?>

            <div id = 'main' style = 'float:right;'>
                <?php require_once "messages_private.php";?>
                <div id = 'chat'>
                    <div id = 'input'>

                        <form method = "post" action = "chat_private.php">
                            <div class = 'text-field'>
                                <label class = 'text-field__label' for = 'msg'>Ваше сообщение</label>
                                <input class = 'text-field__input' type = 'text' name = 'txt' id = 'message' placeholder = 'Напишите ваше сообщение'><br>
                            </div>
                            <?php
                                    $dialogwith = security($_POST['dialogwith']);
                                    echo <<< _END
                                            <input type = 'hidden' name = 'dialogwith' value = '$dialogwith'>
                                    _END;
                            ?>
                            <input type = "submit" value = "Отправить сообщение" class = 'Button'>

                        </form>
                </div>
            </div>


        </div>

    </body>
</html>