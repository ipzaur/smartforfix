<?php
$engine->loadIface('article');

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
$pages_section = false;
if ( isset($engine->url[0]) && is_string($engine->url[0]) ) {
    if ($engine->url[0] == 'fav') {
        $getparam['favuser'] = $engine->auth->user['id'];
        $pages_section = 'fav';
    } else if (mb_substr($engine->url[0], 0, 2) == 'by') {
        $getparam['user_id'] = str_replace('by', '', $engine->url[0]);
        $pages_section = $engine->url[0];
    } else {
        foreach ($sections as $section) {
            if ($section['url'] == $engine->url[0]) {
                $cur_section = $section;
                $pages_section = $cur_section['url'];
                break;
            }
        }
        if ($cur_section !== false) {
            $getparam['section_id'] = $cur_section['id'];
        }
        $engine->tpl->addvar('cur_section', $cur_section);
    }
}
$getparam['hidden'] = 0;

$articles_count = $engine->article->getCount($getparam);

$articles_per_page = 10;

$cur_page = 1;
if ($articles_count > $articles_per_page) {
    if ( isset($engine->url[0]) && ctype_digit($engine->url[0]) ) {
        $cur_page = $engine->url[0];
    } else if ( isset($engine->url[1]) && ctype_digit($engine->url[1]) ) {
        $cur_page = $engine->url[1];
    }
    $max_page = ceil($articles_count / $articles_per_page);
    $engine->tpl->addvar('max_page', $max_page);
    for ($page=1; $page<=$max_page; $page++) {
        $url = ($pages_section !== false) ? $pages_section . '/' : '';
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
$engine->tpl->addvar('article_list', $article_list);

// список юзеров-авторов для клика по ним
$userList = array();
foreach ($article_list as $article) {
    if (isset($userList[$article['user']['id']])) {
        continue;
    }
    $userList[$article['user']['id']] = $engine->user->shortInfo($article['user']);
}
$engine->tpl->addvar('JS_userList', json_encode($userList));
