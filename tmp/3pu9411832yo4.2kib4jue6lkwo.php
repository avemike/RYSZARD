<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="ui/css/bootstrap.min.css">
    <title>Document</title>
</head>
<body>
    <div class="container">
        <?php echo $this->render('./leftMenu.html',NULL,get_defined_vars(),0); ?>
        <div class="col-lg-9 border">
            <div class="col-md-6 border">
                <div class="col-lg-12">
                    <img class="profile-photo" src="ui/images/profile.jpg" alt="">
                </div>
                <div class="col-lg-12">
                    <div class="list-group">
                        <li class="list-group-item">Nick : <?= ($SESSION['nickname']) ?></li>
                        <li class="list-group-item">Server : <?= ($SESSION['server']) ?></li>
                        <li class="list-group-item">Login : <?= ($SESSION['login']) ?></li>
                        <li class="list-group-item">Lv : <?= ($SESSION['level']) ?></li>
                        <li class="list-group-item">Gold : <?= ($SESSION['currency']) ?></li>
                        <li class="list-group-item">Exp : <?= ($SESSION['exp']) ?></li>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 border"></div>
        </div>
    </div>
</body>
<style>
    /* .border {
        border: 1px solid black;
    } */
    .profile-photo {
        width: 100%;
        height: auto;
    }
</style>
</html>