var auth = {
    tag : {
        main : $('#auth')
    },
    state : {
        toggle : function(show) {
            if ( (show === true) || (show === false) ) {
                auth.tag.main.toggleClass('_active', show);
            } else {
                auth.tag.main.toggleClass('_active');
            }
        }
    },
    init : function() {
        $(window).on('click', function(ev) {
            var $el = $(ev.target)
            if ( !$el.closest('#auth').length && auth.tag.main.hasClass('_active') ) {
                auth.state.toggle(false);
            } else if ( $el.closest('[data-auth_action="toggle"]').length && !$el.closest('[data-auth="menu"]').length ) {
                auth.state.toggle();
            }
        });
    }
}

auth.init();
