<?php

/**
 * Class Layout
 * @author rizky
 */
class Layout extends CComponent {

    /**
     * render
     * Fungsi ini untuk me-render layout
     * @param array $layout
     * @param array $sections
     * @param array $model
     * @param boolean $return
     * @return mixed me-return sebuah layout
     */
    public static function render($layout, $sections = null, $model = null, $return = false) {
        $formpath = @$sections['editor'] ? '//layouts/forms_editor/' : "//layouts/forms/";
        if (count($sections) > 0) {
            foreach ($sections as $k => $section) {
                switch (@$section['type']) {
                    case "menu":
                        if (@$section['file'] == "") {
                            continue;
                        }
                        $options = [
                            'title'    => @$section['title'],
                            'icon'     => @$section['icon'],
                            'sections' => $sections,
                            'options'  => @$section['menuOptions'],
                            'inlineJS' => @$section['inlineJS']
                        ];

                        $mt = MenuTree::load($section['file'], $options);
                        if ($mt != null) {
                            $sections[$k]['content'] = $mt->render(false);
                        }
                        break;
                    case "form":
                        if (@$section['class'] == "") {
                            continue;
                        }

                        $fb = FormBuilder::load(@$section['class']);
                        if ($fb != null) {
                            $sections[$k]['content'] = $fb->render($model, [
                                'renderInAjax' => true
                            ]);
                        }
                        break;
                }
            }
        }
        
        return Yii::app()->controller->renderPartial($formpath . $layout, $sections, $return);
    }

    public static function listLayout() {
        return [
            'full-width' => 'full-width',
            'dashboard'  => 'dashboard',
            '2-cols'     => '2-cols'
        ];
    }

    public static function defaultSection($layout) {
        $section = [
            'dashboard'  => 'col1',
            'full-width' => 'col1',
            '2-cols'     => 'col1',
            '3-cols'     => 'col1',
            '2-rows'     => 'row1'
        ];

        return @$section[$layout];
    }

    public static function getMainFormSection($data) {
        foreach ($data as $k => $d) {
            if (@$d['type'] == "mainform") {
                return $k;
            }
        }
    }

}
