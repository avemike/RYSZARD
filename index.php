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
		if (empty($_SESSION["login"])){
			$f3->reroute('@login');
	 	}
		echo \Template::instance()->render('profile.html');
		// echo \Template::instance()->render('template.htm');
	}
);

$f3->route('GET @login: /login',
	function($f3) {
		echo \Template::instance()->render('login.html');
	}
);

$f3->route('POST /login',
	function($f3) {
		
		global $db;
		$user=new DB\SQL\Mapper($db,'accounts');
		if(!empty($_POST["login"])){
			$login=$_POST["login"];
			// $password=md5($_POST["password"]);
			$password=$_POST["password"];

			if (strlen($login)>$f3->get('max_login_len')){
				$loginErr="login or password incorrect";
			}
			else{
				if($user->load(array('login=?',$login))->login == $login && $user->load(array('login=?',$login))->password==$password){
					$_SESSION["login"]=$login;
					$_SESSION["user_id"]=$user->load(array('login=?',$login))->user_id;
					$f3->reroute('@home');
				}
				else{
					$loginErr="login or password incorrect";
				}
			}
			
		}
		$f3->set('loginErr', $loginErr);
		echo \Template::instance()->render('login.html');
	}
);
$f3->route('POST /logout',
	function($f3){
		session_unset();
		$f3->reroute('@login');
	}
);
$f3->run();
