<?php 
    class home{
        function gethome($f3){
            if(empty($_SESSION["login"]) || empty($_SESSION["nickname"])){
                $f3->reroute('@login');
             }
            echo \Template::instance()->render('profile.html');
        }
        function missions($f3){  
            global $db;      
            if(empty($_SESSION["nickname"])){
                $f3->reroute('@login');
            }
            //if character have active mission
            if($result=$db->exec('SELECT mission_id, TIMESTAMPDIFF(SECOND,start_date,current_timestamp()) AS started_ago, duration_time, currency_reward, exp_reward, mission_description FROM missions LEFT JOIN mission_template on missions.mission_template_id = mission_template.mission_template_id WHERE char_id=? AND mission_active=1', $_SESSION["char_id"])){
                $f3->set('missions', false);
                //if active mission has ended
                if($result[0]["started_ago"]>$result[0]["duration_time"]){
                    $f3->set('missionready', $result[0]);
                    $f3->set('mission_description', $result[0]["mission_description"]);

                    $char=new DB\SQL\Mapper($db,'characters');
                    $char->load(array('char_id=?',$_SESSION["char_id"]));
                    $char->currency+=$result[0]["currency_reward"];
                    $char->exp+=$result[0]["exp_reward"];
                    $char->save();
                    $_SESSION["currency"]=$char->currency;
                    $_SESSION["exp"]=$char->exp;

                    $db->exec('DELETE FROM missions WHERE char_id=?', $_SESSION["char_id"]);
                }
                //if mission is not ended yet
                else{
                    $f3->set('missionready', false);
                    $f3->set('missionbox',$result[0]["duration_time"]-$result[0]["started_ago"]);
                }
            }
            //no active missions
            else{
                $f3->set('missions', true);

                //if missions are already generated
                if($result=$db->exec('SELECT char_id, currency_reward, exp_reward, duration_time, mission_name, mission_id FROM missions LEFT JOIN mission_template on missions.mission_template_id = mission_template.mission_template_id WHERE char_id=?',$_SESSION["char_id"])){
                    $f3->set('missionbox',$result);
                }
                //else generate new missions
                else{
                    $mission_templates = $db->exec('SELECT mission_template_id FROM mission_template ORDER BY rand() LIMIT 3');


                    for($i=0;$i<3;$i++){
                        $duration_time=rand(1,20)*30;
                        $currency_reward=round((($_SESSION["level"]*$_SESSION["level"]/10)+100)*$duration_time/100*(1+rand(0,1)));
                        $exp_reward=round((($_SESSION["level"]*$_SESSION["level"]/10)+100)*$duration_time/100*(1+rand(0,1)));

                        $db->exec('INSERT INTO missions (char_id, currency_reward, exp_reward, duration_time, mission_template_id, start_date, mission_active)
                        values (?, ?, ?, ?, ?, CURRENT_TIMESTAMP(), "0")', array($_SESSION["char_id"], $currency_reward, $exp_reward, $duration_time, $mission_templates[$i]["mission_template_id"]));
                    }

                    $f3->set('missionbox',$db->exec('SELECT char_id, currency_reward, exp_reward, duration_time, mission_name, mission_id FROM missions LEFT JOIN mission_template on missions.mission_template_id = mission_template.mission_template_id WHERE char_id=?',$_SESSION["char_id"]));
                }
            }
            echo \Template::instance()->render('missions.html');
        }
        function choosemission($f3){
            if(!empty($_SESSION["nickname"])){
                global $db;
                $db->exec('UPDATE missions SET mission_active="1", start_date=current_timestamp() WHERE mission_id=?',$_POST["activemission"]);
                $f3->reroute('@missions');
            }
            $f3->reroute('@login');
        }
    }
    class login{
        function getlogin($f3){
            if(!empty($_SESSION["login"])){
                if(empty($_SESSION["nickname"])){
                    $this->getservers($f3);
                    echo \Template::instance()->render('servers.html');
                }
                else{
                    $f3->reroute('@home');
                }
            }
            else{
                echo \Template::instance()->render('login.html');
            }
        }
        function postlogin($f3){
            if (!empty($_SESSION["login"])){
                $f3->reroute('@login');
            }
            
            $f3->set('logintemplate', 'login.html');
            global $db;
            $user=new DB\SQL\Mapper($db,'accounts');
            if(!empty($_POST["login"])){
                $login=$_POST["login"];
                $password=md5($_POST["password"]);
                // $password=$_POST["password"];
    
                if(strlen($login)>$f3->get('max_login_len')){
                    $loginErr="login or password incorrect";
                }
                else{
                    if($user->load(array('login=?',$login))->login == $login && $user->load(array('login=?',$login))->password==$password){
                        $_SESSION["login"]=$login;
                        $_SESSION["user_id"]=$user->load(array('login=?',$login))->user_id;
    
                        $f3->set('servers', 'servers.html');
                        $f3->set('logintemplate', 'servers.html');
                        $this->getservers($f3);
                    }
                    else{
                        $loginErr="login or password incorrect";
                    }
                }
            }
            $f3->set('loginErr', $loginErr);
            echo \Template::instance()->render($f3->get('logintemplate'));
        }
        function getservers($f3){
            global $db;
            $sql="SELECT server_id, char_id, level, nickname 
            FROM (
                SELECT servers.server_id, char_id, level, nickname
                FROM servers
                JOIN characters ON servers.server_id = characters.server_id
                WHERE user_id=:id
        
                UNION
        
                SELECT servers.server_id, NULL AS char_id, NULL AS LEVEL, NULL AS nickname
                FROM servers
                LEFT JOIN characters ON servers.server_id = characters.server_id
                WHERE user_id!=:id or user_id IS NULL
            ) t
            GROUP BY server_id";
            $f3->set('result',$db->exec($sql, array(':id'=>$_SESSION["user_id"])));
        }
        function logintoserver($f3){
            global $db;
            if (empty($_SESSION["nickname"]) && !empty($_SESSION["login"]) && $db->exec('SELECT * FROM servers WHERE server_id=?', $_POST["serverno"])){
                if($result=$db->exec('SELECT char_id, nickname, characters.server_id, level, currency, exp FROM servers LEFT JOIN characters ON servers.server_id = characters.server_id WHERE servers.server_id = ? AND user_id = ?', array($_POST["serverno"],$_SESSION['user_id']))){
                    $_SESSION["char_id"]=$result[0]["char_id"];
                    $_SESSION["nickname"]=$result[0]["nickname"];
                    $_SESSION["server"]=$result[0]["server_id"];
                    $_SESSION["level"]=$result[0]["level"];
                    $_SESSION["currency"]=$result[0]["currency"];
                    $_SESSION["exp"]=$result[0]["exp"];

                    $f3->reroute('@home');
                }
                else{
                    //do zrobienia tworzenie postaci
                    // $f3->reroute('@createchar');
                    $f3->reroute('@login');
                }
            }
            else{
                $f3->reroute('@login');
            }
        }
        function logout($f3){
            session_unset();
            $f3->reroute('@login');
        }

    }

    class register {
        function inserting_data($f3) {
            //create mapper
            $f3->set('object_mapper',$user=new DB\SQL\Mapper($f3->get('conn'),'accounts')); 
            $utf = \UTF::instance();
            $login=$_POST['username'];
            $email=$_POST['email'];
            $alphabet=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","v","s","t","u","w","x","y","z","1","2","3","4","5","6","7","8","9","0");
            //checking if username and password is no empty
            if ((!$f3->get('POST.username')=="")&&(!$f3->get('POST.password')=="")) {
                //checking if username has permitted characters
                for ($i=0; $i<($utf->strlen($f3->get('POST.username'))); $i++) {
                    if(!in_array($login[$i],$alphabet)) {
                        $error1_temp="Proszę podać poprawną nazwę użytkownika!";    
                    }
                };
                $f3->set('error1',$error1_temp); 
                //checking if username is not too long
                if ((($utf->strlen($f3->get('POST.username')))<=($f3->get('max_login_len')))) {
                    if(($f3->get('error1')==null)){
                        //checking if password is not too long
                        if ((($utf->strlen($f3->get('POST.password'))))>($f3->get('max_password_len'))) {
                            $f3->set('error2',"Hasło jest zbyt długie!");
                        }   else {
                                //insert password and username into database
                                if (($f3->get('object_mapper')->load(array('login=?',$f3->get('POST.username'))))!=$f3->get('POST.username')) {
                                    $f3->get('object_mapper')->login=$f3->get('POST.username');
                                    $f3->get('object_mapper')->password=md5($f3->get('POST.password')); 
                                    $f3->get('object_mapper')->save(); 
                                    $f3->reroute('@login'); 
                                }
                                else{
                                    $f3->set('error5',"Użytkownik już istnieje!");
                                }
                            }
                    }
                }   else {
                        $f3->set('error3',"Nazwa użytkownika jest zbyt długa!"); 
                    };        
                
            } else {
                $f3->set('error4',"Proszę wypełnić wszystkie pola!");
            };  
            echo \Template::instance()->render('register.html');
        }
    }
    
    // ***********THIS IS FOR MAIL -> DON'T DELETE THIS**************    
    /* else if (!$f3->get('POST.email')=="") { //checking if email is no empty 
        //checking if email is correct
        if (filter_var($f3->get('POST.email'), FILTER_VALIDATE_EMAIL)) {
            //insert email into database
            $f3->get('object_mapper')->email=$f3->get('POST.email');
        }   else {
                $f3->set('error',"Podaj poprawny email!");
                echo "Mail niepoprawny";
            }   
    } */
?>