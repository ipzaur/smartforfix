var login = {
    'tag' : {
        'main'   : $('#login')
    },
    'state' : {
        'toggle' : function(show) {
            if ( (show === true) || (show === false) ) {
                login.tag.main.toggleClass('_active', show);
            } else {
                login.tag.main.toggleClass('_active');
            }
        }
    },
    'init' : function() {
        login.tag.main.on('click', function(ev){
            var el = $(ev.target);
            if (el.is('[data-login_action="toggle"]')) {
                login.state.toggle();
            }
        });
        $(window).on('click', function(ev){
            var el = $(ev.target)
            if ( (el.closest('.login').size() == 0) && login.tag.main.hasClass('_active') ) {
                login.state.toggle(false);
            }
        });
    }
}

login.init();