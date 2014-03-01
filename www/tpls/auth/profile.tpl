<div id="auth" class="auth-profile" data-auth_action="toggle">
    <img class="auth_avatar" src="{siteurl:}{user.avatar:}">
    <span class="auth_name">{user.name:}</span>
    <div class="bubble-grey" data-auth="menu">
        <a class="authProfile_link" href="/profile/">Профиль</a>
        {if:(user.grants:>1)}
            <a class="authProfile_link" href="/article/_edit/">Добавить статью</a>
            <a class="authProfile_link" href="/by{user.id:}/">Мои статьи</a>
        {:fi}
        <a class="authProfile_link" href="/fav/">Избранные статьи</a>
        <a class="authProfile_link-logout" href="/_auth/out/">Выйти</a>
    </div>
</div>
