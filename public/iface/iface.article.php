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

    protected $order_fields = array('id', 'create_date');
    protected $group_fields = array('id');
    protected $get_fields = array(
        'id'      => array('type' => 'integer', 'many' => 1, 'check_single' => 1, 'notnull' => 1),
        'url'     => array('type' => 'string', 'check_single' => 1, 'notnull' => 1),
        'user_id' => array('type' => 'integer', 'notnull' => 1),
        'section_id' => array('type' => 'integer', 'notnull' => 0),
        'info450' => array('type' => 'integer'),
        'info451' => array('type' => 'integer'),
        'info452' => array('type' => 'integer'),
        'info454' => array('type' => 'integer')
/*,
        'tag'     => array('type' => 'string',  'join' => array(
            'table'    => 'tag',
            'key_main' => 'id',
            'key_join' => 'link_id',
            'field'    => 'name'
        ))*/
    );
    protected $save_fields = array(
        'name' => array('type' => 'string', 'notnull' => 1),
        'url' => array('type' => 'string', 'nutnull' => 1),
        'type' => array('type' => 'integer',),
        'content_source' => array('type' => 'string', 'nutnull' => 1),
        'content' => array('type' => 'string', 'nutnull' => 1),
        'ext_link' => array('type' => 'string', 'nutnull' => 0),
        'user_id' => array('type' => 'integer', 'notnull' => 1),
        'info450' => array('type' => 'integer'),
        'info451' => array('type' => 'integer'),
        'info452' => array('type' => 'integer'),
        'info454' => array('type' => 'integer'),
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
     * @param array articles - результат выборки. в процессе вернётся изменённый массив с авторами
     */
    protected function getAfter(&$articles = array())
    {
        $this->engine->loadIface('user');
        if (isset($articles['id'])) {
            $getparam = array('id' => $articles['user_id']);
            $articles['user'] = $this->engine->user->get($getparam);
        } else {
            foreach ($articles AS &$article) {
                $getparam = array('id' => $article['user_id']);
                $article['user'] = $this->engine->user->get($getparam);
            }
        }
    }


    public function __construct()
    {
    }
}
