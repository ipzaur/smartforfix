{if:(cur_section.name:)}<h1 class="articleList_h1 content">{cur_section.name:}</h1>{:fi}
{if:(noarticles:)}
    <div class="info content">
        <h2 class="info_title">Здесь пока пусто</h2>
        <div class="info_text">В этом разделе сохраняются статьи, помеченные Вами, как "избранное".</div>
    </div>
{else:}
    <div class="articleList">
        {article_list:}
            <div class="articleList_item" data-article_id="{id:}" data-is_fav="{isfav:}">
                <div class="content">
                    {if:(media:)}
                        <a href="{^siteurl:}article/{url:}/" class="articleList_photos">
                            {media:}<img class="articleList_photo" src="{^siteurl:}_r/116x70x1/{path:}">{:media}
                            <span class="articleList_countPhotos">{count_media:}</span>
                        </a>
                    {:fi}
                    <div class="articleList_info">
                        {if:(section:)}<a class="articleList_section" href="{^siteurl:}{section.url:}/">{section.name:}</a>{:fi}
                        <h2 class="articleList_name"><a href="{^siteurl:}article/{url:}/">{name:}</a></h2>
                    </div>
                </div>
            </div>
        {:article_list}
    </div>
{:fi}

{if:(pages:)}
    <div class="content">{+paginator:}</div>
{:fi}
