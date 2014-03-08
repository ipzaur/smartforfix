<?php
$engine->loadIface('article');
$engine->loadIface('user');

$getparam = array('info' => array());
$allmodels = true;
foreach ($menu_models as $model=>$menu) {
    if ($menu['show'] == 1) {
        $getparam['info']['info' . $model] = 1;
    } else {
        $allmodels = false;
    }
}
if ($allmodels == true) {
    $getparam = array();
}

$cur_section = false;
$page_index = 0;
if ( isset($engine->url[0]) && is_string($engine->url[0]) ) {
    $page_index = 1;

    // сначала посмотрим, открыли мы страницу с избранным или нет
    if ($engine->url[0] == 'fav') {
        $getparam['favuser'] = $engine->auth->user['id'];
        $cur_section = array(
            'url'  => 'fav',
            'name' => 'Избранные статьи'
        );

    // если не избранное, то может смотрим статьи конкретного юзера
    } else if ($engine->url[0] == 'by') {
        $getparam['user_id'] = $engine->url[1];
        $author = $engine->user->get(array('id' => $getparam['user_id']));
        $cur_section = array(
            'url'  => $engine->url[0] . '/' . $author['id'],
            'name' => 'Статьи от ' . $author['name']
        );
        $page_index = 2;

    // или может делаем поиск по тэгу
    } else if ($engine->url[0] == 'tag') {
        $getparam['tag'] = urldecode($engine->url[1]);
        $cur_section = array(
            'url'  => $engine->url[0] . '/' . $engine->url[1],
            'name' => 'Статьи по тэгу ' . $getparam['tag']
        );
        $page_index = 2;

    // значит наверное просто список смотрим в каком-то разделе или на главной
    } else {
        foreach ($sections as $section) {
            if ($section['url'] == $engine->url[0]) {
                $cur_section = $section;
                break;
            }
        }
        if ($cur_section !== false) {
            $getparam['section_id'] = $cur_section['id'];
        }
    }
}
$engine->tpl->addvar('cur_section', $cur_section);
$getparam['hidden'] = 0;

$articles_count = $engine->article->getCount($getparam);
// если есть статьи по нашим критериям, то соберём пагинатор и выведем статьи
if ($articles_count > 0) {
    $articles_per_page = 10;

    $cur_page = 1;
    if ($articles_count > $articles_per_page) {
        if ( isset($engine->url[$page_index]) && ctype_digit($engine->url[$page_index]) ) {
            $cur_page = $engine->url[$page_index];
        }
        $max_page = ceil($articles_count / $articles_per_page);
        $engine->tpl->addvar('max_page', $max_page);
        for ($page=1; $page<=$max_page; $page++) {
            $url = ($cur_section !== false) ? $cur_section['url'] . '/' : '';
            $pages[$page] = $url . ($page > 1 ? $page . '/' : '');
        }
        if ($cur_page > 1) {
            $engine->tpl->addvar('prev_page', $pages[$cur_page - 1]);
        }
        if ($cur_page < $max_page) {
            $engine->tpl->addvar('next_page', $pages[$cur_page + 1]);
        }
        $engine->tpl->addvar('pages', $pages);
    }
    $engine->tpl->addvar('cur_page', $cur_page);

    $article_list = $engine->article->get($getparam, array('create_date' => 'desc'), false, $articles_per_page, $cur_page);

    // список юзеров-авторов для клика по ним
    $userList = array();
    foreach ($article_list as $article) {
        if (isset($userList[$article['user']['id']])) {
            continue;
        }
        $userList[$article['user']['id']] = $engine->user->shortInfo($article['user']);
    }
    $engine->tpl->addvar('JS_userList', json_encode($userList));
} else {
    $article_list = true;
    $engine->tpl->addvar('noarticles', true);
}
$engine->tpl->addvar('article_list', $article_list);
