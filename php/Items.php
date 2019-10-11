<?php

class items {
        function item_shop($f3) {
            global $db;      
            if(empty($_SESSION["nickname"])){
                $f3->reroute('@login');
            }
            //if items are already generated
            if($result=$db->exec('
            SELECT char_id, item_name, item_description,
            item_icon, value, strength, hp, dex,
            luck, every_attrib
            FROM items LEFT JOIN item_template 
            on items.item_template_id = item_template.item_template_id 
            WHERE item_status = 1 AND char_id=?',$_SESSION["char_id"])){
                $f3->set('items_to_buy', $result);
            }
            //generate new items
            else {
                $item_templates = $db->exec('SELECT item_template_id FROM item_template ORDER BY rand() LIMIT 6');
                
                for($i=0; $i<6; $i++) {
                    $hp = ($_SESSION["level"] - rand(1, $_SESSION["level"])) * rand(5,10);  //temp algorithm
                    $str = rand(0, $_SESSION["level"]);  //everything here is temporary
                    $dex = rand(0, $_SESSION["level"]);
                    $int = rand(0, $_SESSION["level"]);
                    $luck = rand(0, $_SESSION["level"]);
                    $every_attrib = rand(0, $_SESSION["level"]/3);
                    $value = rand(1, ($hp + $str + $dex + $int + $luck + $every_attrib)) * rand(5, 15) * rand(1, $_SESSION["level"]);

                    $db->exec(" INSERT INTO items (char_id, value, strength, 
                        hp, dex, intelligence, luck, every_attrib,	
                        item_status, item_template_id)
                     values (?, ?, ?, ?, ?, ?, ?, ?, 1, ?)", array($_SESSION["char_id"], $value,
                        $str, $hp, $dex, $int, $luck, $every_attrib, $item_templates[$i]["item_template_id"]));
                }
                $items = $db->exec("SELECT char_id, value,
                item_name, item_description, item_icon, 
                hp, dex, strength, intelligence, luck, every_attrib
                FROM items LEFT JOIN mission_template 
                on items.item_template_id = item_template.item_template_id 
                WHERE char_id=?",$_SESSION["char_id"]);


                $f3->set('items_to_buy', json_encode($returned_value));
            }
            echo \Template::instance()->render('itemShop.html');
        }
    }