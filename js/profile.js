var profile = {
    'tag' : {
        'main'   : false,
        'avatar' : false,
        'elem' : {
            'name'      : false,
            'about'     : false,
            'name'      : false,
            'social_vk' : false,
            'link_d2'   : false,
            'link_own'  : false
        }
    },

    'uploadAva' : function(ev) {
        uploader.do(ev, {
            'url' : SITEURL + '_ajax/profile/avatar/',
            'done' : function(avatar_path) {
                profile.tag.avatar.attr('src', avatar_path);
            }
        });
    },

    'preview' : function() {
        var userInfo = {
            'id'        : profile.tag.main.attr('data-user_id'),
            'avatar'    : profile.tag.avatar.attr('src'),
            'name'      : profile.tag.elem.name.val(),
            'about'     : profile.tag.elem['about'].val(),
            'link_d2'   : profile.tag.elem['link_d2'].val(),
            'link_own'  : profile.tag.elem['link_own'].val()
        };
        if ( (profile.tag.elem['social_vk'] != false) && profile.tag.elem['social_vk'].prop('checked') ) {
            userInfo['link_vk'] = profile.tag.elem['social_vk'].attr('data-social_href');
        }
        user.show(userInfo);
    },

    init : function(profileBlock) {
        profile.tag.main = profileBlock;
        profile.tag.avatar = profile.tag.main.find('#avatar');

        var elems = profile.tag.elem;
        for (var elemName in elems) if (elems.hasOwnProperty(elemName))  {
            elems[elemName] = profile.tag.main.find('[data-profile_elem="' + elemName + '"]');
        }

        profile.tag.main.on({
            'click' : function(ev) {
                var el = $(ev.target);
                if (el.is('[data-profile_action="preview"]')) {
                    profile.preview();
                }
            },
            'change' : function(ev) {
                var el = $(ev.target);
                if (el.is('[data-profile_action="avatar"]')) {
                    profile.uploadAva(ev);
                }
            },

        });
    }
}

var $profileBlock = $('#profile');
if ($profileBlock.length) {
    profile.init($profileBlock);
}
