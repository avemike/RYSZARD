<?php 
class login{
    function getlogin($f3){
        //if is logged
        if(!empty($_SESSION["login"])){
            //if didnt choose character yet
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
        global $db;
        $login_template='login.html';
        $user=new DB\SQL\Mapper($db,'accounts');

        if(!empty($_POST["login"])){
            $login=$_POST["login"];
            $password=md5($_POST["password"]);

            if(strlen($login)>$f3->get('max_login_len')){
                $f3->set('loginErr', 'Za długi login');
                echo \Template::instance()->render($login_template);
                return;
            }

            // check if user is not in database
            if( !($user->load(array('login=?',$login))->login == $login && $user->load(array('login=?',$login))->password==$password)) {
                $f3->set('loginErr', 'Nieprawidłowe dane logowania');
                echo \Template::instance()->render($login_template);
                return;
            }

            // SESSION: include login and userid
            $_SESSION["login"]=$login;
            $_SESSION["user_id"]=$user->load(array('login=?',$login))->user_id;

            $f3->set('servers', 'servers.html');
            $login_template='servers.html';
            $this->getservers($f3);
        }
        echo \Template::instance()->render($login_template);
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
            if($result=$db->exec('SELECT char_id, nickname, race, icon, characters.server_id as server, level, currency, exp, exp_to_next_lv, char_class, class_name FROM servers LEFT JOIN characters ON servers.server_id = characters.server_id LEFT JOIN classes ON classes.class_id = characters.char_class WHERE servers.server_id = ? AND user_id = ?', array($_POST["serverno"],$_SESSION['user_id']))){
                foreach($result[0] as $key => $value){
                    $_SESSION[$key]=$value;
                }

                $f3->reroute('@home');
            }
            else{
                $_SESSION["server"]=$_POST["serverno"];
                echo \Template::instance()->render('characterCreation.html');
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
?>