<?php

    class registration {
        function inserting_data($f3) {
            //create mapper
            $f3->set('object_mapper',$user=new DB\SQL\Mapper($f3->get('conn'),'accounts')); 
            $utf = \UTF::instance();

            $login=$_POST['username'];
            $email=$_POST['email'];
            $alphabet=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","v","s","t","u","w","x","y","z","1","2","3","4","5","6","7","8","9","0");
            $errors=array();

            //checking if username and password is no empty
            if ((!$f3->get('POST.username')=="")&&(!$f3->get('POST.password')=="")) {
                //checking if username has permitted characters
                for ($i=0; $i<($utf->strlen($f3->get('POST.username'))); $i++) {
                    if(!in_array($login[$i],$alphabet)) {
                        $errors[0]=null;
                        $errors[0]="Proszę podać poprawną nazwę użytkownika!";    
                    }
                }; 
                //checking if username is not too long
                if ((($utf->strlen($f3->get('POST.username')))<($f3->get('max_login_len')))&&($errors[0]==null)) {
                    //checking if password is not too long
                    if ((($utf->strlen($f3->get('POST.password'))))>($f3->get('max_password_len'))) {
                        $errors[2]="Hasło jest zbyt długie!";
                    }   else {
                            //insert password and username into database
                            $f3->get('object_mapper')->login=$f3->get('POST.username');
                            $f3->get('object_mapper')->password=md5($f3->get('POST.password')); 
                            $f3->get('object_mapper')->save();  
                        }
                }   else {
                        $errors[1]="Nazwa użytkownika jest zbyt długa!"; 
                    };        
                
            } else {
                $errors[3]="Proszę wypełnić wszystkie pola!";
            };  
            echo \Template::instance()->render('main.html');
        }
    }
    
    // ***********THIS IS FOR MAIL -> DON'T DELETE THIS**************    
    /* else if (!$f3->get('POST.email')=="") { //checking if email is no empty 
        //checking if email is correct
        if (filter_var($f3->get('POST.email'), FILTER_VALIDATE_EMAIL)) {
            //insert email into database
            $f3->get('object_mapper')->email=$f3->get('POST.email');
        }   else {
                $f3->set('error',"Podaj poprawny email!");
                echo "Mail niepoprawny";
            }   
    } */

?>