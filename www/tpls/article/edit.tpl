<form id="articleEdit" class="articleEdit" method="POST" action="{siteurl:}article/_edit/{article_edit.id:}/" data-article_id="{article_edit.id:}" enctype="multipart/form-data">
    <div class="content">
        <h1>{if:(article_edit.id:>0)}Редактирование{else:}Создание{:fi} статьи</h1>

        <div class="articleEdit_field"><input class="articleEdit_element-input" name="name" type="text" placeholder="Название статьи" value="{article_edit.name:}"></div>

        <div class="articleEdit_field">
            <div class="articleEdit_block-section">
                <select class="articleEdit_element-select" name="section">
                    <option value="0">Выберите раздел</option>
                    {section:}<option value="{id:}"{if:(id:=^article_edit.section_id:)} selected{:fi}>{name:}</option>{:section}
                </select>
            </div>
            <div class="articleEdit_block-model">Подходит для:
                <input id="info450" class="articleEdit_model" name="info[]" type="checkbox" value="450"{if:(article_edit.info450:=1)} checked{:fi}><label for="info450" class="articleEdit_modelName">ForTwo 450</label>
                <input id="info451" class="articleEdit_model" name="info[]" type="checkbox" value="451"{if:(article_edit.info451:=1)} checked{:fi}><label for="info451" class="articleEdit_modelName">ForTwo 451</label>
                <input id="info452" class="articleEdit_model" name="info[]" type="checkbox" value="452"{if:(article_edit.info452:=1)} checked{:fi}><label for="info452" class="articleEdit_modelName">Roadster</label>
                <input id="info454" class="articleEdit_model" name="info[]" type="checkbox" value="454"{if:(article_edit.info454:=1)} checked{:fi}><label for="info454" class="articleEdit_modelName">ForFour</label>
            </div>
        </div>

        <div class="articleEdit_field articleEdit_block">
            <select class="articleEdit_element-select" name="type">
                <option value="-1">Выберите тип статьи</option>
                <option value="0"{if:(article_edit.type:=0)} selected{:fi}>Оригинал</option>
                <option value="1"{if:(article_edit.type:=1)} selected{:fi}>Перевод</option>
                <option value="2"{if:(article_edit.type:=2)} selected{:fi}>Перепечатка</option>
            </select>
            <input class="articleEdit_element-input" name="ext_link" type="text" placeholder="Ссылка на источник" value="{article_edit.ext_link:}"{if:(article_edit.type:<1)} disabled{:fi}>
        </div>

        <div class="articleEdit_field"><textarea class="articleEdit_element-ta" name="content" placeholder="Текст статьи">{article_edit.content_source:}</textarea></div>

        <div class="articleEdit_field"><span class="articleEdit_upload fake_link">Загрузить фотографии<input class="articleEdit_uploader" name="upload[]" data-photo_action="upload" type="file" multiple></span></div>
        <div class="articleEdit_photos" data-article_elem="photos">
            {photos:}<div class="articleEdit_photo" data-photo_num="{_key:}"{if:(id:)} data-photo_id="{id:}"{:fi}><img class="articleEdit_photo_img" src="{^siteurl:}{path:}" data-photo_action="insert" title="Вставить фотографию в статью"><span class="fake_link-red" data-photo_action="delete">Удалить</span></div>{:photos}
        </div>
    </div>

    <div class="form_footer">
        <div class="content">
            <button class="form_save" name="submit" data-article_action="submit" type="button"{if:(article_edit.id:=0)} disabled{:fi}>Опубликовать</button><!-- span class="profile_errors h">Пожалуйста, <span class="profile_error">укажите имя</span -->
        </div>
    </div>
    <input id="content_id" name="content_id" type="hidden" value="{content_id:}">
</form>
