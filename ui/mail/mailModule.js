const loadMailModule = () => {
    document.querySelectorAll('.mail-row').forEach( mailRow => {
        mailRow.addEventListener('click', () => {
            mailRow.querySelector('.mail-content').classList.toggle('hidden')
        })
    })
}