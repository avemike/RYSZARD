<?php
class register {
    function displayregister($f3) {
        echo \Template::instance()->render('account/register.html');
    }
    function inserting_data($f3) {
        global $db;
        $object_mapper=new DB\SQL\Mapper($db,'accounts'); 
        $error=[];
        $login=$_POST['username'];
        $passw=$_POST['password1'];
        $passw2=$_POST['password2'];

        //check if passwords are same
        if($passw!=$passw2){
            $error[]="Hasła nie są identyczne";
        }
        //check if every field was filled
        if($login=="" || $passw=="" || $passw2==""){
            $error[]="Proszę wypełnić wszystkie pola";
        }
        //check if login was set properly
        if(!$this->checkalphabet($login)){
            $error[]="Proszę podać poprawną nazwę użytkownika!";
        }
        //check login length
        if(mb_strlen($login)>$f3->get('max_login_len')){
            $error[]="Login jest za długi";
        }
        //check password length
        if(mb_strlen($passw)>$f3->get('max_password_len')){
            $error[]="Hasło jest za długie";
        }
        //check if login already exist
        if($object_mapper->load(array('login=?',$login))){
            $error[]="Konto już istnieje";
        }
        //if no errors then create account
        if(empty($error)){
            $object_mapper->login=$login;
            $object_mapper->password=md5($passw); 
            $object_mapper->save(); 
            $f3->reroute('@login');
        }
        $f3->set('error',$error);
        echo \Template::instance()->render('account/register.html');    
    }

    function getCharacterIcons($f3) {
        // Return array of all possible (2 per class-race) paths of icons correlated with specific race and class 
        // Works with characterIcons.html template
        $class = $_GET['class'];
        $race = $_GET['race'];

        $result = array("public/images/".$race."/".$class."1.jpg", "public/images/".$race."/".$class."2.jpg");
        $f3->set('result', $result);

        echo \Template::instance()->render('characterCreation/characterIcons.html');
    }
    function postcreatechar($f3) {
        global $db;
        // $character_classes=array("informatyk", "mechatronik", "elektronik");
        $character_races=array("kobieta","karzel","czlowiek","zyd");
        
        // $f3->set('object_mapper_char', new DB\SQL\Mapper($f3->get('conn'),'characters'));

        if (!empty($_SESSION["login"]) && !empty($_SESSION["server"])) {
            $class = $f3->get('POST.class');
            $race = $f3->get('POST.race');
            $nickname = $f3->get('POST.nickname');
            $icon = $f3->get('POST.icon');
            $server = $f3->get('SESSION.server');
            $user_id = $f3->get('SESSION.user_id');

            $class_id = $db->exec('SELECT class_id FROM classes WHERE class_name=?', $class)[0]['class_id'];

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

        }
        else{
            echo 'xd';
        }
    }
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
}
?>