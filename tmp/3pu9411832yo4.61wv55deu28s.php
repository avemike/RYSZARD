<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form method="POST" action="/RYSZARD/Registration.php" >
        Login<input type="text" name="username" ></br>
        Mail<input type="text" name="email" ></br>
        Hasło<input type="text" name="password"></br>
        <input type="submit" value="Zarejestruj!">
    </form>
    <div>
        <?= ($error1)."
" ?>
        <?= ($error)."
" ?>
    </div>
</body>
</html>