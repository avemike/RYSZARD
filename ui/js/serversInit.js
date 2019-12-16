const buttons = document.querySelectorAll(".server")
for (const button of buttons) {
    button.addEventListener('click', function(event) {
        button.parentNode.submit();
    })
}