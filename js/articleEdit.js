var articleEdit = {
    'content_id' : 0,
    'saving' : false,
    'editor' : false,
    'tag' : {
        'main'   : $('#articleEdit'),
        'field'  : {
            'name'     : false,
            'section'  : false,
            'type'     : false,
            'ext_link' : false,
            'content'  : false,
            'tag'      : false
        },
        'photos' : false,
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

        var articleId = articleEdit.tag.main.attr('data-article_id');

        var postData =
            'id=' + articleId +
            '&name=' + encodeURIComponent(articleEdit.tag.field.name.val()) +
            '&section_id=' + articleEdit.tag.field.section.val() +
            '&type=' + articleEdit.tag.field.type.val() +
            '&content_source=' + encodeURIComponent(articleEdit.tag.field.content.val()) +
            '&tag=' + encodeURIComponent(articleEdit.tag.field.tag.val());
        if (articleEdit.tag.field.type.val() > 0) {
            postData += '&ext_link=' + encodeURIComponent(articleEdit.tag.field.ext_link.val());
        }
        articleEdit.tag.main.find('[name="info[]"]:checked').each(function(i, info){
            postData += '&info[]=' + $(info).val();
        });

        $.ajax({
            type     : 'POST',
            url      : '/_ajax/article/save/',
            data     : postData,
            dataType : 'json',
            success  : function(json){
                if (articleId == 0) {
                    articleEdit.tag.main.attr('data-article_id', json.result);
                    var url = SITEURL + 'article/_edit/' + json.result + '/';
                    if (canHtml5) {
                        history.pushState(null, null, url);
                        if (json.photos) {
                            var photos = articleEdit.tag.photos.find('.articleEdit_photo');
                            for (var i in json.photos) if (json.photos.hasOwnProperty(i)) {
                                $(photos[i]).attr('src', json.photos[i]);
                            }
                        }
                    } else {
                        window.location.href = url;
                    }
                }
                articleEdit.saving = false;
                alert.show('Статья успешно сохранена')
            }
        });
    },

    'upload' : function(ev) {
        uploader.do(ev, {
            'url' : SITEURL + '_ajax/article/upload/',
            'otherData' : {'content_id':articleEdit.content_id},
            'done' : function(files) {
                var last_num = articleEdit.tag.photos.find('.articleEdit_photo').length;
                for (var i in files) if (files.hasOwnProperty(i)){
                    last_num++;
                    var photo = $('<div />').addClass('articleEdit_photo').attr('data-photo_num', last_num);
                    if (parseInt(articleEdit.content_id) > 0) {
                        photo.attr('data-photo_id', i);
                    }
                    $('<img />').addClass('articleEdit_photo_img')
                        .attr({
                            'src' : files[i],
                            'data-photo_action' : 'insert',
                            'title' : 'Вставить фотографию в статью'
                        })
                        .appendTo(photo);
                    $('<span />').addClass('fake_link-red').text('Удалить').attr('data-photo_action', 'delete').appendTo(photo);
                    photo.appendTo(articleEdit.tag.photos);
                }
            }
        });
    },

    'insertPhoto' : function(photo) {
        var title = prompt('Комментарий к изображению (не обязательно)');
        if (title !== null) {
            articleEdit.editor.insertImg(photo.attr('data-photo_num'), title);
        }
    },

    'deletePhoto' : function(photo) {
        var photoNum = photo.attr('data-photo_num');

        var postData = {'content_id' : articleEdit.content_id};
        if (photo.attr('data-photo_id')) {
            postData.photoId = photo.attr('data-photo_id');
        } else {
            postData.photoSrc = photo.find('img').attr('src');
        }

        $.ajax({
            type     : 'POST',
            url      : SITEURL + '_ajax/article/photodel/',
            data     : postData,
            dataType : 'json',
            success  : function(json){
                photo.remove();
                var reg = new RegExp('<img.*?src="' + photoNum + '".*?>', 'g');
                var articleText = articleEdit.editor.tag.area.val().replace(reg, '');

                var photos = articleEdit.tag.photos.find('.articleEdit_photo');
                var photosCount = photos.length;
                for (var i=photoNum; i<=photosCount; i++) {
                    var photoChange = $(photos[i-1]);
                    var reg = new RegExp('(<img.*?src=")(' + photoChange.attr('data-photo_num') + ')(".*?>)', 'g');
                    articleText = articleText.replace(reg, '$1' + i + '$3');
                    photoChange.attr('data-photo_num', i);
                }
                articleEdit.editor.tag.area.val(articleText);
            }
        });
    },

    'check' : function() {
        var disabled = false;
        var fields = articleEdit.tag.field;
        for (var fieldName in fields) if (fields.hasOwnProperty(fieldName)) {
            if ( (fields[fieldName].val() == '') ) {
                if (fieldName == 'tag') {
                    continue;
                }
                if ( (fieldName == 'ext_link') && (fields.type.val() == 0) ) {
                    continue;
                }
                disabled = true;
            }
        }
        articleEdit.tag.submit.prop('disabled', disabled);

        return !disabled;
    },
    'changeType' : function(typeId) {
        articleEdit.tag.field.ext_link.prop( 'disabled', (typeId <= 0) );
    },
    'init' : function() {
        var fields = articleEdit.tag.field;
        for (var fieldName in fields) if (fields.hasOwnProperty(fieldName)) {
            fields[fieldName] = articleEdit.tag.main.find('[name="' + fieldName + '"]');
        }
        articleEdit.tag.photos = articleEdit.tag.main.find('[data-article_elem="photos"]');
        articleEdit.tag.submit = articleEdit.tag.main.find('[name="submit"]');
        articleEdit.content_id = articleEdit.tag.main.find('#content_id').val();

        articleEdit.editor = editor.init(articleEdit.tag.field.content);

        articleEdit.tag.main.on({
            'click' : function(ev){
                var el = $(ev.target);
                if (el.attr('data-photo_action')) {
                    var photo = el.closest('[data-photo_num]');
                    switch (el.attr('data-photo_action')) {
                        case 'insert' : articleEdit.insertPhoto(photo); break;
                        case 'delete' : articleEdit.deletePhoto(photo); break;
                    }
                } else if (el.is('[data-article_action="submit"]')) {
                    if (articleEdit.check()) {
                        articleEdit.save();
                    }
                }
            },
            'change' : function(ev){
                var el = $(ev.target);
                if (el.is('[data-photo_action="upload"]')) {
                    articleEdit.upload(ev);
                } else if (el.is('[name]')) {
                    switch (el.attr('name')) {
                        case 'type' : articleEdit.changeType(el.val()); break;
                    }
                    articleEdit.check();
                }
            },
            'keyup' : function(ev) {
                var el = $(ev.target);
                if (el.is('[name]')) {
                    articleEdit.check();
                }
            }
        });
    }
}

articleEdit.init();
