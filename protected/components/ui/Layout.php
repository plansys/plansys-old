<?php

class Layout extends CComponent {

    public static function render($layout, $data = null, $model = null, $return = false) {
        $formpath = @$data['editor'] ? '//layouts/forms_editor/' : "//layouts/forms/";

        if (count($data) > 0) {
            foreach ($data as $k => $section) {
                switch (@$section['type']) {
                    case "menu":
                        if (@$section['file'] == "") {
                            continue;
                        }
                        $options = array(
                            'title' => @$section['title'],
                            'onclick' => @$section['onclick']
                        );
                        $mt = MenuTree::load($section['file'], $options);
                        if ($mt != null) {
                            $data[$k]['content'] = $mt->render(false);
                        }
                        break;
                    case "form":
                        if (@$section['class'] == "") {
                            continue;
                        }
                        
                        $fb = FormBuilder::load(@$section['class']);
                        if ($fb != null) {
                            $data[$k]['content'] = $fb->render($model, array(
                                'renderInAjax' => true
                            ));
                        }
                        break;
                }
            }
        }

        return Yii::app()->controller->renderPartial($formpath . $layout, $data, $return);
    }

    public static function defaultSection($layout) {
        $section = array(
            'full-width' => 'col1',
            '2-cols' => 'col1',
            '3-cols' => 'col1',
            '2-rows' => 'row1'
        );

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
