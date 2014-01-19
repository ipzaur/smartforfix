<?php
/**
 * Interface Fav
 * Интерфейс для работы с избранным
 * @author Alexey iP Subbota
 * @version 1.0
 */
class iface_fav extends iface_base_entity
{
    public $engine = NULL;

    protected $order_fields = array('create_date');
    protected $get_fields = array(
        'user_id'    => array('type' => 'integer', 'notnull' => 1),
        'article_id' => array('type' => 'integer', 'notnull' => 1)
    );
    protected $save_fields = array(
        'user_id'     => array('type' => 'integer', 'notnull' => 1),
        'article_id'  => array('type' => 'integer', 'notnull' => 1),
        'create_date' => array('type' => 'datetime')
    );
    protected $table_name = 'fav';

    public function __construct()
    {
    }
}
