<?php
class register {
    function checkalphabet($string) {
        $polish=array("ą","Ą","ż","Ż","ś","Ś","ź","Ź","ć","Ć","ę","Ę","ń","Ń","ó","Ó","ł","Ł");
        $alphabet=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","v","s","t","u","w","x","y","z");
        $numbers=range(0,9);
        $special_chars=array("-","_","+","=",";","/","!","@","#","$","%","^","&","*","(",")",",",".","[","]","{","}", " ");
        $allowed=array_merge($polish,array_merge($alphabet,array_merge($numbers,$special_chars)));
        // for ($i=0; $i<strlen($string); $i++) {
        //     if(!in_array(strtolower($string[$i]),$allowed)) {
        //         return false;
        //     }
        // };
        $space_counter=0;
        $array_given=str_split($string);
        foreach($array_given as $char){
            if(!in_array(strtolower($char),$allowed)) {
                return false;
            }
            if($char==" "){
                $space_counter++;
            }
        }
        if($space_counter==mb_strlen($string)){
            return false;
        }
        return true;
    }
    function displayregister($f3) {
        echo \Template::instance()->render('register.html');
    }
    function inserting_data($f3) {
        //create mapper
        $f3->set('object_mapper',$user=new DB\SQL\Mapper($f3->get('conn'),'accounts')); 
        $login=$_POST['username'];
        $email=$_POST['email'];
        $utf = \UTF::instance();
        //checking if username and password is no empty
        if ((!$f3->get('POST.username')=="")&&(!$f3->get('POST.password')=="")) {
            //checking if username has permitted characters
            if($this->checkalphabet($f3->get('POST.username'))) {
                $f3->set('error1',""); 
            }
            else {
                $f3->set('error1',"Proszę podać poprawną nazwę użytkownika!"); 
            } 
            //checking if username is not too long
            if (((mb_strlen($f3->get('POST.username')))<=($f3->get('max_login_len')))&&($f3->get('error1')=="")) {
                //checking if password is not too long
                if (((mb_strlen($f3->get('POST.password'))))>($f3->get('max_password_len'))) {
                    $f3->set('error2',"Hasło jest zbyt długie!");
                }   else {
                        //insert password and username into database
                        if (!($f3->get('object_mapper')->load(array('login=?',$f3->get('POST.username'))))==$f3->get('POST.username')) {
                            $f3->get('object_mapper')->login=$f3->get('POST.username');
                            $f3->get('object_mapper')->password=md5($f3->get('POST.password')); 
                            $f3->get('object_mapper')->save(); 
                            $f3->reroute('@login'); 
                        }
                        else{
                            $f3->set('error5',"Użytkownik już istnieje!");
                        }
                }
            }   else {
                    $f3->set('error3',"Nazwa użytkownika jest zbyt długa!"); 
                };        
            
        } else {
            $f3->set('error4',"Proszę wypełnić wszystkie pola!");
        };  
        echo \Template::instance()->render('register.html');
        
    }
    function getCharacterIcons($f3) {
        // Return array of all possible (2 per class-race) paths of icons correlated with specific race and class 
        // Works with characterIcons.html template

        $class = $_GET['class'];
        $race = $_GET['race'];

        $result = array("ui/images/".$race."/".$class."1.jpg", "ui/images/".$race."/".$class."2.jpg");
        $f3->set('result', $result);

        echo \Template::instance()->render('characterIcons.html');
    }

    function postcreatechar($f3) {
        global $db;
        // $character_classes=array("informatyk", "mechatronik", "elektronik");
        $character_races=array("kobieta","karzel","czlowiek","zyd");
        
        // $f3->set('object_mapper_char', new DB\SQL\Mapper($f3->get('conn'),'characters'));

        if (!empty($_SESSION["login"]) && !empty($_SESSION["server"])) {
            $occupation = $f3->get('POST.occupation');
            $race = $f3->get('POST.race');
            $nickname = $f3->get('POST.nickname');
            $icon = $f3->get('POST.icon');
            $server = $f3->get('SESSION.server');
            $user_id = $f3->get('SESSION.user_id');

            $class_id = $db->exec('SELECT class_id FROM classes WHERE class_name=?', $occupation)[0]['class_id'];

            if ($class_id && !empty( $nickname ) && (in_array( $race, $character_races ))) {
                $nick_already_used = $db->exec('SELECT char_id FROM characters WHERE nickname=? AND server_id=? LIMIT 1', array($nickname, $server));
                // if nickname is already used
                if ($nick_already_used) {

                    $f3->set('creating_error3', "Postać o takim nicku już istnieje!");
                }
                // check if user has already linked character on this server
                elseif (empty($db->exec('SELECT char_id FROM characters WHERE server_id=? AND user_id=?', array($server, $user_id)))) {
                    
                    // check if nickname is valid
                    if( $this->checkalphabet($nickname) && mb_strlen($nickname) <= $f3->get('max_nickname_len')) { 
                        // for future use
                        $exp_to_next_level = 200;
                        $attack = 10;
                        $defence = 10;
                        $vit = 100;
                        //
                        $db->exec('INSERT INTO characters values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                            array(null, $user_id, $server, $class_id, $nickname, "0", "1", "0", $exp_to_next_level,
                            $attack, $defence, "10", "10", $vit, "10", "10", $race, $icon, null));
                        echo 'success';
                        }
                    else {
                        $f3->set('creating_error1', "Proszę wpisać poprawną nazwę postaci!");
                    }
                } else {
                    $f3->set('creating_error4', "Twoja postać na tym serwerze już istnieje!");
                }
            } else {
                $f3->set('creating_error2', "Proszę uzupełnić wszystkie pola!");
            }

            // echo \Template::instance()->render('characters.html');
        }
        else{
            echo 'xd';
            // $f3->reroute("@login");
        }
    }
}
?>