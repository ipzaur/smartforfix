var menuSection = {
    'tag' : {
        'main'   : $('#menuSection')
    },
    'state' : {
        'toggle' : function(show) {
            if ( (show === true) || (show === false) ) {
                menuSection.tag.main.toggleClass('_active', show);
            } else {
                menuSection.tag.main.toggleClass('_active');
            }
        }
    },
    'init' : function() {
        if (isMobile) {
            menuSection.tag.main.find('.menu_items').remove();
            menuSection.tag.main.find('.menu_select').on('change', function(ev){
                var el = $(ev.target);
                document.location.href = SITEURI + el.val();
            });
        } else {
            menuSection.tag.main.find('.menu_select').remove();

            menuSection.tag.main.on('click', function(ev){
                var el = $(ev.target);
                if ( (el.attr('id') == 'menuSection') || el.hasClass('menu_current') ) {
                    menuSection.state.toggle();
                }
            });
            $(window).on('click', function(ev){
                var el = $(ev.target)
                if ( (el.closest('#menuSection').size() == 0) && menuSection.tag.main.hasClass('_active') ) {
                    menuSection.state.toggle(false);
                }
            });
        }
    }
}

menuSection.init();