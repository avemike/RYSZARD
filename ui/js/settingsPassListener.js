function settingsPassListener(button_id){
    document.getElementById(button_id).addEventListener("click", function(){
        if(button_id=='change_password_button'){
            new_password=document.getElementById("new_password").value;
            old_password=document.getElementById("old_password").value;
            params="new_password="+new_password+"&old_password="+old_password;
            load_url='changePassword';
            type='POST';
        }
        else if(button_id=='send_mail'){
            address=document.getElementById("address").value;
            title=document.getElementById("title").value;
            content=document.getElementById("content").value;
            params='address='+address+"&title="+title+"&content="+content;
            load_url='mail';
            type='POST';
        }
        load(load_url, type, '#box', params);
    })
}