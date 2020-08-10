$(document).ready(function () {

    let lang = $('html').attr('lang');
    let options = {};

    if (lang === 'ru') {
        options = {"language": {"url": "/js/datatables_ru.json"}}
    }
    $('#statistics, #archive, #history').DataTable(options);

    options.paging = false;
    options.info = false;
    options.searching = false;
    $('#open_league').DataTable(options);
});
