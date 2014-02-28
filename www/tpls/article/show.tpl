<div class="article" data-is_fav="{article.isfav:}" data-article_id="{article.id:}">
    <p class="breads"><a class="bread" href="{siteurl:}">Главная</a>{if:(article.section_id:>0)}<a class="bread" href="{siteurl:}{article.section.url:}/">{article.section.name:}</a>{:fi}</p>

    <h1 class="article_name">{article.name:}</h1>

    <div class="article_author">
        <img class="article_author_ava" src="{siteurl:}{article.user.avatar:}">
        <p class="article_author_name"><a data-user_action="show" data-user_id="{article.user.id:}">{article.user.name:}</a></p>
        <p class="artictle_type">{if:(article.type:=0)}автор{else:}{if:(article.type:=1)}перевод{else:}перепечатка{:fi}{:fi}</p>
    </div>

    <div class="article_content">{article.content:}
        {if:(article.type:>0)}<p class="article_source">Источник: <a class="article_source_link" href="{article.ext_link:}">{article.ext_link:}</a></p>{:fi}
    </div>

    <div class="article_actions">{+fav:}<!--button class="toprint">Распечатать</button--></div>
    <!-- div class="article_tags">Тэги: <a class="tag" href="">самара</a> <a class="tag" href="">Струковский</a> <a class="tag" href="">история</a></div -->
</div>
{if:(JS_userList:)}
    <script type="text/javascript">
        var userList = {JS_userList:};
    </script>
{:fi}
