<?php
/**
 * Interface Template
 * Интерфейс, отвечающий за шаблонизатор
 * @author Alexey iP Subbota
 * @version 1.0
 */

class iface_tpl
{
    private $tplfile = array();
    private $tplvar = array();
    private $fi_len = 0;


    /**
     * добавляем шаблон в список шаблонов
     * @param string tpl_name - путь к шаблону
     */
    public function loadTpl($tpl_name, $for_render = true, $var_keyname = false, $var_container = false)
    {
        $tpl_cachepath = $this->engine->config['sitepath'] . 'cache/tpls/' . str_replace('/', '=', $tpl_name) . '.php';
        //if (!file_exists($tpl_cachepath)) {
            $this->parseTpl($tpl_name, $tpl_cachepath, $var_keyname, $var_container);
        //}
        if ( ($for_render === true) && (!isset($this->tplfile[$tpl_name])) ) {
            $this->tplfile[$tpl_name] = $tpl_cachepath;
        }

        return $tpl_cachepath;
    }


    /**
     * добавляем значение переменных в шаблоне
     * @param string var_name - название переменной в шаблоне
     * @param string var_value - значение, которое должна принять переменная
     */
    public function addVar($var_name, $var_value)
    {
        if (!isset($this->tplvar[$var_name])) {
            if (is_array($var_value)) {
                $this->tplvar[$var_name] = &$var_value;
            } else {
                $this->tplvar[$var_name] = $var_value;
            }
        }
    }


