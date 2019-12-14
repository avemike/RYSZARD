<?php
class missions{
    function missionPage(){
        global $db;
        global $f3;
        //if character has active mission
        if($result=$db->exec('SELECT mission_id, TIMESTAMPDIFF(SECOND,start_date,current_timestamp()) AS started_ago, duration_time, currency_reward, exp_reward, mission_description FROM missions LEFT JOIN mission_template on missions.mission_template_id = mission_template.mission_template_id WHERE char_id=? AND mission_active=1', $_SESSION["char_id"])[0]){
            $f3->set('activemission', true);
            //if active mission has ended
            if($result["started_ago"]>$result["duration_time"]){
                $fight = new fight_module;
                //if you have won fight
                if($fight->fight($_SESSION['char_id'])){
                    $f3->set('missionwon', true);
                    $this->addexperience($result["currency_reward"], $result["exp_reward"]);
                }
                else{
                    $f3->set('missionwon', false);
                }
                $f3->set('missionready', $result);
                $f3->set('mission_description', $result["mission_description"]);

                //clear missions for character
                $db->exec('DELETE FROM missions WHERE char_id=?', $_SESSION["char_id"]);
            }
            //if mission is not ended yet
            else{
                $f3->set('missionready', false);
                //show time to mission end
                $f3->set('missionbox',$result["duration_time"]-$result["started_ago"]);
            }
        }
        //no active missions
        else{
            $f3->set('activemission', false);

            //if missions are already generated
            if($result=$db->exec('SELECT char_id, currency_reward, exp_reward, duration_time, mission_name, mission_id FROM missions LEFT JOIN mission_template on missions.mission_template_id = mission_template.mission_template_id WHERE char_id=?',$_SESSION["char_id"])){
                $f3->set('missionbox',$result);
            }
            //else generate new missions
            else{
                //get 3 random mission templates
                $mission_templates = $db->exec('SELECT mission_template_id FROM mission_template ORDER BY rand() LIMIT 3');

                for($i=0;$i<3;$i++){
                    $duration_time=rand(1,20)*30;
                    $currency_reward=round((($_SESSION["level"]*$_SESSION["level"]/10)+100)*$duration_time/100*(1+rand(0,1)));
                    $exp_reward=round((($_SESSION["level"]*$_SESSION["level"]/10)+100)*$duration_time/100*(1+rand(0,1)));
                    
                    //TESTING OPTIONS
                    $duration_time=1;
                    $exp_reward=1000;
                    $currency_reward=1000;

                    $db->exec('INSERT INTO missions (char_id, currency_reward, exp_reward, duration_time, mission_template_id, start_date, mission_active)
                    values (?, ?, ?, ?, ?, CURRENT_TIMESTAMP(), "0")', array($_SESSION["char_id"], $currency_reward, $exp_reward, $duration_time, $mission_templates[$i]["mission_template_id"]));
                }
                $f3->set('missionbox', $db->exec('SELECT char_id, currency_reward, exp_reward, duration_time, mission_name, mission_id FROM missions LEFT JOIN mission_template on missions.mission_template_id = mission_template.mission_template_id WHERE char_id=?',$_SESSION["char_id"]));
            }
        }
        echo \Template::instance()->render('missions.html');
    }
    function choosemission($f3){
        global $db;
        if(!empty($_SESSION["nickname"])){
            //if posted mission_id is correct with character used then set that mission active
            if($db->exec('SELECT * FROM missions WHERE mission_id=? AND char_id=?', array($_POST["activemission"], $_SESSION["char_id"]))){
                $db->exec('UPDATE missions SET mission_active="1", start_date=current_timestamp() WHERE mission_id=?',$_POST["activemission"]);
            }
            $f3->reroute('@missions');
        }
        $f3->reroute('@login');
    }
    function addexperience($currency, $exp){
        global $db;
        $char=new DB\SQL\Mapper($db,'characters');
        $char->load(array('char_id=?',$_SESSION["char_id"]));
        $char->currency+=$currency;
        //if new exp pool is higher than needed to lv up, then lv up
        if($char->exp+$exp>=$char->exp_to_next_lv){
            $char->exp=$char->exp+$exp-$char->exp_to_next_lv;
            $char->level++;
            $char->exp_to_next_lv+=500;
        }
        //else just add exp to the exp pool
        else{
            $char->exp+=$exp;
        }
        $char->save();
        //update session
        $_SESSION["currency"]=$char->currency;
        $_SESSION["exp"]=$char->exp;
        $_SESSION['level']=$char->level;
        $_SESSION['exp_to_next_lv']=$char->exp_to_next_lv;
    }
}
?>