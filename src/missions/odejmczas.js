function odejmczas(){
    if(seconds<1 && minutes==0){
        clearInterval(t);
        t=null;
        load('missions', 'GET', '#box');
        // window.location.href="";
    }
    else{
        if(seconds==0){
            minutes--;
            seconds=59
        }
        else{
            seconds--;
        }
        document.getElementById("timer").innerHTML=minutes+" minut "+seconds+" sekund";
        // setTimeout(odejmczas, 1000);
    }
}