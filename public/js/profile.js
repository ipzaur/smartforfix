var profile = {
    'tag' : {
        'main'   : false,
        'avatar' : false
    },
    'uploadAva' : function(ev) {
        uploader.do(ev, {
            'url' : SITEURL + '_ajax/profile/avatar/',
            'done' : function(avatar_path) {
                profile.tag.avatar.attr('src', avatar_path);
            }
        });
    },
    init : function(profileBlock) {
        profile.tag.main = profileBlock;
        profile.tag.avatar = profile.tag.main.find('#avatar');

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

var profileBlock = $('#profile');
if (profileBlock.size() > 0) {
    profile.init(profileBlock);
}
