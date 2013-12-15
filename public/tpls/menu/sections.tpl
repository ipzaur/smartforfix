<div id="menuSection" class="menu-sections" data-sections_action="toggle">
    <span class="menu_current" data-sections_action="toggle">{if:(curSection)}{curSection.name:}{else:}Разделы{:fi}</span>
    <select class="menu_select">
        <option value="{siteurl:}">Все разделы</option>
        {section:}<option value="{^siteurl:}{url:}/">{name:}</option>{:section}
    </select>
    <div class="menu_items">
        <a class="menu_section" href="{siteurl:}">Все разделы</a>
        {section:}<a class="menu_section" href="{^siteurl:}{url:}/">{name:}</a>{:section}
    </div>
</div>
