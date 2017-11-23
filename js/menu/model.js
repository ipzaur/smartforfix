var menuModel = {
    'timer' : 0,
    'tag' : {
        'main'    : $('#menuModel'),
        'select'  : false,
        'current' : false
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
        clearTimeout(menuModel.timer);
        menuModel.timer = setTimeout(function(){
            var models = {};
            var postData = [];
            menuModel.tag.select.find('option:selected').each(function(i, option){
                option = $(option);
                postData[postData.length] = 'model[]=' + option.val();
            });
            menuModel.tag.current.find('.menu_current_model').remove();
            $.ajax({
                type     : 'POST',
                url      : '/_ajax/menumodels/',
                data     : postData.join('&'),
                dataType : 'json',
                success  : function(json){
                    location.reload(); // потом переделать на ХТМЛ5

                    if (isMobile) {
                        return true;
                    }
                    for (var model in json.result) if (json.result.hasOwnProperty(model)) {
                        if (json.result[model].show) {
                            $('<span />')
                                .addClass('menu_current_model')
                                .attr({'data-model_id':model, 'data-models_action':'remove'})
                                .text(json.result[model].name)
                                .appendTo(menuModel.tag.current);
                        }
                    }
                }
            });
        }, 800);
    },
    'modelStatus' : function(model, show) {
        menuModel.tag.select.find('[value="' + model + '"]').prop('selected', show);
        if (!isMobile) {
            menuModel.tag.main.find('.menu_model_cb[data-model_id="' + model + '"]').prop('checked', show);
        }
        menuModel.tag.select.change();
    },
    'init' : function() {
        menuModel.tag.current = menuModel.tag.main.find('.menu_current');
        menuModel.tag.select = menuModel.tag.main.find('.menu_select');

        if (isMobile) {
            menuModel.tag.main.find('.menu_items').remove();
        } else {
            menuModel.tag.select.addClass('h');

            $(window).on({
                click : function(ev){
                    var $el = $(ev.target);
                    if ( !$el.closest('#menuModel').length && menuModel.tag.main.hasClass('_active') ) {
                        menuModel.state.toggle(false);
                    } else if ($el.attr('data-models_action')) {
                        switch ($el.attr('data-models_action')) {
                            case 'remove' : menuModel.modelStatus($el.attr('data-model_id'), false); break;
                            case 'toggle' : menuModel.state.toggle(); break;
                        }
                    }
                },
                change : function(ev) {
                    var $el = $(ev.target);
                    if ($el.is('[data-models_action="show"]')) {
                        menuModel.modelStatus($el.attr('data-model_id'), $el.prop('checked'));
                    } else if ($el.is('[data-models="select"]')) {
                        menuModel.save();
                    }
                }
            });
        }
    }
}

menuModel.init();
