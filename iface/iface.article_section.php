<?php
/**
 * Interface Article Section
 * Интерфейс для связки статей и разделов
 * @author Alexey iP Subbota
 * @version 1.0
 */
class iface_article_section extends iface_base_entity
{
    public $engine = NULL;

    protected $get_fields = array(
        'article_id' => array('type' => 'integer', 'notnull' => 1),
        'section_id' => array('type' => 'integer', 'notnull' => 1)
    );
    protected $save_fields = array(
        'article_id'  => array('type' => 'integer', 'notnull' => 1),
        'section_id'  => array('type' => 'integer', 'notnull' => 1)
    );
    protected $table_name = 'article_section';

    public function __construct()
    {
    }
}
