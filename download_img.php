<?php
    $upload_dir = 'images/';

    session_start();
    if(isset($_POST['profile']))
        $login = $_POST['profile'];
    else
        $login = $_SESSION['login'];
    $name = $login . '.jpg';

    $upload_file = "$upload_dir$name";

    if(($_FILES['userfile']['type'] == 'image/gif' || $_FILES['userfile']['type'] == 'image/jpeg' || $_FILES['userfile']['type'] == 'image/png') &&
    ($_FILES['userfile']['size'] != 0 && $_FILES['userfile']['size'] <= 51200000)){
        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $upload_file)){
            $size = getimagesize($upload_file);

            if(($size[0] < 1501 && $size[1] < 1501) && ($size[0] == $size[1]))
                echo "Файл загружен. Путь к файлу: <b>http:/yoursite.ru/".$upload_file."</b>";
            else{
                echo "Ошибка: Высота и ширина изображения более 1500 пискелей, либо изображение не квадртаное";
                unlink($upload_file);
            }

        }else
            echo "Файл не загружен, вернитеcь и попробуйте еще раз";
    }else
        echo "Размер файла не должен превышать 5120Кб";
?>