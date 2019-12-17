function initMail() {
    document.getElementById(button_id).addEventListener("click", function () {
        address = document.getElementById("address").value;
        title = document.getElementById("title").value;
        content = document.getElementById("content").value;
        params = 'address=' + address + "&title=" + title + "&content=" + content;
        load_url = 'mail';
        type = 'POST';
    
        load(load_url, type, '#box', params);
    })
}