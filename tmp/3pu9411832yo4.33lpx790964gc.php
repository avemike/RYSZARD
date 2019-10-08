<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .error{ 
            color: red;
        }
        .green{
            color: green;
        }
    </style>
</head>
<body>
    <form action="" method="POST">
    <input name="login" placeholder="login" maxlength="<?= ($max_login_len) ?>" autocomplete="off"><br>
    <input type="password" name="password" placeholder="password" maxlength="<?= ($max_passw_len) ?>"><br>
    <button type="submit">Zaloguj się</button><br>
    <a href="register">Zarejestruj się</a>
    <h1 class="error"><?= ($loginErr) ?></h1>
    </form>
</body>
</html>