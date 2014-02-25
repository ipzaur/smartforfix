var user = {
    'tag' : {
        'avatar'   : false,
        'name'     : false,
        'about'    : false,
        'link_vk'  : false,
        'link_d2'  : false,
        'link_own' : false,
        'articles' : false
    },
    'tpl' :
        '<div class="user">' +
            '<img class="user_avatar" data-tpl="avatar"><p class="user_name" data-tpl="name"></p>' +
            '<div class="user_about" data-tpl="about"></div>' +
            '<div class="user_links">' +
                '<a class="user_link-vk" data-tpl="link_vk" target="_blank"></a>' +
                '<a class="user_link-d2" data-tpl="link_d2" target="_blank"></a>' +
                '<a class="user_link-own" data-tpl="link_own" target="_blank"></a>' +
            '</div>' +
            '<a class="user_articles" data-tpl="articles">Показать все статьи пользователя</a>' +
        '</div>'
    ,
    'destroy' : function() {
        for (var tagName in user.tag) if (user.tag.hasOwnProperty(tagName)) {
            user.tag[tagName] = false;
        }
    },
    'show' : function(info) {
        var content = $(user.tpl);
        for (var tagName in user.tag) if (user.tag.hasOwnProperty(tagName)) {
            user.tag[tagName] = content.find('[data-tpl="' + tagName + '"]').removeAttr('data-tpl');
        }

        user.tag['avatar'].attr('src', info.avatar);
        user.tag['name'].text(info.name);

        if ( info.about && (info.about != '') ) {
            var aboutText = info.about.replace("\n", "<br />");
            user.tag['about'].html(aboutText).removeAttr('data-tpl');
        } else {
            user.tag['about'].remove();
        }

        var links = ['link_vk', 'link_d2', 'link_own'];
        for (var i=0; i<3; i++) {
            if ( info[links[i]] && (info[links[i]] != '') ) {
                user.tag[links[i]].attr('href', info[links[i]]);
            } else {
                user.tag[links[i]].remove();
            }
        }

        user.tag['articles'].attr('href', SITEURL + 'by' + info.id + '/');

        lightbox.show(content, user.destroy);
    }
}

$(window).on('click', function(ev){
    var el = $(ev.target);
    if (el.is('[data-user_action="show"]')) {
        var userId = el.attr('data-user_id');
        if (userList[userId]) {
            user.show(userList[userId]);
        }
    }
});
