<?php
    class fight {
        
        function enemy_generate($f3) {
            $db=new DB\SQL('mysql:host=localhost;port=3306;dbname=ryszardDB','root','',array(\PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES utf8;'));
            $user1=new DB\SQL\Mapper($db,'enemy_template');
            $user2=new DB\SQL\Mapper($db,'characters');
            $random_enemy_template_id=rand(1,$user1->count('enemy_template_id>=1')); 
            $player_level=$user2->load(array('char_id=?',$_SESSION["char_id"]))->level; 
            
            if ($user1->load(array('enemy_template_id=?',$random_enemy_template_id))->enemy_class=="elektronik") {
                $strength=rand(30,70);
                $dex=rand(70,100);
                $luck=rand(0,30);
            } elseif ($user1->load(array('enemy_template_id=?',$random_enemy_template_id))->enemy_class=="mechatronik") {
                $strength=rand(70,100);
                $dex=rand(0,30);
                $luck=rand(30,70);
            } else {
                $strength=rand(0,30);
                $dex=rand(0,10);
                $luck=rand(70,100);
            };
            
            $virtual_enemy=array(
                "name" => $user1->load(array('enemy_template_id=?',$random_enemy_template_id))->enemy_name,
                //"icon" => $user1->load(array('enemy_template_id=?',$random_enemy_template_id))->enemy_icon,
                "class" => $user1->load(array('enemy_template_id=?',$random_enemy_template_id))->enemy_class,
                "level" => rand(1,$player_level),
                "strength" => $strength,
                "hp" => 100,
                "dex" => $dex,
                "luck" => $luck,
            );
            return $virtual_enemy;
        }
        function fight_kurwa($f3,$is_real) {
            $db=new DB\SQL('mysql:host=localhost;port=3306;dbname=ryszardDB','root','',array(\PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES utf8;'));
            $user=new DB\SQL\Mapper($db,'characters');
            
            $player_level=$user->load(array('char_id=?',$_SESSION["char_id"]))->level;
            $player_hp=100;
            $player_luck=$user->load(array('char_id=?',$_SESSION["char_id"]))->luck;
            $players_list=$user->find(array('level<=?',$player_level));
            $players_amount=$user->count(array('level<=?',$player_level));
            $random_enemy_char_id=$players_list[rand(0,$players_amount-1)]->char_id;
            
            $required_player_luck=$player_level*10;
            $critical_player_probability=$player_luck/$required_player_luck*50;
            
            $fight_array=array();

            if (!$is_real) {
                $enemy=$this->enemy_generate($f3);
                $required_enemy_luck=$enemy["level"]*10;
                $critical_enemy_probability=$enemy["luck"]/$required_enemy_luck*50;

                while ($enemy["hp"]>0) {
                    $player_hit=rand(10,30);
                    if ($player_hit==0) {
                        array_push($fight_array,"Przeciwnik zrobił unik!");    
                    } else {
                        array_push($fight_array,"Player hit: ".$player_hit);
                        $enemy["hp"]-=$player_hit;
                        if ($enemy["hp"]<=0) {
                            $f3->set('result',"win");
                            break;
                        } else array_push($fight_array,"Current enemy hp: ".$enemy["hp"]);
                    };     
                    $enemy_hit=rand(10,$player_hit-1);
                    if ($enemy_hit==0) {
                        array_push($fight_array,"Zrobiłeś unik!");
                    } else {
                        array_push($fight_array,"Enemy hit: ".$enemy_hit);
                        $player_hp-=$enemy_hit;
                        if ($enemy["hp"]<=0) {
                            $f3->set('result',"win");
                            break;
                        } else array_push($fight_array,"Current player hp: ".$player_hp);
                    }
                    if (rand(1,100)<$critical_player_probability) { 
                        array_push($fight_array,"Your critical hit!");
                        $enemy["hp"]-=50;
                        if ($enemy["hp"]<=0) {
                            $f3->set('result',"win");
                            break;
                        } else {
                            array_push($fight_array,"Current enemy hp: ".$enemy["hp"]);
                        }
                    };        
                };
            } else {
                $enemy=array(
                    "name" => $user->load(array('char_id=?',$random_enemy_char_id))->nickname,
                    "class" => $user->load(array('char_id=?',$random_enemy_char_id))->char_class,
                    "level" => $user->load(array('char_id=?',$random_enemy_char_id))->level,
                    "strength" => $user->load(array('char_id=?',$random_enemy_char_id))->strength,
                    "hp" => 100,
                    "dex" => $user->load(array('char_id=?',$random_enemy_char_id))->dex,
                    "luck" => $user->load(array('char_id=?',$random_enemy_char_id))->luck,
                    "race" => $user->load(array('char_id=?',$random_enemy_char_id))->race,
                );
            
                while ($enemy["hp"]>0) {
                    $player_hit=rand(10,30);
                    if ($player_hit==0) {
                        array_push($fight_array,"Przeciwnik zrobił unik!");    
                    } else {
                        array_push($fight_array,"Player hit: ".$player_hit);
                        $enemy["hp"]-=$player_hit;
                        if ($enemy["hp"]<=0) {
                            $f3->set('result',"win");
                            break;
                        } else array_push($fight_array,"Current enemy hp: ".$enemy["hp"]);
                    };     
                    $enemy_hit=rand(10,30);
                    if ($enemy_hit==0) {
                        array_push($fight_array,"Zrobiłeś unik!");
                    } else {
                        array_push($fight_array,"Enemy hit: ".$enemy_hit);
                        $player_hp-=$enemy_hit;
                        if ($player_hp<=0) {
                            $f3->set('result',"defeat");
                            break;
                        } else array_push($fight_array,"Current player hp: ".$player_hp);
                    }
                    if (rand(1,100)<$critical_player_probability) { 
                        array_push($fight_array,"Your critical hit!");
                        $enemy["hp"]-=50;
                        if ($enemy["hp"]<=0) {
                            $f3->set('result',"win");
                            break;
                        } else {
                            array_push($fight_array,"Current enemy hp: ".$enemy["hp"]);
                        }
                    };
                    if (rand(1,100)<$critical_enemy_probability) { 
                        array_push($fight_array,"Enemy critical hit!");
                        $player_hp-=50;
                        if ($player_hp<=0) {
                            $f3->set('result',"defeat");
                            break;
                        } else {
                            array_push($fight_array,"Current player hp: ".$enemy["hp"]);
                        }
                    };        
                };
            };
            for ($i=0;$i<count($fight_array);$i++) {
                $fight_description.=$fight_array[$i]." ";
            };
            $f3->set('fight_desc',$fight_description);
            $f3->set('name',$enemy["name"]);
            $f3->set('class',$enemy["class"]);
            $f3->set('level',$enemy["level"]);
            $f3->set('strength',$enemy["strength"]);
            $f3->set('hp',"100");
            $f3->set('dex',$enemy["dex"]);
            $f3->set('luck',$enemy["luck"]);
            if ($is_real) {
                $f3->set('race',$enemy["race"]);    
            } else $f3->set('race',null);
            echo \Template::instance()->render('fight.html');
        }
    }
?>