<!DOCTYPE html>
<html lang = "ru">
    <head>
        <meta charset="utf-8">
        <link href='styles.css' rel='stylesheet'>
        <script src = 'http://code.jquery.com/jquery-1.11.1.min.js'></script>
        <title>Чатик</title>
        <style>
            #title{
                margin: 1rem;

                border: 1px solid black;
                border-radius: 3px;

                background: #aaa;

                box-sizing: border-box;
                padding: 7px;

                font-size: 33px;
                font-weight: 600;
            }

            #exit{
                width: 91%;
                margin: 1rem;
                box-sizing: border-box;
                padding: 7px;
            }

            #submit{
                width: 97.5%;
            }
        </style>
    </head>
    <body>


        <div id = 'rules'>
            <div id = 'title'>
                <?php require_once "phpregistration.php";?>
            </div>
            <div class = 'rule'>
                Чтобы писать в чат, нужно представиться и ввести пароль.
                Если вы не зарегистрарованы ранее, введите желаемое имя и пароль и система их запомнит.
            </div>
            <div class = 'rule'>
                Имя и текст проходят проверку на корректность. Допустимы любые символы, длина имени и сообщения не более 32 символов.
                Мат не отправляется и не сохраняется. Нельзя отправлять пустые сообщения, спамить, вставлять ссылки на стрононние ресурсы.
            </div>
            <div class = 'rule'>
                В чате есть модераторы, которые могут давать бан на своё усмотрение.
                Нельзя поднимать политические темы, переходить на личности. Любые формы троллинга будут пресекаться.
            </div>
            <div class = 'rule'>
                Требования к паролю: не короче 6 символов и не более 20 символов<br>
                Требования к имени: не пустое, не содержит мата, ругани, символов "|", не более 20 символов<br>
                Сообщение: не более 512 символов
            </div>
            <div class = 'rule' id = 'reg'>
                    <form method = 'post' action = 'registration.php'>
                        <div class = 'text-field'>
                            <label class = 'text-field__label' for = 'login'>Логин</label>
                            <input class = 'text-field__input' type = 'text' name = 'name' placeholder = 'Представьтесь' id = 'name'><br>
                            <div class = 'text-field__counter' id = 'counter1'></div>
                        </div>


                        <div class = 'text-field'>
                            <label class = 'text-field__label' for>Пароль</label>
                            <input class = 'text-field__input' type = 'password' name = 'password' placeholder = 'Введите пароль' id = 'pass'><br>
                        </div>

                        <input id = "submit" type = "submit"  value = 'Отправить' disabled = "disabled" class = 'Button'><br>
                    </form>
                    <div float = 'left' id = 'requirement'>Я согласен с правилами</div>
                    <input type = "checkbox" onclick = "able()"><br>
                    <input id = "exit" type = "button" onclick = 'ret()' value = 'Вернуться в чат!' class = 'Button'><br>
            </div>
        </div>

        <script>
             function registrate(){
                var name = $('#name').val()
                var password = $('#pass').val()

                hr = "registration.php?name=" + name + "&password=" + password + "&registrate=yes";

                document.location.href = hr
             }
             function able(){
                document.getElementById('submit').disabled = !document.getElementById('submit').disabled;
             }
             function ret(){
                hr = "main.php";
                document.location.href = hr
             }

        </script>

    </body>
</html>