<?php
// Kickstart the framework
$f3=require('lib/base.php');
$f3->config('config.ini');
include('php/functions.php');
session_start();


// Database connection 
$f3->set('conn',$db=new DB\SQL('mysql:host=localhost;port=3306;dbname=ryszardDB','root',''));



$f3->route('GET @home: /','home->gethome');

$f3->route('GET @login: /login','login->getlogin');

$f3->route('POST /login','login->postlogin');

$f3->route('POST @logintoserver: /logintoserver','login->logintoserver');

$f3->route('POST /logout','login->logout');

$f3->route('GET /register',
	function($f3) {
		echo \Template::instance()->render('register.html');
	}
);

$f3->route('POST /register','register->inserting_data');

$f3->run();
