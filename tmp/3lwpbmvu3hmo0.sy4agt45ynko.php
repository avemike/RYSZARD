<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div>
        <form method="POST" action="createchar">
            Informatyk<input type="radio" name="occupation" value="informatyk"></br>
            Mechatronik<input type="radio" name="occupation" value="mechatronik"></br>
            Elektronik<input type="radio" name="occupation" value="elektronik"></br>
            Wpisz nazwę postaci<input type="text" name="nickname"></br>
            <button type="submit">Stwórz postać</button><br>
        </form>
        <?= ($creating_error1)."
" ?>
        <?= ($creating_error2)."
" ?>
    </div>

</body>
</html>