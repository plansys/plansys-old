<?php

class Widget extends CComponent {

    public $icon = "";
    public $badge = "";
    
    private static $activeWidgets = array();

    public static function listActiveWidget() {
        if (count(Widget::$activeWidgets) == 0) {
            $path = "application.components.ui.Widgets";
            $dir = Yii::getPathOfAlias($path);
            
            $items = glob($dir . DIRECTORY_SEPARATOR . "*.php");
            foreach ($items as $k => $m) {
                $m = str_replace($dir . DIRECTORY_SEPARATOR, "", $m);
                $m = str_replace('.php', "", $m);
                $widget = new $m;

                $items[$k] = array(
                    'class' => $m,
                    'widget' => $widget,
                    'class_path' => $path . '.' . $m,
                );
            }
            Widget::$activeWidgets = $items;
        }
        return Widget::$activeWidgets;
    }
    
    public static function registerAllScript() {
        foreach (Widget::listActiveWidget() as $w) {
            $w['widget']->registerScript();
        }
    }
    
    /**
     * @return null Fungsi ini akan melakukan register script sebanyak array java script yang di-include.
     */
    public function registerScript() {
        $includeJS = $this->includeJS();
        if (count($includeJS) > 0) {
            foreach ($includeJS as $js) {
                $class = get_class($this);
                Yii::app()->clientScript->registerScriptFile(
                    Yii::app()->assetManager->publish(
                        Yii::getPathOfAlias("application.components.ui.Widgets.{$class}") . '/' . $js
                    ), CClientScript::POS_END
                );
            }
        }
    }

    /**
     * @return array Fungsi ini akan melakukan render script dan me-return array $html.
     */
    public function renderScript() {
        $includeJS = $this->includeJS();
        $html = array();
        if (count($includeJS) > 0) {
            foreach ($includeJS as $js) {
                $class = get_class($this);
                $html[] = Yii::app()->assetManager->publish(
                    Yii::getPathOfAlias("application.components.ui.Widgets.{$class}") . '/' . $js, true
                );
            }
        }
        return $html;
    }

}
