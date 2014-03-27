<?php
/**
 * Interface Article
 * Интерфейс для работы со статьями
 * @author Alexey iP Subbota
 * @version 1.0
 */
class iface_article extends iface_base_entity
{
    public $engine = NULL;
    private $sectionCache = array();
    private $userCache = array();

    protected $order_fields = array('id', 'create_date');
    protected $group_fields = array('id');
    protected $get_fields = array(
        'id'      => array('type' => 'integer', 'many' => 1, 'check_single' => 1, 'notnull' => 1),
        'url'     => array('type' => 'string', 'check_single' => 1, 'notnull' => 1),
        'user_id' => array('type' => 'integer', 'notnull' => 1),
        'section_id' => array('type' => 'integer', 'notnull' => 0),
        'info' => array('type' => 'or_group', 'fields' => array(
            'info450' => array('type' => 'integer'),
            'info451' => array('type' => 'integer'),
            'info452' => array('type' => 'integer'),
            'info454' => array('type' => 'integer')
        )),
        'search' => array('type' => 'or_group', 'fields' => array(
            'name'           => array('type' => 'string'),
            'content_source' => array('type' => 'string')
        )),
        'favuser' => array('type' => 'integer', 'notnull' => 1, 'join' => array(
            'table'    => 'fav',
            'key_main' => 'id',
            'key_join' => 'article_id',
            'field'    => 'user_id'
        )),
        'tag'     => array('type' => 'string',  'join' => array(
            'table'    => 'tag',
            'key_main' => 'id',
            'key_join' => 'article_id',
            'field'    => 'name'
        )),
        'hidden' => array('type' => 'integer')
    );
    protected $save_fields = array(
        'name' => array('type' => 'string', 'notnull' => 1),
        'url' => array('type' => 'string', 'notnull' => 1),
        'type' => array('type' => 'integer',),
        'section_id' => array('type' => 'integer', 'notnull' => 0),
        'content_source' => array('type' => 'string', 'notnull' => 1),
        'content' => array('type' => 'string', 'notnull' => 1),
        'ext_link' => array('type' => 'string', 'notnull' => 0),
        'user_id' => array('type' => 'integer', 'notnull' => 1),
        'info450' => array('type' => 'integer'),
        'info451' => array('type' => 'integer'),
        'info452' => array('type' => 'integer'),
        'info454' => array('type' => 'integer'),
        'hidden' => array('type' => 'integer'),
        'create_date' => array('type' => 'datetime')
    );
    protected $table_name = 'article';

