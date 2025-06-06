$(document).ready(function() {
    $('#entry-form').on('beforeSubmit', function () {
        const data = $(this).serialize();
        try {
            $.ajax({
                url: '/site/shorten',
                type: 'POST',
                data: data,
                success: function(res){
                    $('#error').hide();
                    $('#s-link').text(res.short_link_text).attr('href', res.short_link);
                    $('#qr-img').attr('src', res.qr_code);
                    $('#result').show();
                },
                error: function(res){
                    $('#result').hide();
                    if (res.status === 400) {
                        $('#error').text(res.responseText).show();
                    } else {
                        $('#error').text('Ошибка получения данных').show();
                    }
                }
            });
        } catch (e) {
            $('#result').hide();
            $('#error').text('Непредвиденная ошибка. Мы уже работаем над этим').show();
        }
        return false;
    })

    $('#s-link').on('click', function() {
        console.log('111');
    })
})