<!--
Общий чат. Писать и читать могут абсолютно все пользователи сети.
Есть всевозможные админские команды.
!-->
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

            <div id = 'main' style = 'float:right;'>
                <?php require_once "messages.php";?>
                <div id = 'chat'>
                    <div id = 'input'>

                        <form method = "post" action = "chat.php">
                            <div class = 'text-field'>
                                <label class = 'text-field__label' for = 'msg'>Ваше сообщение</label>
                                <input class = 'text-field__input' type = 'text' name = 'txt' id = 'message' placeholder = 'Напишите ваше сообщение'><br>
                            </div>

                            <input type = "submit" value = "Отправить" class = 'Button'>

                        </form>

                        <input type = 'button' onclick = 'rules()' value = 'Правила использования чата' class = 'Button'><br>

                </div>
            </div>


        </div>

    </body>
</html>