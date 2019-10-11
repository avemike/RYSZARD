<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form method="POST" action="registration" >
        Login<input type="text" name="username" ></br>
        Hasło<input type="password" name="password"></br>
        <input type="submit" value="Zarejestruj!">
    </form>
    <div>
        <?= ($error1) ?></br>
        <?= ($error2) ?></br>
        <?= ($error3) ?></br>
        <?= ($error4)."
" ?>
    </div>
</body>
</html>