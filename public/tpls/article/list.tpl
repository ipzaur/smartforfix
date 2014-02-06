<div class="articleList">
    {article_list:}
        <div class="articleList_item clearable-after" data-article_id="{id:}" data-is_fav="{isfav:}">
            <div class="articleList_stat">
                <a class="articleList_author">{user.name:}</a>
                <!-- p class="articleList_comments">36</p -->
            </div>
            {if:(thumb)}<a class="articleList_photo" href="{^siteurl:}article/{url:}/"><img src="{^siteurl:}_r/180x130/{thumb.path:}"></a>{:fi}
            <div class="articleList_info">
                {if:(section)}<a class="articleList_section" href="{^siteurl:}{section.url:}/">{section.name:}</a>{:fi}
                <h2 class="articleList_name"><a href="{^siteurl:}article/{url:}/">{name:}</a> {+fav:}</h2>
            </div>
        </div>
    {:article_list}
</div>
{if:(pages)}{+paginator:}{:fi}
