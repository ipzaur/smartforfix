<div class="paginator">
    {if:(cur_page:>1)}<a class="paginator_page" href="{siteurl:}{prev_page:}">Предыдущая</a>{:fi}
    <span class="paginator_pages">
        {pages:}
            {if:(_value:=sep)}<span class="paginator_dots">...</span>
            {else:}
                {if:(_key:=^cur_page:)}<span class="paginator_page _cur">{_key:}</span>
                {else:}<a class="paginator_page" href="{^siteurl:}{_value:}">{_key:}</a>
                {:fi}
            {:fi}
        {:pages}
    </span>
    {if:(next_page:)}<a class="paginator_page" href="{siteurl:}{next_page:}">Следующая</a>{:fi}
</div>
