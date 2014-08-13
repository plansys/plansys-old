<?php

class SectionField extends FormField {

    public function getFieldProperties() {
        return array(
        );
    }

    public $content = array('<column-placeholder></column-placeholder>');
    public $renderContent = "";
    public $parseField = array(
        'content' => 'renderContent',
    );
    public static $toolbarName = "Section";
    public static $category = "Layout";
    public static $toolbarIcon = "fa fa-archive";

    public function renderColumn($i) {
        $html = $this->content;
        if (trim($html == "<column-placeholder></column-placeholder>")) {
            $html = "&nbsp;";
        }

        return $html;
    }

    public function render() {
        return $this->renderInternal('template_render.php');
    }

}
