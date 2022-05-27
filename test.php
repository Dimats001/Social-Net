<!DOCTYPE html>
<html lang = "ru">
    <head>
        <meta charset="utf-8">
        <script src = 'http://code.jquery.com/jquery-1.11.1.min.js'></script>
        <title>Чатик</title>
        <style>
            #left{
                float: left;
                width: 320px;
                border: 1px;
                background: blue;
                margin-left: 60px;
            }
            #center{
                float:left;
                width: 320px;
                border: 1px;
                background: green;
                margin-left: 60px;
                margin-top: 20px;
            }
            #right{
            display: inline-block;
                float: left;
                width: 320px;
                border: 1px;
                background: red;
                margin-left: 60px;
                vertical-align: middle;
            }


            #container {
                width: 1200px;
	            height:150px;
	            margin:0 auto;
	            background-color:#66CCFF;
	        }

        </style>
    </head>
    <body style = 'background: #eee;'>
        <div id = 'container'>

         <div id = 'left'>
        <h1>Левый блок</h1>
        </div>

        <div id = 'center'>
        <h1>Центральный блок</h1>
        </div>

        <div id = 'right'>
        <h1> Правый блок </h1>
        </div>

        </div>
    </body>
</html>