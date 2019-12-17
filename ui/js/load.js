function load(url, method, target_id, string){
            
    var http = new XMLHttpRequest();
    http.open(method, url, true);
    http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    http.onload = function () {
        document.querySelector(target_id).innerHTML = http.responseText;

        // switch new currency with old currency if both exists
        const newcurrency = document.querySelector('#newcurrency');
        const currency = document.querySelector('#currency');
        if ( currency && newcurrency ) {
            currency.innerHTML = newcurrency.innerHTML;
            newcurrency.parentNode.removeChild(newcurrency);
        }

        if(t){
            clearInterval(t);
            t=false;
        }

        // call specific loadingModule function after loading url
        switch (url) {
            case 'missions':
                makeminutes();
                break;
            case 'choosemission':
                missionactive();
                break;
            case 'changePassword':
            case 'settings':
                settingsPassListener('change_password_button');
                break;
            case 'mail':
                settingsPassListener('send_mail');
                break;
            case 'outbox':
            case 'mail':
            case 'inbox':
                loadMailModule() 
                break;
        }
    };
    http.send(string);
}