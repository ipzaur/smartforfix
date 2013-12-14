var articleEdit = {
    'saving' : false,
    'tag' : {
        'main'   : $('#articleEdit'),
        'elem' : {
            'name' : false,
            'type' : false,
            'ext_link' : false,
            'content_source' : false
        },
        'submit' : false
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
    'save' : function() {
        if (articleEdit.saving) {
            return false
        }
        articleEdit.saving = true;

        var postData = 'id=' + articleEdit.tag.main.attr('data-article_id') +
                       '&name=' + articleEdit.tag.elem.name.val() +
                       '&type=' + articleEdit.tag.elem.type.val() +
                       '&content_source=' + articleEdit.tag.elem.content_source.val();
        if (articleEdit.tag.elem.type.val() > 0) {
            postData += '&ext_link=' + articleEdit.tag.elem.ext_link.val();
        }
        articleEdit.tag.main.find('[name="info[]"]:checked').each(function(i, info){
            postData += '&info[]=' + $(info).val();
        });

        $.ajax({
            type     : 'POST',
            url      : '/_ajax/articlesave/',
            data     : postData,
            dataType : 'json',
            success  : function(json){
                articleEdit.tag.main.attr('data-article_id', json.result);
                articleEdit.saving = false;
            }
        });
    },
    'check' : function() {
        var disabled = false;
        var elems = articleEdit.tag.elem;
        for (var elemName in elems) if (elems.hasOwnProperty(elemName)) {
            if ( (elems[elemName].val() == '') ) {
                if ( (elemName == 'ext_link') && (elems.type.val() == 0) ) {
                    continue;
                }
                disabled = true;
            }
        }
        articleEdit.tag.submit.prop('disabled', disabled);

        return !disabled;
    },
    'init' : function() {
        var elems = articleEdit.tag.elem;
        for (var elemName in elems) if (elems.hasOwnProperty(elemName)) {
            elems[elemName] = articleEdit.tag.main.find('[name="' + elemName + '"]');
        }
        articleEdit.tag.submit = articleEdit.tag.main.find('[name="submit"]');

        articleEdit.tag.main.on({
            'change' : function(ev){
                var el = $(ev.target);
                if (el.is('[name]')) {
                    switch (el.attr('name')) {
                        case 'type' :
                            elems.ext_link.toggleClass( 'h', (el.val() == 0) );
                            elems.ext_link.prop( 'disabled', (el.val() == 0) );
                            break;
                    }
                    articleEdit.check();
                }
            },
            'keyup' : function(ev) {
                var el = $(ev.target);
                if (el.is('[name]')) {
                    articleEdit.check();
                }
            },
            'submit' : function() {
                if (articleEdit.check()) {
                    articleEdit.save();
                }
                return false
            }
        });
    }
}

articleEdit.init();
