$(document).ready(function() {
    $('#entry-form').on('beforeSubmit', function () {
        /*
        var yiiform = $(this);    // отправляем данные на сервер
        var formData = yiiform.serializeArray();
        $.ajax({
                type: yiiform.attr('method'),
                url: yiiform.attr('action'),
                data: formData,
                contentType: 'application/json',
                //dataType: 'json',
            }
        )
        .done(function(data) {
            if (data.success) {
                // данные сохранены
                $('#error').hide();
                $('#short-url').text(response.shortUrl).attr('href', response.shortUrl);
                $('#qr-code').html(response.qrCode);
                $('#result').show();
                $('#ul-result').show();
            } else {
                // сервер вернул ошибку и не сохранил наши данные
                $('#result').hide();
                $('#error').text(data.error).show();
            }
        })
        .fail(function () {
            // не удалось выполнить запрос к серверу
        })
        */
        var data = $(this).serialize();
        try {
            $.ajax({
                url: '/site/test',
                type: 'POST',
                data: data,
                success: function(res){
                    //console.log(res);
                    $('#error').hide();
                    $('#short-url').text(res.www).attr('href', res.www);
                    $('#s-link').text(res.www).attr('href', res.www);
                    $('#qr-code').html(res.qr);
                    $('#qr-img').attr('src', res.qr);
                    $('#result').show();
                    $('#ul-result').show();
                },
                error: function(res){
                    //alert('Error!');
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
        return false; // отменяем отправку данных формы
    })
})