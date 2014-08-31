var editor = {
    'list' : {},
    'init' : function(textarea) {
        if (!textarea) {
            return false;
        }

        var instance = {
            'tag' : {
                'main' : $('<div />').addClass('editor'),
                'area' : false,
                'button' : {
                    'b'     : false,
                    'i'     : false,
                    'a'     : false,
                    'video' : false,
                    'table' : false
                }
            },
            'butName' : {
                'b'     : 'Жирный',
                'i'     : 'Курсив',
                'a'     : 'Ссылка',
                'video' : 'Видео',
                'table' : 'Таблица'
            },

            'insertImg' : function(src, title) {
                if (!src) {
                    return false;
                }
                var posStart = instance.tag.area[0].selectionStart || 0,
                    posEnd = instance.tag.area[0].selectionEnd || 0;

                var insertText = "\n" + '<img src="' + src + '"';
                if ( (title != null) && (title != '') ){
                    insertText += ' title="' + title + '"';
                }
                insertText += ">\n";

                var selectedLength = posEnd - posStart;
                var parsedText = instance.tag.area.val().split('');
                parsedText.splice(posStart, selectedLength, insertText);
                parsedText = parsedText.join('');
                instance.tag.area.val(parsedText);
            },

            'insertLink' : function() {
                var posStart = instance.tag.area[0].selectionStart || 0,
                    posEnd = instance.tag.area[0].selectionEnd || 0,
                    link = null, descr = null;

                var selectedLength = posEnd - posStart;
                if (selectedLength > 0) {
                    var selected = instance.tag.area.val().split('').splice(posStart, selectedLength).join('');
                    if ( (selected.indexOf('http://') >= 0) || (selected.indexOf('https://') >= 0) ) {
                        link = selected;
                    } else {
                        descr = selected;
                    }
                }
                if (link == null) {
                    link = prompt('Введите ссылку');
                    if (link == null) return false;
                }
                if (descr == null) {
                    var descr = prompt('Описание ссылки');
                    if (descr == null) return false;
                }
                var parsedText = instance.tag.area.val().split('');
                parsedText.splice(posStart, selectedLength, '<a href="' + link + '">' + descr + '</a>');
                parsedText = parsedText.join('');
                instance.tag.area.val(parsedText);
            },

            'insertVideo' : function() {
                var posStart = instance.tag.area[0].selectionStart || 0,
                    posEnd = instance.tag.area[0].selectionEnd || 0,
                    link = null;

                var selectedLength = posEnd - posStart;
                if (selectedLength > 0) {
                    var selected = instance.tag.area.val().split('').splice(posStart, selectedLength).join('');
                    if ( (selected.indexOf('http://') >= 0) || (selected.indexOf('https://') >= 0) ) {
                        link = selected;
                    }
                }
                if (link == null) {
                    link = prompt('Введите ссылку на видео (YouTube)');
                    if (link == null) return false;
                    if ( (link.indexOf('http://') < 0) && (link.indexOf('https://') < 0) ) {
                        return false;
                    }
                }
                var parsedText = instance.tag.area.val().split('');
                parsedText.splice(posStart, selectedLength, '<video>' + link + '</video>');
                parsedText = parsedText.join('');
                instance.tag.area.val(parsedText);
            },

            'insertTable' : function() {
                var posStart = instance.tag.area[0].selectionStart || 0,
                    posEnd = instance.tag.area[0].selectionEnd || 0,
                    cols=3, rows = 3;

                var selectedLength = posEnd - posStart;
                cols = prompt('Количество столбцов');
                if (cols == null) return false;
                cols = parseInt(cols) || 3;
                rows = prompt('Количество строчек');
                if (rows == null) return false;
                rows = parseInt(rows) || 3;
                var insertedText = "<table>\n  <thead>\n    <tr>";
                for (var col=0; col<cols; col++) {
                    insertedText += "<th>Заголовок" + (col+1) + "</th>";
                }
                insertedText += "</tr>\n  </thead>\n  <tbody>\n";
                for (var row=0; row<rows; row++) {
                    insertedText += "    <tr>";
                    for (var col=0; col<cols; col++) {
                        insertedText += " <td></td> ";
                    }
                    insertedText += "</tr>\n";
                }
                insertedText += "  </tbody>\n</table>";

                var parsedText = instance.tag.area.val().split('');
                parsedText.splice(posStart, selectedLength, insertedText);
                parsedText = parsedText.join('');
                instance.tag.area.val(parsedText);
            },

            'insertPair' : function(tag) {
                var posStart = instance.tag.area[0].selectionStart || 0,
                    posEnd = instance.tag.area[0].selectionEnd || 0;
                if (posStart != posEnd) {
                    var charArray = instance.tag.area.val().split('');
                    charArray.splice(posEnd, 0, '</' + tag + '>');
                    charArray.splice(posStart, 0, '<' + tag + '>');
                    instance.tag.area.val(charArray.join(''));
                }
            },

            'init' : function(textarea) {
                instance.tag.area = textarea;
                var buttons = $('<div />').addClass('editor_buttons');
                for (var butLabel in instance.tag.button) if (instance.tag.button.hasOwnProperty(butLabel)) {
                    instance.tag.button[butLabel] = $('<button />')
                        .addClass('editor_button-' + butLabel)
                        .attr({'editor-action':butLabel, 'type':'button'})
                        .text(instance.butName[butLabel])
                        .appendTo(buttons);
                }
                buttons.appendTo(instance.tag.main);

                instance.tag.main.appendTo(instance.tag.area.parent());
                instance.tag.area.addClass('editor_ta').appendTo(instance.tag.main);

                instance.tag.main.on('click', function(ev){
                    var el = $(ev.target);
                    if (el.is('[editor-action]')) {
                        switch (el.attr('editor-action')) {
                            case 'b'     :
                            case 'i'     : instance.insertPair(el.attr('editor-action')); break;
                            case 'a'     : instance.insertLink(); break;
                            case 'video' : instance.insertVideo(); break;
                            case 'table' : instance.insertTable(); break;
                        }
                    }
                });

                return instance;
            }
        };
        var name = textarea.attr('name');
        editor.list[name] = instance.init(textarea);

        return editor.list[name];
    }
}
