<?php

class FormsController extends Controller {

    public $countRenderID = 1;
    public static $modelField = array();
    public static $modelFieldList = array(); // list of all fields in current model

    public static function setModelFieldList($data, $type = "AR") {
        if (count(FormsController::$modelFieldList) == 0) {
            if ($type == "AR") {
                foreach ($data as $name => $field) {
                    if (is_array($field) && isset($field['name'])) {
                        FormsController::$modelFieldList[$field['name']] = $field['name'];
                    }
                }
            } else {
                foreach ($data as $name => $field) {
                    FormsController::$modelFieldList[$name] = $name;
                }
                unset(FormsController::$modelFieldList['type']);
            }
        }
    }

    public function renderPropertiesForm($field) {
        FormField::$inEditor = true;
        $fbp = FormBuilder::load($field['type']);
        return $fbp->render($field, array(
                'wrapForm' => false,
                'FormFieldRenderID' => $this->countRenderID++
        ));
    }

    public function actionRenderProperties($class = null) {
        if ($class == null) {
            return true;
        }

        $a = new $class;
        $field = $a->attributes;
        $field['name'] = $class::$toolbarName;
        if (isset($array['label'])) {
            $field['label'] = $class::$toolbarName;
        }
        echo $this->renderPropertiesForm($field);
    }

    public function actionRenderTemplate($class = null) {
        if ($class == null) {
            return true;
        }

        echo $class::template();
    }

    public function actionRenderBuilder($class, $layout) {

        $fb = FormBuilder::load($class);
        $builder = $this->renderPartial('form_builder', array(), true);
        $mainFormSection = Layout::getMainFormSection($fb->form['layout']['data']);

        $data = $fb->form['layout']['data'];
        if ($layout != $fb->form['layout']['name']) {
            unset($data[$mainFormSection]);
            $mainFormSection = Layout::defaultSection($layout);
        }

        $data['editor'] = true;
        $data[$mainFormSection]['content'] = $builder;

        Layout::render($fb->form['layout']['name'], $data);
    }

    public function actionRenderHiddenField() {
        $this->renderPartial('form_fields_hidden');
    }

    public function renderAllToolbar($formType) {
        FormField::$inEditor = false;

        $toolbarData = Yii::app()->cache->get('toolbarData');
        if (!$toolbarData) {
            $toolbarData = FormField::allSorted();
            Yii::app()->cache->set('toolbarData', $toolbarData, 0);
        }

        foreach ($toolbarData as $k => $f) {
            $ff = new $f['type'];
            $scripts = $ff->renderScript();
            foreach ($scripts as $script) {
                Yii::app()->clientScript->registerScriptFile($script, CClientScript::POS_END);
            }
        }

        FormField::$inEditor = true;

        return array(
            'data' => $toolbarData
        );
    }

    public function actionIndex() {
        $forms = FormBuilder::listFile('forms.*', function($m) {
                return array(
                    'name' => str_replace(ucfirst($this->module->id), '', $m),
                    'class' => $m
                );
            });

        $this->render('index', array(
            'forms' => $forms
        ));
    }

    public function actionEmpty() {
        $this->layout = "//layouts/blank";
        $this->render('empty');
    }

    public function actionSave($class) {
        FormField::$inEditor = true;

        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        $fb = FormBuilder::load($class);

        if (isset($post['fields'])) {
            if (is_subclass_of($fb->model, 'FormField')) {
                Yii::app()->cache->delete('toolbarData');
                Yii::app()->cache->delete('toolbarHtml');
            }

            //save posted fields
            $fb->fields = $post['fields'];
        }
        if (isset($post['form'])) {

            //save posted form
            $fb->form = $post['form'];
        }
    }

    public function actionCreateAct($class) {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        $fb = FormBuilder::load($class);
        Yii::import('application.modules.' . $fb->module . '.controllers.*');
        $fb->generateCreateAction();
    }

    public function actionUpdateAct($class) {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        $fb = FormBuilder::load($class);
        Yii::import('application.modules.' . $fb->module . '.controllers.*');
        $fb->generateUpdateAction();
    }

    public function actionUpdate($class) {
        FormField::$inEditor = true;

        $this->layout = "//layouts/blank";
        $fb = FormBuilder::load($class);

        if (is_subclass_of($fb->model, 'ActiveRecord')) {
            $formType = "ActiveRecord";
            FormsController::setModelFieldList($class::model()->defaultFields, "AR");
        } else if (is_subclass_of($fb->model, 'FormField')) {
            $formType = "FormField";
            $mf = new $class;
            FormsController::setModelFieldList($mf->attributes, "FF");
        } else if (is_subclass_of($fb->model, 'Form')) {
            $formType = "Form";
            $mf = new $class;
            FormsController::setModelFieldList($mf->attributes, "FF");
        }

        $fieldData = $fb->fields;
        FormsController::$modelField = $fieldData;

        $toolbar = $this->renderAllToolbar($formType);

        Yii::import('application.modules.' . $fb->module . '.controllers.*');

        $this->render('form', array(
            'fb' => $fb,
            'class' => $class,
            'formType' => $formType,
            'toolbarData' => @$toolbar['data'],
            'fieldData' => $fieldData,
        ));
    }

}
