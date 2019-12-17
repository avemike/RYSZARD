function missionactive(){
    minutes = 0;
    seconds = Number(document.getElementById("timer").innerHTML);
    while(seconds>59){
        minutes++;
        seconds-=60;
    }
    document.getElementById("timer").innerHTML=minutes+" minut "+seconds+" sekund";

    // setTimeout(odejmczas, 1000);
    if(!t){
        t=setInterval(odejmczas, 1000);
    }
}