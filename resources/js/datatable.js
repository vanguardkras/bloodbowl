$(document).ready(function () {

    let lang = $('html').attr('lang');
    let options = {};

    if (lang === 'ru') {
        options = {"language": {"url": "/js/datatables_ru.json"}}
    }
    $('#statistics').DataTable(options);
});
