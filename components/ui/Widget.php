<?php

class Widget extends CApplicationComponent {

    public $icon = "";
    public $badge = "";
    private static $activeWidgets = [];

    public function render() {
        return $this->renderInternal('template_render.php');
    }

    public function renderInternal($file) {
        $reflector = new ReflectionClass($this);
        $path = str_replace(".php", DIRECTORY_SEPARATOR . $file, $reflector->getFileName());

        $this->registerScript();

        ob_start();
        include($path);
        return Helper::minifyHtml(ob_get_clean());
    }

    public static function listActiveWidget() {
        if (count(Widget::$activeWidgets) == 0) {
            $path = "application.components.ui.Widgets";
            $dir = Yii::getPathOfAlias($path);

            $items = glob($dir . DIRECTORY_SEPARATOR . "*.php");
            $result = [];
            foreach ($items as $k => $m) {
                $m = str_replace($dir . DIRECTORY_SEPARATOR, "", $m);
                $m = str_replace('.php', "", $m);
                $widget = new $m;

                $include = true;
                switch ($m) {
                    case 'NfyWidget':
                        $include = Yii::app()->user->model->subscribed;
                        break;
                }

                if ($include) {
                    $result[$m] = [
                        'class' => $m,
                        'widget' => $widget,
                        'class_path' => $path . '.' . $m,
                    ];
                }
            }
            Widget::$activeWidgets = $result;
        }
        return Widget::$activeWidgets;
    }

    /**
     * @return null Fungsi ini akan melakukan register script sebanyak array java script yang di-include.
     */
    public function registerScript() {
        $includeJS = $this->includeJS();
        if (count($includeJS) > 0) {
            foreach ($includeJS as $js) {
                $class = get_class($this);
                
                $file = Yii::getPathOfAlias("application.components.ui.Widgets.{$class}") . '/' . $js;
                if (!is_file($file)){ 
                    $file = Yii::getPathOfAlias("app.components.ui.Widgets.{$class}") . '/' . $js;
                }
                
                Yii::app()->clientScript->registerScriptFile(
                    Asset::publish($file), CClientScript::POS_END
                );
            }
        }
    }

    /**
     * @return array Fungsi ini akan melakukan render script dan me-return array $html.
     */
    public function renderScript() {
        $includeJS = $this->includeJS();
        $html = [];
        if (count($includeJS) > 0) {
            foreach ($includeJS as $js) {
                $class = get_class($this);
                $html[] = Asset::publish(
                    Yii::getPathOfAlias("application.components.ui.Widgets.{$class}") . '/' . $js, true
                );
            }
        }
        return $html;
    }

}
