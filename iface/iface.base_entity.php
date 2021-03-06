<?php
/**
 * Interface Base Entity
 * Базовый интерфейс
 * @author Alexey iP Subbota
 * @version 1.0
 */

class iface_base_entity
{
    public $engine = NULL;

    protected $order_fields = array();
    protected $group_fields = array();
    protected $get_fields   = array();
    protected $save_fields  = array();
    protected $table_name   = '';

    private $operator_list = array('=', '>', '<', '!');

    /**
     * Сохранение сущности
     * @param array param - список параметров для сохранения
     * @param array where - список критериев для сохранения
     * @result integer - id юзера или false в случае провала
     */
    public function save($param = array(), $where = array(), &$error = array())
    {
        $result = false;

        if (count($param) == 0) {
            $error[] = 'empty_params';
            return $result;
        }
        if (count($this->save_fields) == 0) {
            $error[] = 'empty_fields';
            return $result;
        }

        if ( method_exists($this, 'beforeSave') && !$this->beforeSave($param, $where) ) {
            $error[] = 'before_not_done';
            return $result;
        }

        $save_list = array();

        foreach ($this->save_fields AS $field_name=>$field) {
            if (isset($param[$field_name])) {
                switch ($field['type']) {
                    case 'integer' :
                        if ( !ctype_digit($param[$field_name]) && !is_int($param[$field_name]) ) {
                            $error[] = 'not_digit';
                            continue;
                        }
                        $param[$field_name] = intval($param[$field_name]);
                        if ( isset($field['notnull']) && ($param[$field_name] == 0) ) {
                            if ($field['notnull'] == 1) {
                                $error[] = $field_name . ' is_null';
                            } else {
                                $save_list[] = $field_name . '=NULL';
                            }
                            continue;
                        }
                        $save_list[] = $field_name . '=' . $param[$field_name];
                        break;

                    case 'string' :
                        if ( isset($field['pattern']) && (!preg_match('~' . $field['pattern'] . '~su', $param[$field_name])) ) {
                            $error[] = 'not_match_pattern';
                            continue;
                        }
                        if ( (mb_strlen($param[$field_name]) == 0) && isset($field['notnull']) ) {
                            if ($field['notnull'] == 1) {
                                $error[] = $field_name . ' is_null';
                                continue;
                            } else {
                                $save_list[] = $field_name . '=NULL';
                            }
                        } else {
                            $save_list[] = $field_name . '="' . mysql_escape_string($param[$field_name]) . '"';
                        }
                        break;

                    case 'date' :
                        if (!preg_match('~^\d{4}-\d{2}-\d{2}$~su', $param[$field_name])) {
                            $error[] = 'wrong_date';
                            continue;
                        }
                        $save_list[] = $field_name . '="' . mysql_escape_string($param[$field_name]) . '"';
                        break;

                    case 'datetime' :
                        if (!preg_match('~^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$~su', $param[$field_name])) {
                            $error[] = 'wrong_datetime';
                            continue;
                        }
                        $save_list[] = $field_name . '="' . mysql_escape_string($param[$field_name]) . '"';
                        break;
                }
            }
        }
        if (count($save_list) == 0) {
            $error[] = 'empty_save_list';
        }

        if (count($error) > 0) {
            return $result;
        }

        if (isset($this->save_fields['update_date'])) {
            $save_list[] = 'update_date="' . date('Y-m-d H:i:s') . '"';
        }

        if ( is_array($where) && (count($where) > 0) ) {
            if ( isset($where['id']) && ($where['id'] > 0) ) {
                $query = 'UPDATE ' . $this->table_name . ' SET ' . implode(', ', $save_list) . ' WHERE id=' . intval($where['id']);
                $this->engine->db->query($query);
                $result = $where['id'];
            }
        } else {
            if (isset($this->save_fields['create_date'])) {
                $save_list[] = 'create_date="' . date('Y-m-d H:i:s') . '"';
            }
            $query = 'INSERT INTO ' . $this->table_name . ' SET ' . implode(', ', $save_list);
            $result = $this->engine->db->query($query);
        }

        if ( method_exists($this, 'afterSave') && !$this->afterSave($result, $param, $where) ) {
            $error[] = 'after_not_done';
            return $result;
        }

        return $result;
    }


