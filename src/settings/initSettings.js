function initSettings() {

    // change password listener
    document.getElementById(button_id).addEventListener("click", function () {
        new_password = document.getElementById("new_password").value;
        old_password = document.getElementById("old_password").value;
        params = "new_password=" + new_password + "&old_password=" + old_password;
        load_url = 'changePassword';
        type = 'POST';

        load(load_url, type, '#box', params);
    });
}