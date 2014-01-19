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


    public function beforeSave(&$saveparam = array(), &$whereparam = array())
    {
        if ( isset($saveparam['content_source']) && (mb_strlen($saveparam['content_source']) > 0) ) {
            $content = $saveparam['content_source'];
            $content = preg_replace('~<img[^>]*>~su', '<p class="article_img">$1</p>', $content);
            $content = '<p class="article_p">' . str_replace(array("\r\n", "\n"), '</p><p class="article_p">', $content) . '</p>';
            $saveparam['content'] = $content;
        }
        if (isset($saveparam['name'])) {
            $this->engine->loadIface('translit');

            $saveparam['url'] = $this->engine->translit->convert(mb_strtolower($saveparam['name']));
            $saveparam['url'] = str_replace(' ', '_', $saveparam['url']);
        }
        if ($saveparam['type'] == 0) {
            $saveparam['ext_link'] = '';
        }

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

        foreach ($articles as &$article) {
            if (!isset($this->userCache[$article['user_id']])) {
                $getparam = array('id' => $article['user_id']);
                $this->userCache[$article['user_id']] = $this->engine->user->get($getparam);
            }
            $article['user'] = $this->userCache[$article['user_id']];
            $article['section'] = $article['section_id'] ? $this->sectionCache[$article['section_id']] : null;

            $article['isfav'] = intval( isset($favarticle) && in_array($article['id'], $favarticle) );
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
        foreach ($sections as $section) {
            $this->sectionCache[$section['id']] = $section;
        }
    }
}
