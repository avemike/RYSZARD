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
?>