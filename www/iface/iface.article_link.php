<?php
/**
 * Interface Article Link
 * Интерфейс для работы со ссылками статей на статьи
 * @author Alexey iP Subbota
 * @version 1.0
 */
class iface_article_link extends iface_base_entity
{
    public $engine = NULL;

    protected $get_fields = array(
        'article_id' => array('type' => 'integer', 'notnull' => 1),
        'link_id'    => array('type' => 'integer', 'notnull' => 1)
    );
    protected $save_fields = array(
        'article_id'  => array('type' => 'integer', 'notnull' => 1),
        'link_id'     => array('type' => 'integer', 'notnull' => 1)
    );
    protected $table_name = 'article_link';

    public function __construct()
    {
    }
}
