<?php
// Kickstart the framework
$f3=require('lib/base.php');
$f3->config('config.ini');
include('php/functions.php');
// Database connection 
$f3->set('conn',$db=new DB\SQL('mysql:host=localhost;port=3306;dbname=ryszardDB','root',''));

$f3->route('GET /',
	function($f3) {
		echo \Template::instance()->render('main.html');
	}
);


$f3->route('POST /registration','registration->inserting_data');

$f3->run();
