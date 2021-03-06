<article id="article" class="article content" data-is_fav="{article.isfav:}" data-article_id="{article.id:}">
    <p class="breads"><a class="bread" href="{siteurl:}">Главная</a><span class="bread">{article.section:}<a class="bread-item" href="{^siteurl:}{url:}/">{name:}</a>{:article.section}</span>{if:(user.grants:>1)}<a class="bread">#{article.id:}</a>{:fi}</p>

    <h1 class="article_name">{article.name:}</h1>

    <div class="article_author">
        <img class="article_author_ava" src="{siteurl:}{article.user.avatar:}">
        <p class="article_author_name"><a data-user_action="show" data-user_id="{article.user.id:}">{article.user.name:}</a></p>
        <p class="artictle_type">{if:(article.type:=0)}автор{else:}{if:(article.type:=1)}перевод{else:}перепечатка{:fi}{:fi}</p>
    </div>

    <div class="article_content">{article.content:}</div>
    <div class="article_bottom">
        <div class="article_actions">{+fav:}{if:(isMobile:=false)}<button class="button-blue" article-action="print">Распечатать</button>{:fi}{if:(user.id:=article.user_id:)}<button class="button-blue" article-action="edit">Редактировать</button>{:fi}</div>

        {if:(article.type:>0)}<p class="article_source" data-text="Источник"><a class="article_source_link" href="{article.ext_link:}" target="_blank">{article.ext_link:}</a></p>{:fi}
    </div>
    {if:(article.tag)}
    <div class="article_tags">Тэги:{article.tag:} <a class="tag" href="/tag/{name:}/">{name:}</a>{:article.tag}</div>
    {:fi}
</article>
{if:(JS_userList:)}
    <script type="text/javascript">
        var userList = {JS_userList:};
    </script>
{:fi}
