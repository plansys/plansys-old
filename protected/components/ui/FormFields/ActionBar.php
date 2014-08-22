<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Class ActionBar
 * @author rizky
 */
class ActionBar extends FormField {
	/**
	 * @return array Fungsi ini akan me-return array property ActionBar.
	 */
    public function getFieldProperties() {
        return array();
    }
	
    /** @var string $toolbarName */
    public static $toolbarName = "Action Bar";
	
    /** @var string $category */
	public static $category = "Layout";
	
	/** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-suitcase";

}
