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
            <div class = 'warning' style = 'float: left;'>
                Раздел пока в разработке!
            </div>
        </div>
    </body>
</html>