<?php

/**
 * Class Portlet
 * @author rizky
 */
class Portlet extends FormField {

    public function includeJS() {
        return [];
    }

    /** @var string $toolbarName */
    public static $toolbarName = "Portlet";

    /** @var string $category */
    public static $category = "Layout";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-square-o";

    /**
     * @return array me-return array property ActionBar.
     */
    public function getFieldProperties() {
        return [
        ];
    }

}
