var debug = $('#debug');
if (debug.size() > 0) {
    $(debug).on('click', function(){
        $.ajax({
            type     : 'POST',
            url      : '/_ajax/debug/',
            dataType : 'json',
            success  : function(json) {
                debug.addClass('_on');
            }
        });
    });
}
