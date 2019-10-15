<?php
    class settings {
        function page($f3) {
            global $db;      
            if(empty($_SESSION["nickname"])){
                $f3->reroute('@login');
            }
            
        	echo \Template::instance()->render('settings.html');
        }
    }

?>