    /**
     * Сборка параметров выборки
     * @param array param - список параметров для выборки
     * @param string result_type - тип выборки (list = список, row = один элемент)
     * @result array - список параметров выборки вида 'поле=значение' или false
     */
    private function &makeWhere($params = array(), &$result_type = 'list', &$join_list = array())
    {
        $result = false;

        if (count($params) == 0) {
            return $result;
        }

        if (count($this->get_fields) == 0) {
            return $result;
        }

        $result = array();
        $join_cache = array();

        foreach ($params AS $field_name=>$param) {
            if (is_array($param)) {
                if ( isset($param['_operator']) xor isset($param['_value']) ) {
                    continue;
                }
                if ( isset($param['_operator']) && isset($param['_value']) ) {
                    $query_operator = $param['_operator'];
                    $param = $param['_value'];
                }
            } else {
                $query_operator = '=';
            }

            if (mb_strtolower($query_operator) == 'or') {
                $result[] = '(' . implode(' OR ', $this->makeWhere($param, $result_type, $join_list)) . ')';
                continue;
            }

            if (!isset($this->get_fields[$field_name])) {
                continue;
            }
            $field = $this->get_fields[$field_name];

            if (isset($field['join'])) {
                if (!in_array($field['join']['table'], $join_cache)) {
                    $join_list[] = ' LEFT JOIN ' . $field['join']['table'] . ' ON ' . $field['join']['table'] . '.' . $field['join']['key_join'] . '=' . $this->table_name . '.' . $field['join']['key_main'];
                    $join_cache[] = $field['join']['table'];
                }
                $full_field_name = $field['join']['table'] . '.' . $field['join']['field'];
            } else {
                $full_field_name = $this->table_name . '.' . $field_name;
            }


            switch ($field['type']) {
                case 'integer' :
                    if ( is_array($param) && (count($param) > 0) && (isset($field['many'])) ) {
                        $valid_values = array();
                        foreach ($param AS $value) {
                            if (isset($field['notnull'])) {
                                if (intval($value) > 0) {
                                    $valid_values[] = intval($value);
                                }
                            } else {
                                $valid_values[] = intval($value);
                            }
                        }
                        if (count($valid_values) > 0) {
                            $query_operator = ($query_operator == '!=') ? 'NOT IN' : 'IN';
                            $query_value = '(' . implode(',', $valid_values) . ')';
                        }
                    } else if ( ctype_digit($param) || is_int($param) ) {
                        $param = intval($param);
                        if ( isset($field['notnull']) && ($param == 0) ) {
                            continue(2);
                        }
                        $query_value = $param;
                        if (isset($field['check_single'])) {
                            $result_type = 'row';
                        }
                    }
                    break;

                case 'string' :
                    if (isset($field['notnull'])) {
                        if (mb_strlen($param) > 0) {
                            $query_value = '"' . mysql_escape_string($param) . '"';
                        }
                    } else {
                        $query_value = '"' . mysql_escape_string($param) . '"';
                    }

                    if (preg_match('~(^\%|\%$)~su', $param, $match) != false) {
                        $query_operator = ($query_operator == '!=') ? 'NOT LIKE' : 'LIKE';
                    } else if (isset($field['check_single'])) {
                        $result_type = 'row';
                    }
                    break;

                case 'date' :
                    if (!preg_match('~^\d{4}-\d{2}-\d{2}$~su', $param)) {
                        continue(2);
                    }
                    $query_value = '"' . mysql_escape_string($param) . '"';
                    break;

                case 'datetime' :
                    if (!preg_match('~^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$~su', $param)) {
                        continue(2);
                    }
                    $query_value = '"' . mysql_escape_string($param) . '"';
                    break;
            }

            $result[] = $full_field_name . ' ' . $query_operator . ' ' . $query_value;
        }

        if (count($result) == 0) {
            $result = false;
        }
        return $result;
    }


