<?php
    class fight_module{
        function fight($you, $enemy=false){
            global $f3;
            global $db;
            $items = new items;
            $you=$items->get_stats($you);
            if($enemy){
                $enemy=$items->get_stats($enemy);
            }
            else{
                //generate
                $items = new items;
                $enemy=$items->get_stats($_SESSION['char_id']);
                $enemy['nickname']="przeciwnik";
                $enemy['level']-=1;
            }
            $f3->set('fight_log', json_encode($you)."%".json_encode($enemy)."%<br>");
            while($you['health']>0 && $enemy['health']>0 ){
                $enemy['health']-=$this->hit($you, $enemy);
                if($enemy['health']>0){
                    $you['health']-=$this->hit($enemy, $you);
                }
            }

            echo $f3->get('fight_log');

            if($you['health']>0){
                return true;
            }
            else{
                return false;
            }
        }
        function hit($attacker, $defender){
            global $f3;
            $crit="";
            $crit_dmg=1;

            $crit_chance=$attacker['luck']/$attacker['level']*10;
            if(rand(0,100)<=$crit_chance){
                $crit="!!!";
                $crit_dmg=rand(15,20)/10;
            }

            $hit=round(($attacker['attack']-($defender['defence']*7))*rand(10,15)/10*$crit_dmg);

            $newhealth=$defender['health']-$hit;
            
            $info=$attacker['nickname']."->".$defender['nickname']."&".$crit.$hit."&".$defender['health']."->".$newhealth."#<br>";
            
            $log=$f3->get('fight_log');
            $log.=$info;
            $f3->set('fight_log', $log);

            if($hit<0){
                return 0;
            }
            else{
                return $hit;
            }
        }
    }
?>