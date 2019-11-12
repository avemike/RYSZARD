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
                // $enemy['level']+=1;
            }
            $f3->set('fight_log', json_encode($you)."%".json_encode($enemy)."%<br>");
            $counter=0;
            while($you['health']>0 && $enemy['health']>0 ){
                $enemy['health']-=$this->hit($you, $enemy);
                $counter++;
                if($enemy['health']>0){
                    $you['health']-=$this->hit($enemy, $you);
                    $counter++;
                }
                if($counter>=200){
                    break;
                }
            }

            echo $f3->get('fight_log');
            echo "<br>".$counter;
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

            $crit_chance=$attacker['luck']/$attacker['level']*3*2;
            if(rand(0,100)<=$crit_chance){
                $crit="!!!";
                $crit_dmg=rand(150,200)/100;
            }

            $hit_multiplier=rand(80,120)/100;
            $hit=round(($attacker['attack']-($defender['defence']*2))*$hit_multiplier*$crit_dmg);

            $miss_chance=$defender['luck']/$defender['level']*3;
            if($hit<0 || rand(0,100)<=$miss_chance){
                $hit="???miss";
                $crit="";
                $newhealth=$defender['health'];
            }
            else{
                $newhealth=$defender['health']-$hit;
            }

            
            // $info=$attacker['nickname']."->".$defender['nickname']."&".$crit.$hit."&".$defender['health']."->".$newhealth."#<br>";
            $info['crit_chance']=$crit_chance;
            // $info['miss_chance']=$miss_chance;
            $info['hit_multiplier']=$hit_multiplier;
            $info['desc']=$attacker['nickname']."->".$defender['nickname'];
            $info['crit_multiplier']=$crit_dmg;
            $info['hit']=$crit.$hit;
            $info['health']=$defender['health']."->".$newhealth;


            $log=$f3->get('fight_log');
            $log.=json_encode($info)."&<br>";
            $f3->set('fight_log', $log);

            if($hit<0 || !is_numeric($hit)){
                return 0;
            }
            else{
                return $hit;
            }
        }
    }
?>