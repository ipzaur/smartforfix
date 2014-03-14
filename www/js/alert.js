var alert = {
    'tag' : {
        'main' : false
    },
    'close' : function(item) {
        item.remove();
    },
    'show' : function(text, type) {
        if (!type) {
            type = 'message';
        }
        var item = $('<div />').addClass('alert-' + type).attr('alert-action', 'close').html(text).appendTo(alert.tag.main);
        setTimeout(function(){
            item.addClass('_faded');
            setTimeout(function(){
                alert.close(item);
            }, 2000);
        }, 8000);
    },
    'init' : function() {
        alert.tag.main = $('<div />').addClass('alerts').appendTo($('body'));

        alert.tag.main.on('click', function(ev){
            var el = $(ev.target);
            if (el.is('[alert-action="close"]')) {
                alert.close(el);
            }
        });
    }
}

alert.init();
