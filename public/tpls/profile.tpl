<form id="profile" class="profile" method="POST" action="{siteurl:}profile/" enctype="multipart/form-data" data-user_id="{user.id:}">
    <div class="content"><h1 class="profile_h1">Личный профиль <a class="profile_prev" data-profile_action="preview">Предпросмотр</a></h1></div>
    <div class="content">
        <div class="profile_block-info">
            <div class="profile_ava">
                <img id="avatar" class="user_avatar" src="{if:(user.avatar)}{siteurl:}{user.avatar:}{else:}{noava:}{:fi}">
                <a class="profile_ava_upload">Сменить аватарку<input class="profile_upload" name="avatar" type="file" data-profile_action="avatar"></a>
            </div>
            <input data-profile_elem="name" name="name" value="{user.name:}" placeholder="Имя или никнейм">
            <textarea data-profile_elem="about" name="about" placeholder="Пара слов о себе">{user.about:}</textarea>
        </div>

        <div class="profile_block" data-label="Авторизация">
            {profile.social:}
                <div class="profile_social" data-label="Через {title:}">
                    {if:(social_url)}<a class="socialButton-{_key:} profile_auth" href="{social_url:}">Связать с {title:}</a>
                    {else:}<span class="profile_social_name">{name:}</span> <label class="profile_social_show"><input data-social_href="{url:}" data-profile_elem="social_{_key:}" name="social[{_key:}]" type="checkbox" value="1"{if:(show:=1)} checked{:fi}> Отображать в профиле</label>{:fi}
                </div>
            {:profile.social}
        </div>

        <div class="profile_block" data-label="Дополнительные ссылки">
            <label class="profile_link-d2"><input data-profile_elem="link_d2" name="link_d2" value="{user.link_d2:}" placeholder="Ссылка на персональную страницу на drive2.ru"></label>
            <label class="profile_link-own"><input data-profile_elem="link_own" name="link_own" value="{user.link_own:}" placeholder="Ссылка на личный сайт"></label>
        </div>
    </div>

    <div class="form_footer">
        <div class="content profile_block">
            <button class="form_save">Сохранить изменения</button><!-- span class="profile_errors h">Пожалуйста, <span class="profile_error">укажите имя</span -->
        </div>
    </div>
</form>