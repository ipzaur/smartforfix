var canHtml5 = !!(window.history && history.pushState);
function html5Hit(url)
{
    history.pushState(null, null, historyUrl);
    $.ajax({
        type     : 'GET',
        url      : '/gate/',
        data     : url + '&method=ajax',
        dataType : 'json',
        success  : function(json){
            if (json.error) {
            }
            if (json.block) {
                for (var blockId in json.block) if (json.block.hasOwnProperty(blockId)) {
                    var block = $('#' + blockId);
                    if (block.size() > 0) {
                        block.html(json.block[blockId]);
                    }
                }
            }
        }
    });
}