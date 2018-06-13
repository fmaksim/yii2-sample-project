$(document).ready(function () {
    $('a.button-complain').click(function () {
        var button = $(this);
        var loader = button.find('i.icon-placeholder');
        var params = {
            'id': $(this).attr('data-id')
        };
        loader.show();
        $.post('/post/default/complain', params, function (data) {
            loader.hide();
            button.addClass('disabled');
            button.html(data.text);
        });
        return false;
    });
});