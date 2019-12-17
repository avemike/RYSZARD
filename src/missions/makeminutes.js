function makeminutes(){
    timespans = document.querySelectorAll(".mission_time");
    for(const time of timespans){
        console.log(time.innerHTML)
        time.innerHTML=Number(time.textContent)/60 + ' min';
    }
}