<div class="articleList">
    {article_list:}
    <div class="articleList_item clearable-after" data-article_id="{id:}" data-is_fav="0">
        <div class="articleList_stat">
            <a class="articleList_author" href="">{user.name:}</a>
            <p class="articleList_comments">36</p>
        </div>
        <a class="articleList_photo" href="{^siteurl:}article/{url:}/"><img src="/include/articles/01/01.jpg"></a>
        <div class="articleList_info">
            {if:(section)}<a class="articleList_section" href="{^siteurl:}{section.url:}/">{section.name:}</a>{:fi}
            <h2 class="articleList_name"><a href="{^siteurl:}article/{url:}/">{name:}</a> {+fav:}</h2>
        </div>
    </div>
    {:article_list}
</div>
{if:(pages)}{+paginator:}{:fi}
