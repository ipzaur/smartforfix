$(window).on('click', function(ev){
    var el = $(ev.target)
    if (el.hasClass('fav')) {
        var article = el.closest('[data-article_id]');
        $.ajax({
            type     : 'POST',
            url      : '/_ajax/articlefav/',
            data     : 'article_id=' + article.attr('data-article_id'),
            dataType : 'json',
            success  : function(json){
                var fav = parseInt(article.attr('data-is_fav'));
                article.attr('data-is_fav', !fav|0 );
            }
        });
    }
});
