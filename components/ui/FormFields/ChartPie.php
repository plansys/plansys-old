<?php

class ChartPie extends FormField {

    
    /** @var string $toolbarName */
    public static $toolbarName = "Pie Chart";
	
    /** @var string $category */
    public static $category = "Chart";
	
    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-pie-chart";
    
    public function getFieldProperties() {
        return array(
        );
    }

}