    /**
     * Выборка элементов
     * @param array param - список параметров для выборки
     * @param array order - список параметров для сортировки
     * @param integer limit - лимит на выборку
     * @param integer page - номер страницы если надо
     * @result array - список элементов или один элемент
     */
    public function &get($param = array(), $order = array(), $group=array(), $limit = 0, $page = 1)
    {
        $result = false;
        $result_type = 'list';

        if (method_exists($this, 'getQuery')) {
            $query = $this->getQuery();
        } else {
            $query = 'SELECT ' . $this->table_name . '.* FROM ' . $this->table_name;
        }

        // проверим параметры выборки
        if ( is_array($param) && (count($param) > 0) ) {
            $join_list = array();
            $where_list = $this->makeWhere($param, $result_type, $join_list);
            if ($where_list === false) {
                return $result;
            }
            if (count($join_list) > 0) {
                $query .= implode(' ', $join_list);
            }
            $query .= ' WHERE ' . implode(' AND ', $where_list);
        }

        // проверим группировки
        if ( is_array($group) && (count($group) > 0) ) {
            if (count($this->group_fields) == 0) {
                return $result;
            }

            $group_list = array();
            foreach ($group AS $group_key) {
                if (in_array($group_key, $this->group_fields)) {
                    $group_list[] = $this->table_name . '.' . mysql_escape_string($group_key);
                }
            }

            if (count($group_list) == 0) {
                return $result;
            }

            $query .= ' GROUP BY ' . implode(', ', $group_list);
        }

        // проверим сортировки
        if ( is_array($order) && (count($order) > 0) ) {
            if (count($this->order_fields) == 0) {
                return $result;
            }

            $order_list = array();
            foreach ($order AS $key=>$dir) {
                $order_dir = (mb_strtolower($dir) == 'desc') ? 'DESC' : 'ASC';
                if (in_array($key, $this->order_fields)) {
                    $order_list[] = $this->table_name . '.' . mysql_escape_string($key) . ' ' . $order_dir;
                } else if (isset($this->order_fields[$key])) {
                    $order_list[] = ( ($this->order_fields[$key] != '') ? $this->table_name . '.' . mysql_escape_string($key) : mysql_escape_string($key) ) . ' ' . $order_dir;
                }
            }

            if (count($order_list) == 0) {
                return $result;
            }

            $query .= ' ORDER BY ' . implode(', ', $order_list);
        }

        if ($limit > 0) {
            $limit = intval($limit);
            $page = intval($page);
            $query .= ' LIMIT ' . ( $limit * ($page - 1) ) . ', ' . $limit;
            if ($limit == 1) {
                $result_type = 'row';
            }
        }

        $result = $this->engine->db->query($query, $result_type);
        if ( method_exists($this, 'getAfter') && ($result !== false) ) {
            $this->getAfter($result);
        }

        return $result;
    }


    /**
     * Выборка кол-ва элементов
     * @param array param - список параметров для выборки
     * @result integer - кол-во элементов
     */
    public function &getCount($param = array(), $group=array())
    {
        $result = false;

        if (method_exists($this, 'getQuery')) {
            $query = $this->getCountQuery();
        } else {
            $query = 'SELECT COUNT(*) FROM ' . $this->table_name;
        }

        if ( is_array($param) && (count($param) > 0) ) {
            $join_list = array();
            $result_type = 'list';
            $where_list = $this->makeWhere($param, $result_type, $join_list);
            if ($where_list === false) {
                return $result;
            }
            if (count($join_list) > 0) {
                $query .= implode(' ', $join_list);
            }
            $query .= ' WHERE ' . implode(' AND ', $where_list);
        }

        // проверим группировки
        if ( is_array($group) && (count($group) > 0) ) {
            if (count($this->group_fields) == 0) {
                return $result;
            }

            $group_list = array();
            foreach ($group AS $group_key) {
                if (in_array($group_key, $this->group_fields)) {
                    $group_list[] = $this->table_name . '.' . mysql_escape_string($group_key);
                }
            }

            if (count($group_list) == 0) {
                return $result;
            }

            $query .= ' GROUP BY ' . implode(', ', $group_list);
            $query = 'SELECT COUNT(*) AS cnt FROM (' . $query . ') AS count_table';
        }

        $result = $this->engine->db->query($query, 'single');

        return $result;
    }


    /**
     * Удаление элементов
     * @param array param - список параметров для поиска, по которому удалятся будет
     * @result boolean - успех удаления
     */
    public function delete($param = array())
    {
        $query = 'DELETE FROM ' . $this->table_name;

        if ( !is_array($param) && (count($param) == 0) ) {
            return false;
        }

        $where_list = $this->makeWhere($param);
        if ($where_list === false) {
            return false;
        }
        $query .= ' WHERE ' . implode(' AND ', $where_list);
        $this->engine->db->query($query);
        return true;
    }


    public function __construct()
    {
    }
}
