<?php
// Kickstart the framework
$f3=require('lib/base.php');

$f3->route('GET /',
	function($f3) {
		echo \Template::instance()->render('ui/profile.html');
		// echo \Template::instance()->render('template.htm');
	}
);


$f3->run();