    protected function beforeSave(&$saveparam = array(), &$whereparam = array())
    {
        // откомпилируем статью, если она изменилась
        if ( isset($saveparam['content_source']) && (mb_strlen($saveparam['content_source']) > 0) ) {
            $this->engine->loadIface('string');
            $saveparam['content_source'] = $this->engine->string->removeTags($saveparam['content_source'], array('a','b','i','img','video','table'));
            $content = $saveparam['content_source'];

            // видюшечки
            $content = preg_replace('~(\s*)?(<video>[^<]*</video>)(\s*)~isu', '$2', $content);
            if (preg_match_all('~<video>(.*?)</video>~su', $content, $article_videos, PREG_SET_ORDER) !== false) {
                foreach ($article_videos as &$video) {
                    $tag = false;
                    if (mb_strpos($video[1], 'youtube') !== false) {
                        if (preg_match('~v=(.*?)(\&|$)~isu', $video[1], $video_id) != false) {
                            $tag =
                                '<div class="article_video">' .
                                    '<iframe width="560" height="420" src="//www.youtube.com/embed/' . $video_id[1] . '" frameborder="0" allowfullscreen></iframe>' .
                                '</div>';
                        }
                    }
                    if ($tag === false) {
                        $content = str_replace($video[0], '', $content);
                        continue;
                    }
                    $content = str_replace($video[0], '</p>' . $tag . '<p class="article_p">', $content);
                }
            }

            // таблички
            $content = preg_replace('~(\s*)?(<table[^>]*>.*?</table>)(\s*)~isu', '$2', $content);
            if (preg_match_all('~<table([^>]*)>(.*?)</table>~su', $content, $article_tables, PREG_SET_ORDER) !== false) {
                foreach ($article_tables as &$table) {
                    $tag = trim($table[2]);
                    $tag = preg_replace('~(<tr>|<\/td>|<\/th>).*?(<td|<th|<\/tr>)~isu', "$1$2", $tag);
                    $tag = preg_replace('~(<thead>|<tbody>).*?(<tr>)~isu', "$1$2", $tag);
                    $tag = preg_replace('~(</thead>).*?(<tbody>)~isu', "$1$2", $tag);
                    $tag = preg_replace('~(<\/tr>).*?(<\/thead>|<\/tbody>|<tr>)~isu', "$1$2", $tag);

                    $tag_class = 'article_table';
                    
                    if ($table[1] !== '') {
                        if (preg_match('~center="(\d+)"~', $table[1], $center)) {
                            $tag_class .= ' table-center';
                            if ($center[1] > 1) {
                                $tag_class .= $center[1];
                            }
                        }
                    }
                    $tag = '<table class="' . $tag_class . '">' . $tag . '</table>';

                    $content = str_replace($table[0], '</p>' . $tag . '<p class="article_p">', $content);
                }
            }

            if ( isset($whereparam['id']) && ($whereparam['id'] > 0) ) {
                // посмотрим ссылки-связи
                $this->engine->loadIface('article_link');
                $this->engine->article_link->delete(array('article_id' => $whereparam['id']));
                if (preg_match_all('~<a\s+href="(\d+)">~su', $content, $links, PREG_SET_ORDER) !== false) {
                    foreach ($links as $link) {
                        $linked = $this->get(array('id' => $link[1]));
                        if ($linked !== false) {
                            $linkparam = array(
                                'article_id' => $whereparam['id'],
                                'link_id'    => $linked['id']
                            );
                            $this->engine->article_link->save($linkparam);
                            $content = str_replace($link[0], '<a href="' . $this->engine->config['siteurl'] . 'article/' . $linked['url'] . '/">', $content);
                        }
                    }
                }

                // теперь картиночки
                $this->engine->loadIface('media');
                $getparam = array(
                    'article_id' => $whereparam['id'],
                    'hidden' => 0
                );
                $photos = $this->engine->media->get($getparam);
                if (count($photos) > 0) {
                    $content = preg_replace('~(\s*)?(<img[^>]*>)(\s*)~su', '$2', $content);
                    if (preg_match_all('~<img.*?src="(\d+)".*?(title=".*?".*?|)>~su', $content, $article_images, PREG_SET_ORDER) !== false) {
                        foreach ($article_images as &$image) {
                            if (isset($photos[$image[1]-1])) {
                                $imgsrc = $this->engine->config['siteurl'] . $photos[$image[1]-1]['path'];
                                $tag = '<div class="article_photo"><a href="' . $imgsrc . '" target="_blank"><img class="article_img" src="' . $imgsrc . '"></a>';
                                if (!empty($image[2])) {
                                    $title = preg_replace('~title="(.*?)"~su', '$1', $image[2]);
                                    if ($title != '') {
                                        $tag .= '<p class="article_imgDesc">' . str_replace('"', '&quot;', $title) . '</p>';
                                    }
                                }
                                $tag .= '</div>';
                                $content = str_replace($image[0], '</p>' . $tag . '<p class="article_p">', $content);
                            } else {
                                $content = str_replace($image[0], '', $content);
                            }
                        }
                    }
                } else {
                    $content = preg_replace('~<img[^>]*>~su', '', $content);
                }
            } else {
                $content = preg_replace('~<img[^>]*>~su', '', $content);
            }
            $content = str_replace(array("\r\n\r\n", "\n\n"), '</p><p class="article_p">', $content);
            $content = '<p class="article_p">' . str_replace(array("\r\n", "\n"), '<br />', $content) . '</p>';
            $content = str_replace('<p class="article_p"></p>', '', $content);
            $saveparam['content'] = $content;
        }

        if (isset($saveparam['name'])) {
            $article = false;
            if ( isset($whereparam['id']) && ($whereparam['id'] > 0) ) {
                $article = $this->get(array('id' => $whereparam['id']));
            }

            if ( ($article === false) || ($article['name'] != $saveparam['name']) ) {
                $this->engine->loadIface('translit');

                $saveparam['url'] = $this->engine->translit->convert(mb_strtolower($saveparam['name']));
                $saveparam['url'] = str_replace(array('-', ' '), '_', $saveparam['url']);
                $saveparam['url'] = preg_replace('~[^a-zA-Zа-яА-Я0-9_]~su', '', $saveparam['url']);

                $getparam = array('url' => $saveparam['url']);
                if (!empty($whereparam['id'])) {
                    $getparam['id'] = array(
                        '_operator' => '!=',
                        '_value' => $whereparam['id']
                    );
                }
                $exists = $this->get($getparam);
                if ($exists !== false) {
                    $getparam['url'] = $saveparam['url'] . '-%';
                    $exists = $this->get($getparam);
                    $count = count($exists);
                    $saveparam['url'] .= '-' . ($count + 1);
                }

                if ( ($article !== false) && ($article['url'] == $saveparam['url']) ) {
                    unset($saveparam['url']);
                }
            }
        }

        if ( isset($saveparam['type']) && ($saveparam['type'] == 0) ) {
            $saveparam['ext_link'] = '';
        }

        return true;
    }

