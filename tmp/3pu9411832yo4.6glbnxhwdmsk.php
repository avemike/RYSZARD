<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
    <form action="mail" method="POST">
        <input name="address" placeholder="adresat"><br>
        <input name="content" placeholder="treść" autocomplete="off"><br>
        <button type="submit">wyślij</button><br>
    </form>
    <h1><?= ($mailerror) ?></h1>
</body>
</html>