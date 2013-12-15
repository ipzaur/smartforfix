<div class="article" data-is_fav="0">
    <p class="breads"><a class="bread" href="{siteurl:}">Главная</a><a class="bread" href="">Подключение и разводка</a></p>

    <h1>{article.name:}</h1>

    <div class="article_author">
        <img class="article_author_ava" src="{siteurl:}{article.user.avatar:}">
        <p class="article_author_name"><a href="">{article.user.name:}</a></p>
        <p class="artictle_type">перевод</p>
    </div>

    <div class="article_content">{article.content:}
        {if:(article.type:>0)}<p class="article_source">Источник: <a class="article_source_link" href="{article.ext_link:}">{article.ext_link:}</a></p>{:fi}
    </div>

    <div class="article_actions">{+fav:}<button class="toprint">Распечатать</button></div>
    <div class="article_tags">Тэги: <a class="tag" href="">самара</a> <a class="tag" href="">Струковский</a> <a class="tag" href="">история</a></div>
</div>
