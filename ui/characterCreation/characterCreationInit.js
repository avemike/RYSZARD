$('button.left, button.right').click( (e) => {
    e.preventDefault()
    // $icons = $('.icons')
    if($('.icons').hasClass('right')) {
        $('.icons').removeClass('right')
    }
    else {
        $('.icons').addClass('right')
    }

})
let icon = 0
let race = document.querySelector('button.race.selected').dataset.name
let char_class = document.querySelector('button.class.selected').dataset.name
let nickname = "";
// initial load
$('.icons').load(`getCharacterIcons?class=${char_class}&race=${race}`)
// if buttons pressed then load new image

$('input[name=nickname]').change(function () {
    nickname = this.value
})

$('button.class, button.race').click( function (e) {
    e.preventDefault()
                
    if( $(this).hasClass('class')) {
        $('button.class.selected').removeClass('selected')                
        char_class = this.dataset.name
    }
    else {
        $('button.race.selected').removeClass('selected')
        race = this.dataset.name
    }

    $(this).addClass('selected')
    $('.icons').load(`getCharacterIcons?class=${char_class}&race=${race}`)
})

$('form').submit( e => {
    e.preventDefault()
    let params = []

    const data = {
        nickname: nickname,
        occupation: char_class,
        race: race,
        icon: $('.icons').hasClass('right') ? '2' : '1'
    };
    for ( i in data) {
        params.push(i + '=' + data[i])
    }
    params = params.join('&')

    const http = new XMLHttpRequest()
    http.open('POST', 'createchar', true)
    http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
    http.onreadystatechange = () => {
        if(http.readyState == 4 && http.status == 200) {
            if(http.responseText === 'success') {
                // redirect to new path
                const splitedPath = window.location.href.split('/')
                const newPath = splitedPath.slice(0, splitedPath.length - 1).join('/')
                
                window.location.href = newPath
            }
        }
    }
    http.send(params)
})