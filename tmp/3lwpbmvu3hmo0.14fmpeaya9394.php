<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .error{ 
            color: red;
        }
    </style>
</head>
<body>
    <form method="POST" action="register" >
        Login<input type="text" name="username" maxlength="<?= ($max_login_len) ?>"></br>
        Hasło<input type="password" name="password" maxlength="<?= ($max_password_len) ?>"></br>
        <input type="submit" value="Zarejestruj!">
    </form>
    <a href="login">Powrót do logowania</a>
    <div class="error">
        <?= ($error1)."
" ?>
        <?= ($error2)."
" ?>
        <?= ($error3)."
" ?>
        <?= ($error4)."
" ?>
        <?= ($error5)."
" ?>
    </div>
</body>
</html>