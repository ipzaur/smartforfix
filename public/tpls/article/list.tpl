{if:(cur_section.name:)}<h1 class="articleList_h1">{cur_section.name:}</h1>{:fi}
{if:(noarticles:)}
    <div class="info">
        <h2 class="info_title">Здесь пока пусто</h2>
        <div class="info_text">В этом разделе сохраняются статьи, помеченные Вами, как "избранное".</div>
    </div>
{else:}
    <div class="articleList">
        {article_list:}
            <div class="articleList_item clearable-after" data-article_id="{id:}" data-is_fav="{isfav:}">
                {if:(thumb)}<a class="articleList_photo" href="{^siteurl:}article/{url:}/"><img src="{^siteurl:}_r/180x130/{thumb.path:}"></a>{:fi}
                {if:(section)}<p class="articleList_section"><a class="articleList_sectionLink" href="{^siteurl:}{section.url:}/">{section.name:}</a></p>{:fi}
                <div class="articleList_stat">
                    <a class="articleList_author" data-user_action="show" data-user_id="{user.id:}">{user.name:}</a>
                    <!-- p class="articleList_comments">36</p -->
                </div>
                <div class="articleList_info">
                    <h2 class="articleList_name"><a href="{^siteurl:}article/{url:}/">{name:}</a> {+fav:}</h2>
                </div>
            </div>
        {:article_list}
    </div>
{:fi}

<script type="text/javascript">
    var userList = {JS_userList:};
</script>
{if:(pages)}{+paginator:}{:fi}
