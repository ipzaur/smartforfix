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
                    'b'   : false,
                    'i'   : false,
                    'a'   : false,
                    'img' : false
                }
            },
            'insertImg' : function(img) {
                var posStart = instance.tag.area[0].selectionStart || 0,
                    posEnd = instance.tag.area[0].selectionEnd || 0,
                    link = null;
                if (!img) {
                    link = prompt('Ссылка на изображение');
                }
                if (link == null) return false;
                var title = prompt('Комментарий к изображению (не обязательно)');

                var selectedLength = posEnd - posStart;
                var parsedText = instance.tag.area.val().split('');
                parsedText.splice(posStart, selectedLength, '<img src="' + link + '" title="' + title + '">');
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
                for (var butName in instance.tag.button) if (instance.tag.button.hasOwnProperty(butName)) {
                    instance.tag.button[butName] = $('<button />')
                        .attr({'editor-action':butName, 'type':'button'})
                        .text(butName)
                        .appendTo(instance.tag.main);
                }

                instance.tag.main.appendTo(instance.tag.area.parent());
                instance.tag.area.appendTo(instance.tag.main);

                instance.tag.main.on('click', function(ev){
                    var el = $(ev.target);
                    if (el.is('[editor-action]')) {

                        switch (el.attr('editor-action')) {
                            case 'b' :
                            case 'i' : instance.insertPair(el.attr('editor-action')); break;
                            case 'a' : instance.insertLink(); break;
                            case 'img' : instance.insertImg(); break;
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
