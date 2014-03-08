<?php
/**
 * Interface Tag
 * Интерфейс для работы с тэгами
 * @author Alexey iP Subbota
 * @version 1.0
 */
class iface_tag extends iface_base_entity
{
    public $engine = NULL;

    protected $order_fields = array('article_id', 'name', 'tag_count');
    protected $group_fields = array('name');
    protected $get_fields = array(
        'article_id' => array('type' => 'integer', 'notnull' => 1),
        'name'    => array('type' => 'string',  'notnull' => 1),
        'user_id' => array('type' => 'integer', 'notnull' => 1, 'join' => array(
            'table'    => 'article',
            'key_main' => 'article_id',
            'key_join' => 'id',
            'field'    => 'user_id'
        ))
    );

    protected $save_fields = array(
        'name' => array('type' => 'string', 'notnull' => 1),
        'article_id' => array('type' => 'integer', 'notnull' => 1)
    );
    protected $table_name = 'tag';



    /**
     * Начало запроса к БД
     * @result string - строку с началом запроса
     */
    public function getWithCounters()
    {
        $query = 'SELECT tag.*, count(tag.name) AS tag_count FROM tag';
        $tags = $this->engine->db->query($query);
        return $tags;
    }

    public function __construct()
    {
    }
}
