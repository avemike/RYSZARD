<?php
    class items {
        //item_status
        //0 - in inventory
        //1 - in shop
        //2 - equipped

        //item_class
        //0 everyone
        //1 informatyk
        //2 mechatronik
        //3 elektronik

        //item_type
        //0 bron
        //1 armor
        //2 tarcza
        //3 helm
        //4 buty
        //5 rekawice
        //6 amulet
        function item_shop($shop_type) {
            global $f3;
            global $db;
            if(empty($_SESSION["nickname"])){
                $f3->reroute('@login');
            }
            if($shop_type=="armory"){
                $from=0;
                $to=3;
            }
            else{
                $from=4;
                $to=6;
            }

            $CheckItemAmount = $db->exec('SELECT *
            FROM items LEFT JOIN item_template 
            on items.item_template_id = item_template.item_template_id 
            WHERE item_status=1 AND item_type>=? AND item_type<=? AND char_id=?',array($from, $to, $_SESSION["char_id"]));

            // if there is 6 > items
            for($i = count($CheckItemAmount); $i < 6; $i++) {
                $this->generate_item($i, $from, $to);
            }

            $itemsToBuy=$db->exec('SELECT *
            FROM items LEFT JOIN item_template 
            on items.item_template_id = item_template.item_template_id 
            WHERE item_status=1 AND item_type>=? AND item_type<=? AND char_id=? ORDER BY item_place',array($from, $to, $_SESSION["char_id"]));
            
        
            $this->show_inventory();

            $f3->set('items_to_buy', $itemsToBuy);
            $f3->set('item_class', array('everyone','informatyk','mechatronik','elektronik'));
            $f3->set('shop_type', $shop_type);

            echo \Template::instance()->render('itemShop.html');
        }
        function item_buy($f3) {
            global $db;
            if(empty($_SESSION["nickname"])){
                $f3->reroute('@login');
            }
            $item_id = $f3->get('POST.item_id');

            if($f3->get('PARAMS.type')=="armory"){
                $from=0;
                $to=3;
                $reroute="@armoryShop";
            }
            else{
                $from=4;
                $to=6;
                $reroute="@accessoryShop";
            }

            if($itemValue = $db->exec("SELECT value, item_place FROM items 
                    WHERE item_id=? AND char_id=? AND item_status=1", array($item_id, $_SESSION['char_id']))) {

                $user=new DB\SQL\Mapper($db,'characters');
                $user->load(array('char_id=?',$_SESSION["char_id"]));

                if($user->currency-$itemValue[0]['value']>=0 && count($db->exec('SELECT item_id FROM items WHERE item_status=0 and char_id=?', $_SESSION["char_id"]))<8) {
                    // get rid of money from account
                    $user->currency-=$itemValue[0]['value'];
                    $user->save();

                    // change state of item (item_id, to item status, from item status)
                    $this->change_item_status($item_id, 0);

                    $_SESSION['currency']=$user->currency;


                    //generate new item
                    $this->generate_item($itemValue[0]["item_place"], $from, $to);
                }
            }
            // render shop
            $f3->reroute($reroute);

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
            if($f3->get('PARAMS.type')=="armory"){
                $f3->reroute('@armoryShop');
            }
            else{
                $f3->reroute('@accessoryShop');
            }
        }
        function reroll($f3){
            global $db;
            if(empty($_SESSION["nickname"])){
                $f3->reroute('@login');
            }

            if($f3->get('PARAMS.type')=="armory"){
                $from=0;
                $to=3;
                $reroute="@armoryShop";
            }
            else{
                $from=4;
                $to=6;
                $reroute="@accessoryShop";
            }

            $db->exec('DELETE items FROM items LEFT JOIN item_template 
            ON items.item_template_id = item_template.item_template_id WHERE item_status=1 AND char_id=? AND item_type>=? AND item_type<=?', array($_SESSION["char_id"], $from, $to));

            $f3->reroute($reroute);
        }
        function equip($f3){
            global $db;
            $item_id=$_POST['item_id'];
            $item_equiped=false;

            if($inv = $db->exec('SELECT * FROM items LEFT JOIN item_template 
            ON items.item_template_id = item_template.item_template_id 
            WHERE item_id=? AND item_status=0 AND char_id=?', array($item_id, $_SESSION["char_id"]))){

                if($equipped = $db->exec('SELECT * FROM items LEFT JOIN item_template 
                ON items.item_template_id = item_template.item_template_id 
                WHERE item_type=? AND item_status=2 AND char_id=?', array($inv[0]['item_type'], $_SESSION["char_id"]))){
                    $item_equiped=true;
                }

                $this->change_item_status($inv[0]['item_id'], 2);
                if($item_equiped){
                    $this->change_item_status($equipped[0]['item_id'], 0);
                }
            }

            $f3->reroute('@profile');
        }
        function unequip($f3){
            global $db;
            $item_id=$_POST['item_id'];
            if($db->exec('SELECT * FROM items WHERE char_id=? AND item_id=? AND item_status=2', array($_SESSION["char_id"], $item_id)) && count($db->exec('SELECT * FROM items WHERE char_id=? AND item_status=0', $_SESSION["char_id"]))<8){
                $this->change_item_status($item_id, 0);
            }


            $f3->reroute('@profile');
        }
        function generate_item($place, $from, $to) {
            global $db;

            $item_templates = $db->exec('SELECT item_template_id, item_type FROM item_template WHERE (item_class=? OR item_class=0) AND item_type>=? AND item_type<=? ORDER BY rand() LIMIT 1',array($_SESSION["char_class"], $from, $to));
            
            $vit = rand(0, $_SESSION["level"]);
            // $vit = ($_SESSION["level"] - rand(1, $_SESSION["level"])) * rand(5,10);  //temp algorithm
            $str = rand(0, $_SESSION["level"]);  //everything here is temporary
            $dex = rand(0, $_SESSION["level"]);
            $int = rand(0, $_SESSION["level"]);
            $luck = rand(0, $_SESSION["level"]);
            $every_attrib = rand(0, $_SESSION["level"]/3);
            if(in_array($item_templates[0]["item_type"], range(1,3))){
                $attack = null;
                $defence = rand(0, $_SESSION["level"])*4;
            }
            else if($item_templates[0]["item_type"]==0){
                $attack = rand(0, $_SESSION["level"]*3);
                $defence = null;
            }
            else{
                $attack = null;
                $defence = null;
            }
            $value = rand(1, ($vit + $str + $dex + $int + $luck + $every_attrib)) * rand(1, 5);


            $db->exec(" INSERT INTO items (char_id, value, attack, defence, strength, 
                vit, dex, intelligence, luck, every_attrib,	
                item_status, item_template_id, item_place)
                values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?)", array($_SESSION["char_id"], $value, $attack, $defence,
                $str, $vit, $dex, $int, $luck, $every_attrib, $item_templates[0]["item_template_id"], $place));
        }
        function change_item_status($item_id, $item_status){
            global $db;
            if($item_status==0){
                
                $place=0;
                if($items=$db->exec('SELECT item_place FROM items WHERE char_id=? AND item_status=0 ORDER BY item_place', $_SESSION["char_id"])){
                    for($i=0;$i<8;$i++){
                        if($items[$i]["item_place"]!=$place){
                            break;
                        }
                        $place++;
                    }
                }
                $db->exec('UPDATE items SET item_status=0, item_place=? WHERE item_id=?', array($place, $item_id));
            }
            else if($item_status==2){
                $db->exec('UPDATE items SET item_status=2, item_place=null WHERE item_id=?', $item_id);
            }
        }
        function show_inventory(){
            global $db;
            global $f3;
            $matrix = \Matrix::instance();

            $itemsInventory=$db->exec('SELECT * 
            FROM items LEFT JOIN item_template 
            on items.item_template_id = item_template.item_template_id 
            WHERE item_status = 0 AND char_id=? ORDER BY item_place',$_SESSION["char_id"]);

            $max_items=range(0,7);
            foreach($itemsInventory as $item){
                $max_items = \array_diff($max_items, [$item["item_place"]]);
            }
            foreach($max_items as $place){
                $itemsInventory[]=array('item_name'=>'puste', 'value'=>null, 'item_place'=>$place);
            }
            $matrix->sort($itemsInventory,'item_place');
            
            $f3->set('items_inventory', $itemsInventory);
        }
        function show_equipped(){
            global $db;
            global $f3;
            $matrix = \Matrix::instance();

            $equipped=$db->exec('SELECT * 
            FROM items LEFT JOIN item_template 
            on items.item_template_id = item_template.item_template_id 
            WHERE item_status = 2 AND char_id=? ORDER BY item_type',$_SESSION["char_id"]);

            $item_types=array(
                '0'=>'bron',
                '1'=>'armor',
                '2'=>'tarcza',
                '3'=>'helm',
                '4'=>'buty',
                '5'=>'rekawice',
                '6'=>'amulet',
            );
            foreach($equipped as $item){
                unset($item_types[$item["item_type"]]);
            }
            foreach($item_types as $key => $item){
                $equipped[]=array('item_name'=>$item, 'value'=>null, 'item_type'=>$key);
            }
            $matrix->sort($equipped,'item_type');
            
            $classes=array('class','class','class','class','class','class','class');
            $f3->set('item_classes', $classes);
            $f3->set('equipped', $equipped);

        //0 bron
        //1 armor
        //2 tarcza`
        //3 helm
        //4 buty
        //5 rekawice
        //6 amulet
        }
        function get_stats($char_id, $real=true){
            global $f3;
            global $db;
            $stats = $db->exec('SELECT sum(attack) as attack, sum(vit) as vitallity, sum(defence) as defence, sum(strength) as strength, sum(intelligence) as intelligence, sum(dex) as dex, sum(luck) as luck, sum(every_attrib) as every_attrib 
            FROM (
                SELECT attack, defence, vit, strength, intelligence, dex, luck, "" as every_attrib FROM characters WHERE char_id=:id
            
                UNION
            
                SELECT sum(attack) as attack, sum(defence) as defence, sum(vit) as vit, sum(strength) as strength, sum(intelligence) as intelligence, sum(dex) as dex, sum(luck) as luck, sum(every_attrib) as every_attrib FROM items WHERE char_id=:id AND item_status=2
            ) t', array(':id'=>$char_id))[0];

            $char_info = $db->exec('SELECT level, nickname, char_class FROM characters WHERE char_id=?', $char_id)[0];

            if($stats['every_attrib']){
                foreach($stats as $key => $value){
                    if($key!='every_attrib'){
                        $stats[$key]+=$stats['every_attrib'];
                    }
                }
            }
            unset($stats['every_attrib']);

            if(!$real){
                $char_info['level']-=rand(-3,3);
                if($char_info['level']<1){
                    $char_info['level']=1;
                }
                foreach($stats as $key => $stat){
                    $stats[$key]=round($stat*7/10);         
                }
            }

            if($char_info['char_class']==1){
                $stat1=$stats['intelligence'];
                $stat2=$stats['dex'];
                $stat3=$stats['strength'];
            }
            elseif($char_info['char_class']==2){
                $stat1=$stats['strength'];
                $stat2=$stats['dex'];
                $stat3=$stats['intelligence'];
            }
            else{
                $stat1=$stats['dex'];
                $stat2=$stats['intelligence'];
                $stat3=$stats['strength'];
            }

            $multiplier=sqrt($char_info['level'])*5;
            $attack = round($multiplier*$stat1*6/10+$multiplier*$stat2*3/10+$multiplier*$stat3*1/10);
            $stats['attack']+=$attack;
            $health=round($stats['vitallity']*$multiplier*2);
            
            if($real){
                $arr=array();
                foreach($stats as $key => $value){
                    $arr[] = array('name' => $key, 'value' => $value);
                    $test = $value;
                }
    
                $f3->set('health', $health);
                $f3->set('stats', $arr);
                
                foreach($char_info as $key => $value){
                    $stats[$key]=$value;
                }
            }
            else{
                $stats['level']=$char_info['level'];
                $enemy=$db->exec('SELECT enemy_name, enemy_icon FROM enemy_template ORDER BY rand() limit 1')[0];
                $stats['nickname']=$enemy['enemy_name'];
                $stats['icon']="ui/images/icons/".$enemy['enemy_icon'];
                $stats['char_class']=4;
            }


            $stats['health']=$health;
            return $stats;
        }
    }
?>