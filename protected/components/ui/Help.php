<?php
/**
 * Class Help
 * @author rizky
 */
class Help extends CComponent {
        /**
         * @param string $menu
         * @param string $type
	 * @return string Fungsi ini digunakan untuk membaca data help.
	*/
    public static function get($menu, $type) {
        return file_get_contents(Yii::app()->controller->module->basePath .'/help/'. $menu .'/'. $type.'.html');
    }
    
}