$(document).ready(function() {
    $('#entry-form').on('beforeSubmit', function () {
        const data = $(this).serialize();
        try {
            $.ajax({
                url: '/site/shorten',
                type: 'POST',
                data: data,
                success: function(res){
                    if (res.ok) {
                        $('#error').hide();
                        $('#s-link')
                            .text(res.short_link_text)
                            .attr('href', res.short_link)
                            .attr('data-short-code', res.short_code);
                        $('#qr-img').attr('src', res.qr_code);
                        $('#result').show();
                    } else {
                        $('#result').hide();
                        $('#error').text(res.errorMessage).show();
                    }
                },
                error: function() {
                    $('#result').hide();
                    $('#error').text('Ошибка получения данных').show();
                }
            });
        } catch (e) {
            $('#result').hide();
            $('#error').text('Непредвиденная ошибка. Мы уже работаем над этим').show();
        }
        return false;
    })
})