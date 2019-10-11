<?php
// Kickstart the framework
$f3=require('lib/base.php');
$f3->config('config.ini');

// Database connection 
$f3->set('conn',$db=new DB\SQL('mysql:host=localhost;port=3306;dbname=ryszardDB','root','',array(\PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES utf8;')));

session_start();

include('php/functions.php');



$f3->route('GET @home: /','home->gethome');

$f3->route('GET @missions: /missions','home->missions');

$f3->route('POST /choosemission','home->choosemission');

$f3->route('GET @createchar: /createchar','register->createchar');

$f3->route('POST /createchar','register->postcreatechar');

$f3->route('GET @login: /login','login->getlogin');

$f3->route('POST /login','login->postlogin');

$f3->route('POST @logintoserver: /logintoserver','login->logintoserver');

$f3->route('POST /logout','login->logout');

$f3->route('GET /register', 'register->displayregister');

$f3->route('POST /register','register->inserting_data');

$f3->run();
