<?php
/**
 * Class Help
 * @author rizky
 */
class Help extends CComponent {
    /**
     * get
     * Fungsi ini digunakan untuk membaca data dan me-returnnya
     * @param string $menu
     * @param string $type
     * @return string me-return string data yang dibaca
    */
    public static function get($menu, $type) {
        return file_get_contents(Yii::app()->controller->module->basePath .'/help/'. $menu .'/'. $type.'.html');
    }
    
}