<?php
                    require_once "functions.php";

                    if(isset($_POST['dialogwith'])){
                        echo "<div style = 'margin: 1rem; margin-bottom: 0rem;'><b>Вы желаете общаться с " . $_POST['dialogwith']. ". Справа ваш диалог</b></div><br>";
                    }else
                        echo "<div style = 'margin: 1rem; margin-bottom: 0rem;'><b>Выберите, с кем вы хотите общаться.</b></div>";

                    $login = $_SESSION['login'];
                    $conn = new mysqli('localhost', 'root', '', 'myownchat');

                    $query = 'SELECT * FROM dialogs';
                    $result = $conn -> query($query);

                    if($result -> num_rows != 0){
                        echo "<div style = 'margin-left: 1rem; margin-right: 1rem;'><b>У вас активны диалоги со следующими людьми:</b></div><br>";
                        for($i = 0; $i < $result -> num_rows; ++$i){
                            $result -> data_seek($i);
                            $row = $result -> fetch_array(MYSQLI_ASSOC);
                            $name1 = $row['name1'];
                            $name2 = $row['name2'];
                            if($name1 == $login)
                                echo <<<_END
                                    <form action = 'chat_private.php' method = 'post'>
                                        <input type = 'hidden' name = 'dialogwith' value = '$name2'>
                                        <input class = 'Button' type = 'submit' style = 'margin: 1rem; margin-top: 0px; margin-bottom: 0px; width: 92.5%;' value = '$name2' ><br>
                                    </form>
                                _END;
                            if($name2 == $login)
                                echo <<< _END
                                    <form action = 'chat_private.php' method = 'post'>
                                        <input type = 'hidden' name = 'dialogwith' value = '$name1'>
                                        <input type = 'submit' class = 'Button' value = '$name1' style = 'margin: 1rem; margin-top: 0px; margin-bottom: 0px; width: 92.5%;'><br>
                                    </form>
                                _END;
                        }
                    }
                    ?>