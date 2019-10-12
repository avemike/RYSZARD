<?php

class items {
        function item_shop($f3) {
            global $db;      
            if(empty($_SESSION["nickname"])){
                $f3->reroute('@login');
            }
            //if items are already generated
            // if($result=


            $result = $db->exec('SELECT *
            FROM items LEFT JOIN item_template 
            on items.item_template_id = item_template.item_template_id 
            WHERE item_status = 1 AND char_id=?',$_SESSION["char_id"]);

            // if there is 6 > items
            for($i = count($result); $i < 6; $i++) {
                $this->generate_item($i);
            }

            $final_result=$db->exec('SELECT *
            FROM items LEFT JOIN item_template 
            on items.item_template_id = item_template.item_template_id 
            WHERE item_status = 1 AND char_id=? ORDER BY item_place',$_SESSION["char_id"]);
            
            $f3->set('items_to_buy', $final_result);


            // }
            // //generate new items
            // else {
                
            //     for($i=0; $i<6; $i++) {
            //         $this->generate_item();           
            //     }
            //     $items = $db->exec("SELECT char_id, item_id, value,
            //         item_name, item_description, item_icon, 
            //         hp, dex, strength, intelligence, luck, every_attrib
            //         FROM items LEFT JOIN mission_template 
            //         on items.item_template_id = item_template.item_template_id 
            //         WHERE char_id=?",$_SESSION["char_id"]);


            //     $f3->set('items_to_buy', $items);
            // }

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
                    WHERE item_id =? AND char_id =?", array($item_id, $_SESSION['char_id']))) {
                
                // check if session is wrote  enough gold
                // it will minimalize number of request to server
                // : check session -> if okey, then check database
                if($_SESSION['currency'] - $itemValue[0]['value'] > 0) {
                    $bank_state = $db->exec("SELECT currency FROM characters
                        WHERE char_id=?", $_SESSION['char_id']);
                    
                    // SHOULD CHECK IF EQ IS FULL
                    // !!!!!!!!!

                    // get rid of money from account
                    $db->exec("UPDATE characters SET currency=?
                        WHERE char_id=?", array($_SESSION['currency'], $_SESSION['char_id']));
                    // get rid of money from session
                    $_SESSION['currency'] = $_SESSION['currency'] - $itemValue[0]['value'];

                    // change state of item
                    $db->exec('UPDATE items SET item_status = 0 WHERE item_id=?', $item_id);

                    //generate new item
                    $this->generate_item($itemValue[0]["item_place"]);

                    // render shop
                    $f3->reroute('@itemShop');

                }
            }
            // echo $itemValue;


            // if($result=$db->exec('
            // SELECT char_id, item_name, item_description,
            // item_icon, value, strength, hp, dex,
            // luck, intelligence, every_attrib
            // FROM items LEFT JOIN item_template 
            // on items.item_template_id = item_template.item_template_id 
            // WHERE item_status = 1 AND char_id=?',$_SESSION["char_id"])){
            //     $f3->set('items_to_buy', $result);
            // }
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