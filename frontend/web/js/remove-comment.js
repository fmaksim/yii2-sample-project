$(document).ready(function () {
    $('a.remove-comment').click(function () {
        var formId = $(this).attr('data-form');
        $("#" + formId).submit();
    });
});