    /**
     * парсилка шаблона
     * @param string content - строка, которую нужно обработать
     * @param string var_keyname - название ключа, если обрабатываем массив
     * @param string var_container - название элемента массива, который обрабатываем
     */
    private function parseContent($content, $var_keyname = false, $var_container = false)
    {
        $result = '';
        $var_container = ($var_container !== false) ? $var_container : 'this->tplvar';

        while (preg_match('~{([\^a-zA-Z_+\d\/]*)\:((\(([\^a-zA-Z_\d]*)(\:|)(=|>|<|\!)([\^a-zA-Z_\d]*)(\:|)\))|)}~us', $content, $action)) {
            $var_name = $action[1];
            $pos_end = mb_strpos($content, $action[0]);
            $result .= "echo '" . mb_substr($content, 0, $pos_end) . "';\n";

            $pos_end += mb_strlen($action[0]);
            $content = mb_substr($content, $pos_end);

            // вывод ключа
            if ($var_name == '_key') {
                $result .= "echo \$" . $var_keyname . ";\n";

            // вывод значения
            } else if ($var_name == '_value') {
                $result .= "if (!is_array(\$" . $var_container . ")) { echo \$" . $var_container . ";}\n";

            // обработка условия
            } else if ($var_name == 'if') {
                // сначала узнаем, где находится закрывающий :fi нашего условия
                if (!preg_match_all('~({if:[^}]*})|({:fi})~su', $content, $if_match, PREG_OFFSET_CAPTURE)) {
                    continue;
                }
                $if_count = 1;
                foreach ($if_match[2] AS $if_info) {
                    if ($if_info != '') {
                        $if_count--;
                    } else {
                        $if_count++;
                    }
                    if ($if_count == 0) {
                        $pos_fi = $if_info[1] + $this->fi_len; // поправка на случай,контента с русскими буквами
                        break;
                    }
                }
                // теперь вырежем контент условия
                $if_content = mb_substr($content, 0, $pos_fi);
                $pos_end =  mb_strrpos($if_content, '{:fi}');
                $if_content = mb_substr( $if_content, 0, $pos_end);

                // продвинем курсор дальше
                $pos_end = $pos_end + $this->fi_len;
                $content = mb_substr($content, $pos_end);

                // в операциях заменим равенства и неравенства на ПХПшные
                $action[6] = str_replace(array('=', '!'), array('==', '!='), $action[6]);
                $isset_action = array();
                for ($i=0; $i<2; $i++) {
                    $var_index = 4 + $i * 3;
                    // узнаем, что за переменные в условиях
                    if ($action[$var_index + 1] == ':') {
                        if ( $action[$var_index] == '_key') {
                            $action[$var_index] = "\$" . $var_keyname;
                        } else if ($action[$var_index] == '_value') {
                            $action[$var_index] = "\$" . $var_container;
                        } else if (mb_substr($action[$var_index], 0, 1) == '^') {
                            $action[$var_index] = "\$this->tplvar['" . str_replace('^', '', $action[$var_index]) . "']";
                        } else {
                            $action[$var_index] = "\$" . $var_container . "['" . $action[$var_index] . "']";
                        }
                        $isset_action[] = "isset(" . $action[$var_index] . ")";
                    } else {
                        $action[$var_index] = "'" . $action[$var_index] . "'";
                    }
                }
                if (count($isset_action) > 0) {
                    $result .= "if (" . implode(" && ", $isset_action) . " && (" . $action[4] . $action[6] . $action[7] .")) {";
                } else {
                    $result .= "if (" . $action[4] . $action[6] . $action[7] .") {";
                }
                $result .= $this->parseContent($if_content, $var_keyname, $var_container);
                $result .= "}";

            // обработаем включение шаблона
            } else if (mb_substr($var_name, 0, 1) == '+') {
                $var_name = str_replace('+', '', $var_name);
                $result .= "include '" . $this->loadTpl($var_name, false, $var_keyname, $var_container) . "';";

            // обработаем переменную
            } else {
                // сначала проверим не массив ли это
                $pos_array = mb_strpos($content, '{:' . $var_name . '}');
                if ($pos_array > 0) {
                    $array_content = mb_substr($content, 0, $pos_array);
                    $pos_end = $pos_array + mb_strlen('{:' . $var_name . '}');
                    $content = mb_substr($content, $pos_end);
                    $result .= "if ( isset(\$" . $var_container . "['" . $var_name . "']) && is_array(\$" . $var_container . "['" . $var_name . "']) ) {\n";
                    $result .= "foreach (\$" . $var_container . "['" . $var_name . "'] AS \$" . $var_name . "_key=>\$" . $var_name . "_value) {\n";
                    $result .= $this->parseContent($array_content, $var_name . '_key', $var_name . '_value');
                    $result .= "}\n";
                    $result .= "}\n";

                // если не массив, то просто выведем значение переменной
                } else {
                    if (mb_substr($var_name, 0, 1) == '^') {
                        $var_name = str_replace('^', '', $var_name);
                        $result .= "if ( isset(\$this->tplvar['" . $var_name . "'])) {\n";
                        $result .= "echo \$this->tplvar['" . $var_name . "'];\n";
                        $result .= "}\n";
                    } else {
                        $result .= "if ( isset(\$" . $var_container . "['" . $var_name . "']) ) {\n";
                        $result .= "echo \$" . $var_container . "['" . $var_name . "'];\n";
                        $result .= "}\n";
                    }
                }

            }
        }
        $result .= "echo '" . $content . "';\n";

        return $result;
    }


    /**
     * парсилка шаблона
     * @param string tpl_name - имя шаблона, который надо обработать
     * @param string tpl_fullpath - полный путь до места, куда сохранить обработанный шаблон
     */
    private function parseTpl($tpl_name, $tpl_fullpath, $var_keyname = false, $var_container = false)
    {
        $tpl_content = file_get_contents($this->engine->config['sitepath'] . 'tpls/' . $tpl_name . '.tpl');
        $tpl_content = str_replace("'", '&apos;', $tpl_content);
        $save_content = "<?php\n";
        $save_content .= $this->parseContent($tpl_content, $var_keyname, $var_container);
        file_put_contents($tpl_fullpath, $save_content);
    }


    /**
     * Рендеринг шаблонов
     */
    public function render()
    {
        // проходим все шаблоны и посылаем их в парсилку
        foreach ($this->tplfile as $path=>&$full_path) {
            include $full_path;
        }
    }


    public function __construct()
    {
        $this->fi_len = mb_strlen('{:fi}');
    }
}