$('.logo_upload').change(function () {
    let data = new FormData;
    let file = $(this)[0].files[0];
    let element = $(this).closest('.team_logo_upload');
    let id = element.find('input[name="team_id"]').val();

    data.append('logo', file);
    data.append('_method', 'PATCH');

    $.ajax({
        url: '/teams/' + id,
        dataType: 'text',
        cache: false,
        contentType: false,
        processData: false,
        data: data,
        type: 'post',
        success: function (new_path) {
            element.find('.card-img-top').attr('src', '/storage/' + new_path);
        }
    });
});
