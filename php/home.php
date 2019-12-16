<?php
class home{
    function mainPage($f3){
        if(empty($_SESSION["login"]) || empty($_SESSION["nickname"])){
            $f3->reroute('@login');
        }
        global $db;
        $user=new DB\SQL\Mapper($db,'characters');
        $user->load(array('char_id=?',$_SESSION["char_id"]));
        $_SESSION["currency"]=$user->currency;
        

        echo \Template::instance()->render('mainpage.html');
    }
    function profile($f3){
        $inv = new items;
        $inv->show_inventory();
        $inv->show_equipped();
        $inv->get_stats($_SESSION["char_id"]);
        
        echo \Template::instance()->render('profile.html');
    }
    function missions($f3){  
        if(empty($_SESSION["nickname"])){
            $f3->reroute('@login');
        }
        $missions = new missions;
        $missions->missionPage();
    }
    function armoryShop($f3){
        $shop=new items;
        $shop->item_shop("armory");
    }
    function accessoryShop($f3){
        $shop=new items;
        $shop->item_shop("accessory");
    }
}
class mail{
    function getinbox($f3){
        global $db;
        $this->inbox_mails();
        echo \Template::instance()->render('mail/inbox.html');
    }
    function getoutbox($f3){
        global $db;
        $result = $db->exec('SELECT mail_date, mail_title, mail_receiver, mail_content, nickname FROM mail LEFT JOIN characters ON mail.mail_receiver=characters.char_id WHERE mail_sender=? ORDER BY mail_date DESC', array($_SESSION["char_id"]));
        $f3->set('result', $result);
        echo \Template::instance()->render('mail/outbox.html');
    }
    function getmail($f3){
        echo \Template::instance()->render('mail/mail.html');    
    }
    function postmail($f3){
        global $db;
        if($to_char = $db->exec('SELECT char_id FROM characters WHERE nickname=? AND server_id=?', array($_POST["address"], $_SESSION["server"]))){
            $db->exec('INSERT INTO mail (mail_receiver, mail_content, mail_title, mail_sender) values (?, ?, ?, ?)', array($to_char[0]["char_id"], htmlspecialchars($_POST["content"]), $_POST["title"], $_SESSION["char_id"])); 
        }
        // else{
        //     $f3->set('mailerror', 'Podany użytkownik nie istnieje');
        // }
        // $f3->reroute('home');

        $this->inbox_mails();
        echo \Template::instance()->render('mail/inbox.html');
            
    }
    function inbox_mails(){
        global $f3;
        global $db;
        $result = $db->exec('SELECT mail_date, mail_title, mail_sender, mail_content, nickname FROM mail LEFT JOIN characters ON mail.mail_sender=characters.char_id WHERE mail_sender!=:char_id AND mail_receiver=:char_id ORDER BY mail_date DESC', array('char_id'=>$_SESSION["char_id"]));
        $f3->set('result', $result);
    }
}
?>