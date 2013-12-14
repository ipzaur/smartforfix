<?php
/**
 * Interface Translit
 * Интерфейс для транслитерации
 * @author Alexey iP Subbota
 * @version 1.0
 */
class iface_translit
{
    public $engine = NULL;

    public function convert($source = false, $langTo = 'eng')
    {
        $rus = array(
            'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж',  'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч',  'ш',  'щ',   'ь', 'ы', 'ъ', 'э', 'ю',  'я',
            'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж',  'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч',  'Ш',  'Щ',   'Ь', 'Ы', 'Ъ', 'Э', 'Ю',  'Я',
        );
        $eng = array(
            'a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', '',  'y', '',  'e', 'yu', 'ya',
            'A', 'B', 'V', 'G', 'D', 'E', 'E', 'ZH', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'CH', 'SH', 'SCH', '',  'Y', '',  'E', 'YU', 'YA'
        );

        if ($langTo == 'rus') {
            $result = str_replace($eng, $rus, $source);
        } else {
            $result = str_replace($rus, $eng, $source);
        }

        return $result;
    }

    public function __construct()
    {
    }
}
