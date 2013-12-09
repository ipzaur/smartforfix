<form id="articleEdit" class="articleEdit" method="POST" action="{siteurl:}article/_edit/{if:(article)}{article.id}/{:fi}">
    <p class="articleEdit_field"><input class="articleEdit_element-input" name="name" type="text" placeholder="Название статьи" value="{if:(article)}{article.name}{:fi}"></p>
    <p class="articleEdit_field">Тип статьи:
        <select class="articleEdit_element-select" name="type">
            <option value="0">Оригинал</option>
            <option value="1">Перевод</option>
            <option value="2">Перепечатка</option>
        </select>
        <input class="articleEdit_element-input" name="ext_link" type="text" placeholder="Ссылка на оригинал" value="{if:(article)}{article.ext_link}{:fi}">
    </p>
    <p class="articleEdit_field">Статья для:
        <label><input name="info[]" type="checkbox" value="450"> ForTwo 450</label>
        <label><input name="info[]" type="checkbox" value="451"> ForTwo 451</label>
        <label><input name="info[]" type="checkbox" value="452"> Roadster</label>
        <label><input name="info[]" type="checkbox" value="454"> ForFour</label>
    </p>
    <p class="articleEdit_field"><textarea class="articleEdit_element-ta" name="content_source" value="{if:(article)}{article.content_source}{:fi}"></textarea></p>
    <p class="articleEdit_field"><button name="submit" type="submit">Сохранить</button></p>
</form>
