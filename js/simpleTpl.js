var simpleTpl = function(tpl, data, render, contextName) {
    if (!tpl) {
        return false;
    }
    if (!data) {
        return (render ? $(tpl) : tpl);
    }
    if (!contextName) {
        contextName = '';
    }

    // сначала пройдёмся по условным операторам
    var ifInfo;
    while (ifInfo = (new RegExp('{% if (.*?) %}(.*?){% endif %}')).exec(tpl)) {
        var matched = data,
            deeps = ifInfo[1].split('.');

        for (var i=0; deeps[i]; i++) {
            if (matched[deeps[i]]) {
                matched = matched[deeps[i]];
            } else {
                matched = false;
                break;
            }
        }

        if (matched) {
            tpl = tpl.replace(ifInfo[0], simpleTpl(ifInfo[2], data));
        } else {
            tpl = tpl.replace(ifInfo[0], '');
        }
    }

    // теперь заменим просто переменные
    for (var field in data) if (data.hasOwnProperty(field)) {
        if (data[field] == null) {
            continue;
        }
        if ((typeof data[field]).toLowerCase() == 'object') {
            tpl = simpleTpl(tpl, data[field], false, contextName + field + '.');
        } else {
            var reg = new RegExp('{{ ' + contextName + field + ' }}', 'g');
            tpl = tpl.replace(reg, data[field]);
        }
    }
    if (contextName == '') {
        tpl = tpl.replace(/\{\{ .*? \}\}/g, '');
    }
    return (render ? $(tpl) : tpl);
}
