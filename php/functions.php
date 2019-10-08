<?php 
    class home{
        function gethome($f3){
            if (empty($_SESSION["login"]) || empty($_SESSION["nickname"])){
                $f3->reroute('@login');
             }
            echo \Template::instance()->render('profile.html');
        }
    }
    class login{
        function getlogin($f3){
            if(!empty($_SESSION["login"])){
                if(empty($_SESSION["nickname"])){
                    $this->getservers($f3);
                    // $f3->set('result',$db->exec('SELECT servers.server_id, char_id, level, nickname FROM servers left join characters on servers.server_id = characters.server_id where user_id=? or user_id IS NULL', $_SESSION["user_id"]));
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
        
                SELECT
                    servers.server_id,
                    NULL AS char_id,
                    NULL AS LEVEL,
                    NULL AS nickname
                FROM
                    servers
                    LEFT JOIN characters
                    ON servers.server_id = characters.server_id
                WHERE user_id!=:id or user_id IS NULL
            ) t
            GROUP BY server_id";
            $f3->set('result',$db->exec($sql, array(':id'=>$_SESSION["user_id"])));
        }
        function logintoserver($f3){
            global $db;
            if (empty($_SESSION["nickname"]) && !empty($_SESSION["login"]) && $db->exec('SELECT * FROM servers WHERE server_id=?', $_POST["serverno"])){
                if($result=$db->exec('SELECT char_id, nickname, characters.server_id, level FROM servers LEFT JOIN characters ON servers.server_id = characters.server_id WHERE servers.server_id = ? AND user_id = ?', array($_POST["serverno"],$_SESSION['user_id']))){
                    
                    $values = array();
                    foreach($result[0] as $value){
                        array_push($values, $value);
                    }
                    $_SESSION["char_id"]=$values[0];
                    $_SESSION["nickname"]=$values[1];
                    $_SESSION["server"]=$values[2];
                    $_SESSION["level"]=$values[3];
                    $f3->reroute('@home');

                }
                else{
                    $f3->reroute('@createchar');
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
        function checkalphabet($string) {
            $alphabet=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","v","s","t","u","w","x","y","z","1","2","3","4","5","6","7","8","9","0");
            for ($i=0; $i<strlen($string); $i++) {
                if(!in_array($string[$i],$alphabet)) {
                    return false;    
                }
            };
            return true;
        }
        function inserting_data($f3) {
            //create mapper
            $f3->set('object_mapper',$user=new DB\SQL\Mapper($f3->get('conn'),'accounts')); 
            $login=$_POST['username'];
            $email=$_POST['email'];
            $utf = \UTF::instance();
            //checking if username and password is no empty
            if ((!$f3->get('POST.username')=="")&&(!$f3->get('POST.password')=="")) {
                //checking if username has permitted characters
                if($this->checkalphabet($f3->get('POST.username'))) {
                    $f3->set('error1',""); 
                }
                else {
                    $f3->set('error1',"Proszę podać poprawną nazwę użytkownika!"); 
                } 
                //checking if username is not too long
                if ((($utf->strlen($f3->get('POST.username')))<($f3->get('max_login_len')))&&($f3->get('error1')=="")) {
                    //checking if password is not too long
                    if ((($utf->strlen($f3->get('POST.password'))))>($f3->get('max_password_len'))) {
                        $f3->set('error2',"Hasło jest zbyt długie!");
                    }   else {
                            //insert password and username into database
                            if (!($f3->get('object_mapper')->load(array('login=?',$f3->get('POST.username'))))==$f3->get('POST.username')) {
                                $f3->get('object_mapper')->login=$f3->get('POST.username');
                                $f3->get('object_mapper')->password=md5($f3->get('POST.password')); 
                                $f3->get('object_mapper')->save(); 
                                $f3->reroute('@login'); 
                            }
                            else{
                                $f3->set('error5',"Użytkownik już istnieje!");
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
        function postcreatechar($f3) {
            
            $f3->set('object_mapper_char',$user=new DB\SQL\Mapper($f3->get('conn'),'characters'));
            //$f3->set('object_mapper_acco',$user=new DB\SQL\Mapper($f3->get('conn'),'accounts'));

            if (!empty($_SESSION["login"])) {
                if (!empty($f3->get('POST.occupation'))&&(!empty($f3->get('POST.nickname')))) {
                    if($this->checkalphabet($f3->get('POST.nickname'))) {
                        $f3->get('object_mapper_char')->char_class=$f3->get('POST.occupation');
                        $f3->get('object_mapper_char')->nickname=$f3->get('POST.nickname');
                        //$f3->get('object_mapper_char')->user_id=($f3->get('object_mapper_acco')->select('user_id',['login = ?', $_SESSION["login"]]));
                        $f3->get('object_mapper_char')->save(); 
                    }
                    else {
                        $f3->set('creating_error1', "Proszę wpisać poprawną nazwę postaci!");
                    }
                } else {
                    $f3->set('creating_error2', "Proszę uzupełnić wszystkie pola!");
                }
                echo \Template::instance()->render('characters.html');
            }
        }
        function createchar($f3) {
            if (!empty($_SESSION["login"])) {
            echo \Template::instance()->render('characters.html');
            }
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