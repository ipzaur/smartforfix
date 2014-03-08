var article = {
    'id'  : 0,
    'tag' : {
        'main' : false
    },

    'init' : function() {
        article.tag.main = $('#article');
        if (article.tag.main.size() == 0) {
            return false;
        }
        article.id = article.tag.main.attr('data-article_id');

        article.tag.main.on({
            'click' : function(ev){
                var el = $(ev.target);
                if (el.is('[article-action]')) {
                    switch (el.attr('article-action')) {
                        case 'print' : window.print(); break;
                        case 'edit'  : window.location = SITEURL + 'article/_edit/' + article.id + '/'; break;
                    }
                }
            }
        });
    }
}

article.init();
