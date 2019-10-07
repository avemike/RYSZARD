<?php
// Kickstart the framework
$f3=require('lib/base.php');
$f3->config('config.ini');

session_start();

$db=new DB\SQL(
	'mysql:host=localhost;port=3306;dbname=ryszardDB',
	'root',
	''
);

$f3->route('GET @home: /',
	function($f3) {
		if (empty($_SESSION["login"]) || empty($_SESSION["nickname"])){
			$f3->reroute('@login');
	 	}
		echo \Template::instance()->render('profile.html');
		// echo \Template::instance()->render('template.htm');
	}
);

$f3->route('GET @login: /login',
	function($f3) {
		global $db;
		if(!empty($_SESSION["login"])){
			if(empty($_SESSION["nickname"])){
				$f3->set('result',$db->exec('SELECT servers.server_id, char_id, level, nickname FROM servers left join characters on servers.server_id = characters.server_id where user_id=? or user_id IS NULL', $_SESSION["user_id"]));
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
);

$f3->route('POST /login',
	function($f3) {
		if (!empty($_SESSION["login"])){
			$f3->reroute('@login');
		}
		
		$f3->set('logintemplate', 'login.html');
		global $db;
		$user=new DB\SQL\Mapper($db,'accounts');
		if(!empty($_POST["login"])){
			$login=$_POST["login"];
			// $password=md5($_POST["password"]);
			$password=$_POST["password"];

			if(strlen($login)>$f3->get('max_login_len')){
				$loginErr="login or password incorrect";
			}
			else{
				if($user->load(array('login=?',$login))->login == $login && $user->load(array('login=?',$login))->password==$password){
					$_SESSION["login"]=$login;
					$_SESSION["user_id"]=$user->load(array('login=?',$login))->user_id;

					$f3->set('servers', 'servers.html');
					$f3->set('logintemplate', 'servers.html');
					$f3->set('result',$db->exec('SELECT servers.server_id, char_id, level, nickname FROM servers LEFT JOIN characters ON servers.server_id = characters.server_id WHERE user_id=? OR user_id IS NULL', $_SESSION["user_id"]));
				}
				else{
					$loginErr="login or password incorrect";
				}
			}
		}
		$f3->set('loginErr', $loginErr);
		echo \Template::instance()->render($f3->get('logintemplate'));
	}
);

$f3->route('POST @logintoserver: /logintoserver',
	function($f3){
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
				//do zrobienia tworzenie postaci
				// $f3->reroute('@createchar');
				$f3->reroute('@login');
			}
		}
		else{
			$f3->reroute('@login');
		}
	}
);

$f3->route('POST /logout',
	function($f3){
		session_unset();
		$f3->reroute('@login');
	}
);
$f3->run();
