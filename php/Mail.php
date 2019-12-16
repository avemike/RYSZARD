<?php
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