    protected function afterSave($id, &$saveparam, &$whereparam)
    {
        // если у нас статья поменяля УРЛ, то надо поменять сылки на статью у статей, которые ссылаются на эту
        if ( isset($saveparam['url']) && !empty($whereparam['id']) ) {
            $this->engine->loadIface('article_link');
            $links = $this->engine->article_link->get(array('link_id' => $id));
            if ($links !== false) {
                $articleparam = array('id' => array());
                foreach ($links as $link) {
                    $articleparam['id'][] = $link['article_id'];
                }
                $articles = $this->get($articleparam);
                foreach ($articles as $linked) {
                    $this->save(array('content_source' => $linked['content_source']), array('id' => $linked['id']));
                }
            }
        }

        // сохраним тэги
        if (isset($saveparam['tag'])) {
            $this->engine->loadIface('tag');
            $this->engine->tag->delete(array('article_id' => $id));
            foreach ($saveparam['tag'] as $tag) {
                $tag = trim($tag);
                $tagparam = array(
                    'article_id' => $id,
                    'name' => $tag
                );
                $this->engine->tag->save($tagparam);
            }
        }

        // дальнейшая часть нам нужна для загрузки фоточек для новой статьи
        if ( !empty($whereparam['id']) || !isset($_COOKIE['temp_article'])) {
            return true;
        }

        $upload_path = $this->engine->config['sitepath'] . $this->engine->config['upload_dir'] . $_COOKIE['temp_article'] . '/';
        if (!file_exists($upload_path)) {
            return true;
        }

        $files = array_diff( scandir($upload_path), array('.', '..') );
        if (count($files) == 0) {
            return true;
        }

        $this->engine->loadIface('media');
        $article_path = $this->engine->config['sitepath'] . $this->engine->config['article_dir'] . $id;
        if (!file_exists($article_path)) {
            mkdir($article_path, 0777, true);
        }
        foreach ($files as $file) {
            $newPath = $article_path . '/' . $file;
            rename($upload_path . $file, $newPath);
            $mediaparam = array(
                'article_id' => $id,
                'path' => $this->engine->config['article_dir'] . $id . '/' . $file,
                'user_id' => $this->engine->auth->user['id']
            );
            $this->engine->media->save($mediaparam);
        }

        $whereparam['id'] = $id;
        $saveparam = array('content_source' => $saveparam['content_source']);
        $this->save($saveparam, $whereparam);

        setcookie('temp_article', '', time() - 100, '/', $this->engine->config['sitedomain']);

        return true;
    }

    /**
     * Дополнительная функция по выборке статей
     * @param array articles - результат выборки. в процессе вернётся изменённый массив с авторами и избранным
     */
    protected function getAfter(&$articles = array())
    {
        $this->engine->loadIface('user');
        $single = isset($articles['id']);
        if ($single) {
            $articles = array($articles);
        }

        if ($this->engine->auth->user) {
            $this->engine->loadIface('fav');
            $favparam = array('user_id' => $this->engine->auth->user['id']);
            $favs = $this->engine->fav->get($favparam);
            if ($favs !== false) {
                $favarticle = array();
                foreach($favs as $fav) {
                    $favarticle[] = $fav['article_id'];
                }
            }
        }

        $this->engine->loadIface('media');
        foreach ($articles as &$article) {
            if (!isset($this->userCache[$article['user_id']])) {
                $getparam = array('id' => $article['user_id']);
                $this->userCache[$article['user_id']] = $this->engine->user->get($getparam);
            }
            $article['user'] = $this->userCache[$article['user_id']];
            $article['section'] = $article['section_id'] ? $this->sectionCache[$article['section_id']] : null;

            $article['isfav'] = intval( isset($favarticle) && in_array($article['id'], $favarticle) );

            $getparam = array(
                'article_id' => $article['id'],
                'hidden'     => 0
            );
            $article['media'] = $this->engine->media->get($getparam);
        }

        if ($single) {
            $articles = $articles[0];

            $this->engine->loadIface('tag');
            $getparam = array('article_id' => $articles['id']);
            $articles['tag'] = $this->engine->tag->get($getparam);
        }
    }


    public function __construct()
    {
    }

    public function start()
    {
        $this->engine->loadIface('section');
        $sections = $this->engine->section->get(array('hidden' => 0));
        if ($sections !== false) {
            foreach ($sections as $section) {
                $this->sectionCache[$section['id']] = $section;
            }
        }
    }
}
