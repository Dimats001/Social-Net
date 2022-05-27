 <?php
            session_start();
            if(isset($_POST['exit'])){
                unset($_SESSION['login']);
                session_destroy();
            }

            if(!isset($_SESSION['login'])){
                $butt = <<<_END
                    <div id = 'box'>
                        <div class = 'warning' id = 'warning1' style = 'text-align: center;'> Для доступа  необходима авторизация. Нажмите на кнопку ниже</div>
                        <input type = "submit" value = "Войти" onclick = enter() class = 'Button' id = 'enter'>
                        <script>
                            function enter(){
                                hr = 'startpage.php'
                                document.location.href = hr
                            }
                        </script>
                    </div>
                _END;
                die($butt);
            }
        ?>