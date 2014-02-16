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
        'favuser' => array('type' => 'integer', 'notnull' => 1, 'join' => array(
            'table'    => 'fav',
            'key_main' => 'id',
            'key_join' => 'article_id',
            'field'    => 'user_id'
        )),
        'hidden' => array('type' => 'integer')
    );
    protected $save_fields = array(
        'name' => array('type' => 'string', 'notnull' => 1),
        'url' => array('type' => 'string', 'notnull' => 1),
        'type' => array('type' => 'integer',),
        'section_id' => array('type' => 'integer',),
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
        if ( isset($saveparam['content_source']) && (mb_strlen($saveparam['content_source']) > 0) ) {
            $content = $saveparam['content_source'];
            $content = preg_replace('~<script>(.*?)</script>~', '', $content);
            if ( isset($whereparam['id']) && ($whereparam['id'] > 0) ) {
                $this->engine->loadIface('media');
                $getparam = array(
                    'article_id' => $whereparam['id'],
                    'hidden' => 0
                );
                $photos = $this->engine->media->get($getparam);
                if (count($photos) > 0) {
                    if (preg_match_all('~<img.*?src="(\d+)".*?(title=".*?".*?|)>~su', $content, $articleImages, PREG_SET_ORDER) !== false) {
                        foreach ($articleImages as &$image) {
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
                                $content = str_replace($image[0], $tag, $content);
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
            $content = '<p class="article_p">' . str_replace(array("\r\n", "\n"), '</p><p class="article_p">', $content) . '</p>';
            $saveparam['content'] = $content;
        }
        if (isset($saveparam['name'])) {
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
        }
        if ( isset($saveparam['type']) && ($saveparam['type'] == 0) ) {
            $saveparam['ext_link'] = '';
        }

        return true;
    }

    protected function afterSave($id, &$saveparam, &$whereparam)
    {
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
            $media = $this->engine->media->get($getparam);
            if ($media !== false) {
                $article['thumb'] = $media[0];
            }
        }

        if ($single) {
            $articles = $articles[0];
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
