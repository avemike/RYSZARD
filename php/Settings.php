<?php
    class settings {
        function page($f3) {
            if(empty($_SESSION["nickname"])){
                $f3->reroute('@login');
            }
        	echo \Template::instance()->render('settings.html');
        }
        function change_password($f3) {
            if(empty($_SESSION["nickname"])){
                $f3->reroute('@login');
            }

            global $db;
            $old_password = $f3->get("POST.old_password");
            $new_password = $f3->get("POST.new_password");
            
            // here should be check if it's proper password
            
            // check if one of them (or both) are existing
            if(empty($old_password) || empty($new_password)) {
                // return error to do
                $f3->set("changed_password_wrong", true);                 
                
            }
            // check if old password is correlated with login
            elseif($user_id_row = $db->exec("SELECT user_id FROM accounts WHERE login=? AND password=?",
            array($_SESSION["login"], md5($old_password)))) {
                $user_id = $user_id_row[0]['user_id']; 
                
                // modify current password
                $db->exec("UPDATE accounts
                SET password = ?
                WHERE user_id = ?", array(md5($new_password), $user_id));

                $f3->set("changed_password_success", true);
            }
            else{
                $f3->set("changed_password_wrong", true);
            }
            echo \Template::instance()->render('settings.html');
        }
    }
?>