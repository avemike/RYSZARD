<?php
    class items {
        //item_status
        //0 - in inventory
        //1 - in shop
        //
        function item_shop($f3) {
            global $db;      
            if(empty($_SESSION["nickname"])){
                $f3->reroute('@login');
            }

            $CheckItemAmount = $db->exec('SELECT *
            FROM items LEFT JOIN item_template 
            on items.item_template_id = item_template.item_template_id 
            WHERE item_status = 1 AND char_id=?',$_SESSION["char_id"]);

            // if there is 6 > items
            for($i = count($CheckItemAmount); $i < 6; $i++) {
                $this->generate_item($i);
            }

            $itemsToBuy=$db->exec('SELECT *
            FROM items LEFT JOIN item_template 
            on items.item_template_id = item_template.item_template_id 
            WHERE item_status = 1 AND char_id=? ORDER BY item_place',$_SESSION["char_id"]);
            
            $itemsInventory=$db->exec('SELECT * 
            FROM items LEFT JOIN item_template 
            on items.item_template_id = item_template.item_template_id 
            WHERE item_status = 0 AND char_id=?',$_SESSION["char_id"]);

            $f3->set('items_to_buy', $itemsToBuy);
            $f3->set('items_inventory', $itemsInventory);

            echo \Template::instance()->render('itemShop.html');
        }
        function item_buy($f3) {
            // check if player has required gold
            global $db;
            if(empty($_SESSION["nickname"])){
                $f3->reroute('@login');
            }
            $item_id = $f3->get('POST.item_id');

            if($itemValue = $db->exec("SELECT value, item_place FROM items 
                    WHERE item_id=? AND char_id=? AND item_status=1", array($item_id, $_SESSION['char_id']))) {

                $user=new DB\SQL\Mapper($db,'characters');
                $user->load(array('char_id=?',$_SESSION["char_id"]));

                if($user->currency-$itemValue[0]['value']>=0 && count($db->exec('SELECT item_id FROM items WHERE item_status=0 and char_id=?', $_SESSION["char_id"]))<8) {
                    // get rid of money from account
                    $user->currency-=$itemValue[0]['value'];
                    $user->save();

                    // change state of item
                    $db->exec('UPDATE items SET item_status=0, item_place=null WHERE item_id=?', $item_id);

                    $_SESSION['currency']=$user->currency;

                    //generate new item
                    $this->generate_item($itemValue[0]["item_place"]);
                }
            }
            // render shop
            $f3->reroute('@itemShop');
        }
        function item_sell($f3){
            global $db;
            if(empty($_SESSION["nickname"])){
                $f3->reroute('@login');
            }
            $item_id = $f3->get('POST.item_id');

            $user=new DB\SQL\Mapper($db,'characters');
            $user->load(array('char_id=?',$_SESSION["char_id"]));

            if($result = $db->exec('SELECT * FROM items WHERE char_id=? AND item_id=? AND item_status=0', array($_SESSION["char_id"], $item_id))){
                $user->currency+=round($result[0]["value"]*8/10);
                $user->save();

                $db->exec('DELETE FROM items WHERE item_id=?', $item_id);
                
                $_SESSION['currency']=$user->currency;
            }
            $f3->reroute('@itemShop');
        }
        function generate_item($place) {
            global $db;

            $item_templates = $db->exec('SELECT item_template_id FROM item_template ORDER BY rand() LIMIT 1');
            
            $hp = ($_SESSION["level"] - rand(1, $_SESSION["level"])) * rand(5,10);  //temp algorithm
            $str = rand(0, $_SESSION["level"]);  //everything here is temporary
            $dex = rand(0, $_SESSION["level"]);
            $int = rand(0, $_SESSION["level"]);
            $luck = rand(0, $_SESSION["level"]);
            $every_attrib = rand(0, $_SESSION["level"]/3);
            $value = rand(1, ($hp + $str + $dex + $int + $luck + $every_attrib)) * rand(1, 5);

            $db->exec(" INSERT INTO items (char_id, value, strength, 
                hp, dex, intelligence, luck, every_attrib,	
                item_status, item_template_id, item_place)
                values (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?)", array($_SESSION["char_id"], $value,
                $str, $hp, $dex, $int, $luck, $every_attrib, $item_templates[0]["item_template_id"], $place));
        }
    }

?>