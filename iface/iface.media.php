<?php
/**
 * Interface Media
 * Интерфейс для работы с привязанными к статьям фоточками
 * @author Alexey iP Subbota
 * @version 1.0
 */
class iface_media extends iface_base_entity
{
    public $engine = NULL;

    protected $order_fields = array('id', 'create_date');
    protected $get_fields = array(
        'id'      => array('type' => 'integer', 'many' => 1, 'check_single' => 1, 'notnull' => 1),
        'article_id' => array('type' => 'integer', 'notnull' => 1),
        'user_id' => array('type' => 'integer'),
        'hidden' => array('type' => 'integer')
    );
    protected $save_fields = array(
        'article_id' => array('type' => 'integer', 'notnull' => 1),
        'path' => array('type' => 'string', 'notnull' => 1),
        'user_id' => array('type' => 'integer'),
        'hidden' => array('type' => 'integer'),
        'create_date' => array('type' => 'datetime')
    );
    protected $table_name = 'media';

    public function __construct()
    {
    }
}
