<form id="articleEdit" class="articleEdit" method="POST" action="{siteurl:}article/_edit/{articleEdit.id:}/" data-article_id="{articleEdit.id:}">
    <p class="articleEdit_field"><input class="articleEdit_element-input" name="name" type="text" placeholder="Название статьи" value="{articleEdit.name:}"></p>
    <p class="articleEdit_field">Тип статьи:
        <select class="articleEdit_element-select" name="type">
            <option value="0">Оригинал</option>
            <option value="1">Перевод</option>
            <option value="2">Перепечатка</option>
        </select>
        <input class="articleEdit_element-input{if:(articleEdit.type:=0)} h{:fi}" name="ext_link" type="text" placeholder="Ссылка на оригинал" value="{articleEdit.ext_link:}"{if:(articleEdit.type:=0)} disabled{:fi}>
    </p>
    <p class="articleEdit_field">Статья для:
        <label><input name="info[]" type="checkbox" value="450"{if:(articleEdit.info450:=1)} checked{:fi}> ForTwo 450</label>
        <label><input name="info[]" type="checkbox" value="451"{if:(articleEdit.info451:=1)} checked{:fi}> ForTwo 451</label>
        <label><input name="info[]" type="checkbox" value="452"{if:(articleEdit.info452:=1)} checked{:fi}> Roadster</label>
        <label><input name="info[]" type="checkbox" value="454"{if:(articleEdit.info454:=1)} checked{:fi}> ForFour</label>
    </p>
    <p class="articleEdit_field"><textarea class="articleEdit_element-ta" name="content_source">{articleEdit.content_source:}</textarea></p>
    <p class="articleEdit_field"><button name="submit" type="submit"{if:(articleEdit.id:=0)} disabled{:fi}>Сохранить</button></p>
</form>
