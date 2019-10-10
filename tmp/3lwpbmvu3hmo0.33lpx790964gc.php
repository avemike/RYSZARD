<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="ui/css/bootstrap.min.css">
    <link rel="stylesheet" href="./ui/css/loginRegister.css">
    <title>EZN - logowanie</title>
</head>
<body>
    <div class="container">
        <div class="row box">
            <div class="col-md-4 col-sm-8 col-xs-12 text-center inner-box">
                <form action="" method="POST">
                    <div class="form-group text-left">
                        <label for="login">Wpisz login</label>
                        <input name="login" class="form-control" placeholder="login" maxlength="<?= ($max_login_len) ?>" autocomplete="off"><br>
                    </div>
                    <div class="form-group text-left">
                        <label for="password">Wpisz hasło</label>
                        <input type="password" class="form-control" name="password" placeholder="password" maxlength="<?= ($max_passw_len) ?>"><br>                        
                    </div>
                    <button type="submit" class="btn btn-primary">Zaloguj się</button><br>
                    <h3 class="error"><?= ($loginErr) ?></h3>                                        
                    <a href="register">
                        Zarejestruj się
                    </a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>