var article = {
    id  : 0,
    $main : false,

    init : function() {
        with (article) {
            $main = $('#article');
            if (!$main.length) {
                return false;
            }
            id = $main.attr('data-article_id');

            $main.on({
                'click' : function(ev){
                    var $el = $(ev.target);
                    if ($el.is('[article-action]')) {
                        switch ($el.attr('article-action')) {
                            case 'cut'   : $el.parent().toggleClass('_expanded'); break;
                            case 'print' : window.print(); break;
                            case 'edit'  : window.location = SITEURL + 'article/_edit/' + article.id + '/'; break;
                        }
                    }
                }
            });
        }
    }
}

article.init();
