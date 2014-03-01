<?php
/**
 * Interface String
 * Интерфейс с разными функционалом для работы со строками
 * @author Alexey iP Subbota
 * @version 1.0
 */
class iface_string
{
    public $engine = NULL;

    public function removeTags($content = false, $keep = array())
    {
        if ($content == false) {
            return $content;
        }

        $single_tag = array('img', 'input', 'br');

        while (preg_match('~<(\/|)([a-zA-Z]*)(|[^>]*)>~us', $content, $tag) != false) {
            $replaced = array(
                'start' => (in_array($tag[2], $keep)) ? '[==' . $tag[1] . $tag[2] . $tag[3] . '==]' : ''
            );
            if (in_array($tag[2], $single_tag)) {
                $content = str_replace($tag[0], $replaced['start'], $content);
            } else {
                $end_tag = '</' . $tag[2] . '>';
                list($before, $after) = explode($tag[0], $content, 2);
                // если это закрывающий тэг - потеряшка или у тэга нету закрывающего, то уберём этот тэг
                if ( ($tag[1] == '/') || (mb_strpos($after, $end_tag) === false) ) {
                    $content = $before . $after;
                    continue;
                }
                $replaced['end'] = '[==/' . $tag[2] . '==]';
                list($inner, $after) = explode($end_tag, $after, 2);
                if (!in_array($tag[2], $keep)) {
                    $replaced['end'] = '';
                    $inner = '';
                }
                $content = $before . $replaced['start'] . $inner . $replaced['end'] . $after;
            }
        }
        $content = str_replace(array('[==', '==]'), array('<', '>'), $content);

        return $content;
    }

    public function __construct()
    {
    }
}
