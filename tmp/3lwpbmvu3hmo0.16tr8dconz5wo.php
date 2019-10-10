<!DOCTYPE html>
<html lang="pl-PL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="ui/css/bootstrap.min.css">
    <title>Document</title>
    <style>
        .centered{
            align-content: center;
            text-align: center;
        }
    
    </style>
</head>
<body>
    <?php echo $this->render('./upperPanel.html',NULL,get_defined_vars(),0); ?>
    <div class="container">
        <div class="row">
            <?php echo $this->render('./leftMenu.html',NULL,get_defined_vars(),0); ?>
            <div class="col-md-9">
                <?php if ($missions): ?>
                    
                        <h1 class="centered">Misje</h1>
                        <?php foreach (($missionbox?:[]) as $mission): ?>
                            <form action="choosemission" method="POST">
                                <p class="click"><?= ($mission['mission_name']) ?>: <span class="mission_time"><?= ($mission['duration_time']) ?></span>min, nagrody: <?= ($mission['currency_reward']) ?>golda i <?= ($mission['exp_reward']) ?>expa</p>
                                <input type="hidden" name="activemission" value="<?= ($mission['mission_id']) ?>">
                            </form>
                        <?php endforeach; ?>
                        <script>
                            function makeminutes(){
                                timespans = document.querySelectorAll(".mission_time");
                                for(const time of timespans){
                                    time.innerHTML=Number(time.textContent)/60;
                                }
                            }
                            makeminutes();
                        </script>
                    
                    <?php else: ?>
                        <?php if ($missionready): ?>
                            
                                <h2 class="centered"><?= ($mission_description) ?></h2>
                                <h2 class="centered">Twoje nagrody to <?= ($missionready['currency_reward']) ?> golda i <?= ($missionready['exp_reward']) ?>expa</h2>
                                <a href="missions">Zacznij nową misję</a>
                                <br>
                            
                            <?php else: ?>
                                <h2 class="centered">Jesteś teraz w trakcie misji i zostało ci tyle czasu do końca:</h2>
                                <h2 id="timer" class="centered"><?= ($missionbox) ?></h2>
                                <script>
                                    minutes = 0;
                                    seconds = Number(document.getElementById("timer").innerHTML);
                                    while(seconds>59){
                                        minutes++;
                                        seconds-=60;
                                    }
                                    document.getElementById("timer").innerHTML=minutes+" minut "+seconds+" sekund";
            
                                    function odejmczas(){
                                        if(seconds<1 && minutes==0){
                                            window.location.href="";
                                        }
                                        else{
                                            if(seconds==0){
                                                minutes--;
                                                seconds=59
                                            }
                                            else{
                                                seconds--;
                                            }
                                            document.getElementById("timer").innerHTML=minutes+" minut "+seconds+" sekund";
                                            setTimeout(odejmczas, 1000);
                                        }
                                    }
                                    setTimeout(odejmczas, 1000);
                                </script>
                            
                        <?php endif; ?>
                    
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script>
        const buttons = document.querySelectorAll(".click")
        for (const button of buttons) {
            button.addEventListener('click', function(event) {
                button.parentNode.submit();
            })
        }
    </script>
</body>
</html>