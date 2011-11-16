/**
 * Simple tabs
 */
$(document).ready(function(){

    // Добавим сразу же видимость первым элементам табов
    var tabSw = $('#blog-l10n ul.tabs');
    $('li:first', tabSw).addClass ('active')
    .parents('#blog-l10n').find('div.content:first').show();
    tabSw.delegate('li:not(.active)', 'click', function() {
        $(this).addClass('active')
        .siblings().removeClass('active')
        .parents('#blog-l10n')
        .find('div.content').hide()
        .eq($(this).index()).show();
    });
});