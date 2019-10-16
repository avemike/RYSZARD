<?php
// Kickstart the framework
$f3=require('lib/base.php');
$f3->config('config.ini');

// Database connection 
$f3->set('conn',$db=new DB\SQL('mysql:host=localhost;port=3306;dbname=ryszardDB','root','',array(\PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES utf8;')));

session_start();

include('php/functions.php');
include('php/Items.php');
include('php/Settings.php');


$f3->route('GET @home: /','home->gethome');

$f3->route('GET @missions: /missions','home->missions');

$f3->route('POST /choosemission','home->choosemission');

$f3->route('GET @createchar: /createchar','register->createchar');

$f3->route('POST /createchar','register->postcreatechar');

$f3->route('POST @logintoserver: /logintoserver','login->logintoserver');


$f3->route('POST /logout','login->logout');

$f3->route('GET @itemShop: /itemShop', 'items->item_shop');
$f3->route('POST /itemShop/sellItem', 'items->item_sell');
$f3->route('POST @buyItem: /itemShop/buyItem', 'items->item_buy');

$f3->route('GET /register',
function($f3) {
	echo \Template::instance()->render('register.html');
	}
); 
$f3->route('POST /register','register->inserting_data');

$f3->route('GET @login: /login','login->getlogin');
$f3->route('POST /login','login->postlogin');

// Settings related
$f3->route('GET /settings','settings->page');
$f3->route('POST @changePassword: /settings/changePassword','settings->change_password');


$f3->run();
