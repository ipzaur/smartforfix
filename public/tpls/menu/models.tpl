<div id="menuModel" class="menu-models">
    <span class="menu_current">Модели Smart{menu_model:}{if:(show:=1)}<span class="menu_current_model" data-model_remove="{_key:}">{name:}</span>{:fi}{:menu_model}</span>
    <select class="menu_select" size="1" multiple>
        {menu_model:}<option value="{_key:}"{if:(show:=1)} selected{:fi}>{name:}</option>{:menu_model}
    </select>
    <div class="menu_items">
        {menu_model:}<input class="menu_model_cb" id="model{_key:}" type="checkbox" data-model_show="{_key:}"{if:(show:=1)} checked{:fi}><label for="model{_key:}" class="menu_model-{_key:}" title="{name:}"></label>{:menu_model}

        <a class="menu_test" href="/model_test/">Как узнать модель?</a>
    </div>
</div>
