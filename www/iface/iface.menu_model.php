<?php
/**
 * Interface Menu Model
 * Интерфейс для генерации менюшки моделей смартов
 * @author Alexey iP Subbota
 * @version 1.0
 */
class iface_menu_model
{
    public $engine = NULL;

    private $default = array(
        '450' => array(
            'name' => 'ForTwo 450',
            'show' => 0
        ),
        '451' => array(
            'name' => 'ForTwo 451',
            'show' => 0
        ),
        '452' => array(
            'name' => 'Roadster',
            'show' => 0
        ),
        '454' => array(
            'name' => 'ForFour',
            'show' => 0
        )
    );

    public function getMenu()
    {
        $result = $this->default;

        if ($this->engine->auth->user) {
            foreach ($result as $model=>&$menu) {
                $menu['show'] = $this->engine->auth->user['info' . $model];
            }
        } else if (isset($_COOKIE['menumodels'])) {
            $result = json_decode($_COOKIE['menumodels'], true);
        }

        return $result;
    }

    public function saveMenu($modelsStatus = false)
    {
        $result = $this->getMenu();

        $saveparam = array();
        foreach ($result AS $model=>&$menu) {
            $show = ( isset($modelsStatus['model']) && in_array($model, $modelsStatus['model']) ) ? 1 : 0;
            $menu['show'] = $show;
            $saveparam['info' . $model] = $show;
        }
        if ($this->engine->auth->user) {
            $whereparam = array('id' => $this->engine->auth->user['id']);
            $this->engine->user->save($saveparam, $whereparam);
        } else {
            setcookie('menumodels', json_encode($result), time() + 30240000, '/', $this->engine->config['sitedomain']);
        }

        return $result;
    }

    public function __construct()
    {
    }
}