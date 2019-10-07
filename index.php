<?php
// Kickstart the framework
$f3=require('lib/base.php');
$f3->config('config.ini');

// Database connection 
$f3->set('conn',$db=new DB\SQL('mysql:host=localhost;port=3306;dbname=ryszardDB','root',''));

$f3->route('GET /',
	function($f3) {
		echo \Template::instance()->render('main.html');
	}
);


$f3->route('POST /registration',
	function($f3) {
		include('registration.php');
		echo \Template::instance()->render('main.html');
	}
);

$f3->run();
