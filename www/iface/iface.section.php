<?php
/**
 * Interface Section
 * Интерфейс для работы с разделами
 * @author Alexey iP Subbota
 * @version 1.0
 */
class iface_section extends iface_base_entity
{
    public $engine = NULL;

    protected $order_fields = array('id', 'name');
    protected $get_fields = array(
        'id'     => array('type' => 'integer', 'many' => 1, 'check_single' => 1, 'notnull' => 1),
        'url'    => array('type' => 'string',  'check_single' => 1, 'notnull' => 1),
        'hidden' => array('type' => 'integer')
    );

    protected $save_fields = array(
        'name'   => array('type' => 'string', 'notnull' => 1),
        'url'    => array('type' => 'string', 'notnull' => 1),
        'hidden' => array('type' => 'integer', 'notnull' => 0)
    );
    protected $table_name = 'section';


    public function __construct()
    {
    }
}
