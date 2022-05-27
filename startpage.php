<!DOCTYPE html>
<html lang = "ru">
    <head>
        <meta charset="utf-8">
        <link href='styles.css' rel='stylesheet'>
        <script src = 'http://code.jquery.com/jquery-1.11.1.min.js'></script>
        <title>Добро пожаловать в нашу соцсеть *Название*!</title>
    </head>
    <body>
        <div id = 'main'>
            <div id = 'chat'>
                <div id = 'input'>
                    <form method = "post" action = "startpage.php">
                    <div class = 'text-field'>
                        <label class = 'text-field__label' for = 'login' id = 'login0'>Логин</label>
                        <input class = 'text-field__input' type = 'text' name = 'name' placeholder = 'Представьтесь' id = 'name'><br>
                        <div class = 'text-field__counter' id = 'counter1'></div>
                    </div>

                    <div class = 'text-field'>
                        <label class = 'text-field__label' for>Пароль</label>
                        <input class = 'text-field__input' type = 'password' id = 'paasowrd0' name = 'password' placeholder = 'Введите пароль' id = 'pass'><br>
                    </div>
                        <?php require_once "Startpage_phplogic.php";?>
                        <?php
                            if(!isset($_SESSION['login']))
                            echo <<< _END
                                <input type = "submit" value = "Войти" class = 'Button'>
                            _END;
                        ?>
                    </form>

                    <?php
                        if(!isset($_SESSION['login']))
                            echo <<< _END
                                <input type = 'button' onclick = 'reg()' value = 'Регистрация' class = 'Button'><br>
                            _END;
                    ?>
                    <script src = 'functions.js'></script>
                </div>
            </div>
        </div>
    </body>
</html>