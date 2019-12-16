<?php
// Kickstart the framework
$f3=require('lib/base.php');
$f3->config('config.ini');

// Database connection 
$f3->set('conn',$db=new DB\SQL('mysql:host=localhost;port=3306;dbname=ryszardDB','root',''));

//session
session_start();

//update currency value if user is logged to character
if(!empty($_SESSION['char_id'])){
	$f3->set('newcurrency', $db->exec('SELECT currency FROM characters WHERE char_id=?', $_SESSION['char_id'])[0]['currency']);
}

//include files from file_list array
$file_list=array('fight','home','items','login','register', 'missions', 'mail','settings');
$path='php/';
$ext='.php';
foreach($file_list as $file){
	include($path.$file.$ext);
}

//main page
$f3->route('GET @home: /','home->mainPage');

//profile
$f3->route('GET @profile: /profile', 'home->profile');

//missions
$f3->route('GET @missions: /missions','home->missions');
$f3->route('POST /choosemission','missions->choosemission');

//both shops and their functions
$f3->route('GET @armoryShop: /armory', 'home->armoryShop');
$f3->route('GET @accessoryShop: /accessories', 'home->accessoryShop');

$f3->route('POST /itemShop/buyItem/@type', 'items->item_buy');
$f3->route('POST /itemShop/sellItem/@type', 'items->item_sell');
$f3->route('POST /itemShop/reroll/@type', 'items->reroll');
$f3->route('POST /itemShop/equipitem', 'items->equip');
$f3->route('POST /itemShop/unequipitem', 'items->unequip');

//mail
$f3->route('GET @inbox: /inbox','mail->getinbox');

$f3->route('GET @outbox: /outbox','mail->getoutbox');
$f3->route('GET @mail: /mail','mail->getmail');
$f3->route('POST /mail','mail->postmail');

//login
$f3->route('GET @login: /login','login->getlogin');
$f3->route('POST /login','login->postlogin');
$f3->route('POST @logintoserver: /logintoserver','login->logintoserver');

//register
$f3->route('GET /register',
function($f3) {
	echo \Template::instance()->render('register.html');
	}
); 
$f3->route('POST /register','register->inserting_data');

//create char
$f3->route('GET @createchar: /createchar','register->createchar');
$f3->route('GET /getCharacterIcons', 'register->getCharacterIcons');
$f3->route('POST /createchar','register->postcreatechar');

// Settings related
$f3->route('GET /settings','settings->page');
$f3->route('POST /changePassword','settings->change_password');

//logout
$f3->route('POST /logout','login->logout');

$f3->run();
