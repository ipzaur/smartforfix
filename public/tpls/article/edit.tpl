<form id="articleEdit" class="articleEdit" method="POST" action="{siteurl:}article/_edit/{article_edit.id:}/" data-article_id="{article_edit.id:}" enctype="multipart/form-data">
    <p class="articleEdit_field"><input class="articleEdit_element-input" name="name" type="text" placeholder="Название статьи" value="{article_edit.name:}"></p>
    <p class="articleEdit_field">Раздел:
        <select class="articleEdit_element-select" name="section">
            <option value="0">не выбран</option>
            {section:}<option value="{id:}"{if:(id:=^article_edit.section_id:)} selected{:fi}>{name:}</option>{:section}
        </select>
        Тип статьи:
        <select class="articleEdit_element-select" name="type">
            <option value="0"{if:(article_edit.type:=0)} selected{:fi}>Оригинал</option>
            <option value="1"{if:(article_edit.type:=1)} selected{:fi}>Перевод</option>
            <option value="2"{if:(article_edit.type:=2)} selected{:fi}>Перепечатка</option>
        </select>
        <input class="articleEdit_element-input{if:(article_edit.type:=0)} h{:fi}" name="ext_link" type="text" placeholder="Ссылка на оригинал" value="{article_edit.ext_link:}"{if:(article_edit.type:=0)} disabled{:fi}>
    </p>
    <p class="articleEdit_field">Статья для:
        <label><input name="info[]" type="checkbox" value="450"{if:(article_edit.info450:=1)} checked{:fi}> ForTwo 450</label>
        <label><input name="info[]" type="checkbox" value="451"{if:(article_edit.info451:=1)} checked{:fi}> ForTwo 451</label>
        <label><input name="info[]" type="checkbox" value="452"{if:(article_edit.info452:=1)} checked{:fi}> Roadster</label>
        <label><input name="info[]" type="checkbox" value="454"{if:(article_edit.info454:=1)} checked{:fi}> ForFour</label>
    </p>

    <p class="articleEdit_field"><textarea class="articleEdit_element-ta" name="content">{article_edit.content_source:}</textarea></p>

    <p class="articleEdit_field"><span class="articleEdit_upload fake_link">Загрузить фотографии<input class="articleEdit_uploader" name="upload[]" type="file" multiple></span></p>
    <div class="articleEdit_photos" data-article_elem="photos">
        {photos:}<div class="articleEdit_photo" data-photo_num="{_key:}"{if:(id:)} data-photo_id="{id:}"{:fi}><img class="articleEdit_photo_img" src="{^siteurl:}{path:}" data-photo_action="insert" title="Вставить фотографию в статью"><span class="fake_link-red" data-photo_action="delete">Удалить</span></div>{:photos}
    </div>

    <p class="articleEdit_field"><button name="submit" data-article_action="submit" type="button"{if:(article_edit.id:=0)} disabled{:fi}>Сохранить</button></p>
    <input id="content_id" name="content_id" type="hidden" value="{content_id:}">
</form>
