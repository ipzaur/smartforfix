var login = {
    'tag' : {
        'main'   : $('#login'),
        'bubble' : false
    },
    'state' : {
        'toggle' : function(show) {
            if ( (show === true) || (show === false) ) {
                login.tag.bubble.toggleClass('h', !show);
            } else {
                login.tag.bubble.toggleClass('h');
            }
        }
    },
    'init' : function() {
        login.tag.bubble = login.tag.main.find('.bubble');

        login.tag.main.on('click', function(ev){
            var el = $(ev.target);
            if (el.is('[data-login_action="toggle"]')) {
                login.state.toggle();
            }
        });
        $(window).on('click', function(ev){
            var el = $(ev.target)
            if (el.closest('.login').size() == 0) {
                login.state.toggle(false);
            }
        });
    }
}

login.init();