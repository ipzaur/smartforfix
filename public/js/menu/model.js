var menuModel = {
    'tag' : {
        'main'   : $('#menuModel'),
        'select' : false
    },
    'state' : {
        'toggle' : function(show) {
            if ( (show === true) || (show === false) ) {
                menuModel.tag.main.toggleClass('_active', show);
            } else {
                menuModel.tag.main.toggleClass('_active');
            }
        }
    },
    'save' : function(ev) {
    },
    'init' : function() {
        menuModel.tag.select = menuModel.tag.main.find('.menu_select');
        menuModel.tag.select.on('change', menuModel.save);

        if (isMobile) {
            menuModel.tag.main.find('.menu_items').remove();
        } else {
            menuModel.tag.select.addClass('h');
            menuModel.tag.main.on('click', function(ev){
                var el = $(ev.target);
                if ( (el.attr('id') == 'menuModel') || el.hasClass('menu_current') ) {
                    menuModel.state.toggle();
                }
            });
            $(window).on('click', function(ev){
                var el = $(ev.target)
                if (el.closest('#menuModel').size() == 0) {
                    menuModel.state.toggle(false);
                }
            });
        }
    }
}

menuModel.init();