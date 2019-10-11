<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form method="POST" action="register" >
        Login<input type="text" name="username" ></br>
        Hasło<input type="password" name="password"></br>
        <input type="submit" value="Zarejestruj!">
    </form>
    <div>
<<<<<<< HEAD:ui/main.html
        {{ @error1 }}
        {{ @error2 }}
        {{ @error3 }}
        {{ @error4 }}
=======
        <?= ($error1)."
" ?>
        <?= ($error2)."
" ?>
        <?= ($error3)."
" ?>
        <?= ($error4)."
" ?>
>>>>>>> c724852908ec009ef2f585e43b20e6eadec4011b:tmp/3lwpbmvu3hmo0.pw3tcs9b040c.php
    </div>
</body>
</html>