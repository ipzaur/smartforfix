var lightbox = {
    'tag' : {
        'main'    : false,
        'area'    : false,
        'back'    : false,
        'content' : false
    },
    'show' : function(content, callback) {
        var maxHeight = $(document).height();
        lightbox.tag.area = $('<div />').addClass('lightbox_area').attr('data-lb_action','close').appendTo($('body'));
        lightbox.tag.back = $('<div />').addClass('lightbox_back').attr('data-lb_action','close').appendTo($(lightbox.tag.area));
        lightbox.tag.main = $('<div />').addClass('lightbox').appendTo(lightbox.tag.area);
        $('<div />').addClass('lightbox_close').attr('data-lb_action','close').appendTo(lightbox.tag.main);
        lightbox.tag.content = $('<div />').addClass('lightbox_content').appendTo(lightbox.tag.main);
        if (typeof content === 'string') {
            content = $(content);
        }
        content.appendTo(lightbox.tag.content);

        var top = $(window).scrollTop() + 100;
        if (top + lightbox.tag.main.outerHeight() > maxHeight) {
            top = maxHeight - lightbox.tag.main.outerHeight();
            if (top < 0) {
                top = 0;
            }
        }
        lightbox.tag.area.css('top', top + 'px');

        lightbox.tag.area.on('click', function(ev){
            var el = $(ev.target);
            if (el.is('[data-lb_action="close"]')) {
                if (callback) {
                    callback();
                }
                lightbox.close();
            }
        });
    },
    'close' : function() {
        lightbox.tag.back.remove()
        lightbox.tag.area.remove()
        lightbox.tag.back = false;
        lightbox.tag.area = false;
        lightbox.tag.main = false;
        lightbox.tag.content = false;
    }
}
