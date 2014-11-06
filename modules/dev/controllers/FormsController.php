<?php

class FormsController extends Controller {

    public $countRenderID = 1;
    public static $modelField = array();
    public static $modelFieldList = array(); // list of all fields in current model
    public static $relFieldList = array();

    public static function setModelFieldList($data, $type = "AR", $class = "") {
        if (count(FormsController::$modelFieldList) == 0) {
            if ($type == "AR") {
                FormsController::$modelFieldList = $data;

                $rel = isset($data['Relations']) ? $data['Relations'] : array();
                FormsController::$relFieldList = array_merge(array(
                    '' => '-- None --',
                    '---' => '---',
                    'currentModel' => 'Current Model',
                    '--' => '---',
                    ), $rel);
            } else {
                foreach ($data as $name => $field) {
                    FormsController::$modelFieldList[$name] = $name;
                }
                unset(FormsController::$modelFieldList['type']);
            }
        }
    }

    public function actionNew() {
        $this->renderForm("");
    }

    public function renderPropertiesForm($field) {
        FormField::$inEditor = false;
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
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        if (isset($post['form'])) {
            $form = $post['form'];
        } else {
            $fb = FormBuilder::load($class);
            $form = $fb->form;
        }

        $builder = $this->renderPartial('form_builder', array(), true);
        $mainFormSection = Layout::getMainFormSection($form['layout']['data']);
        $data = $form['layout']['data'];
        if ($layout != $form['layout']['name']) {
            unset($data[$mainFormSection]);
            $mainFormSection = Layout::defaultSection($layout);
        }

        $data['editor'] = true;
        $data[$mainFormSection]['content'] = $builder;
        Layout::render($layout, $data);
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
                $ext = array_pop(explode(".", $script));
                if ($ext == "js") {
                    Yii::app()->clientScript->registerScriptFile($script, CClientScript::POS_END);
                } else {
                    Yii::app()->clientScript->registerCSSFile($script, CClientScript::POS_BEGIN);
                }
            }
        }

        FormField::$inEditor = true;

        return array(
            'data' => $toolbarData
        );
    }

    public function actionFormList() {
        echo json_encode(FormBuilder::listFile());
    }

    public function actionIndex() {
        $this->render('index', array(
            'forms' => array()
        ));
    }

    public function actionEmpty() {
        $this->layout = "//layouts/blank";
        $this->render('empty');
    }

    public function actionSave($class) {
        FormField::$inEditor = true;

        $class = FormBuilder::classPath($class);
        $session = Yii::app()->session['FormBuilder_' . $class];
        $file = file(Yii::getPathOfAlias($class) . ".php", FILE_IGNORE_NEW_LINES);

        $changed = false;
        foreach ($file as $k => $f) {
            if (trim($file[$k]) != trim(@$session['file'][$k])) {
                $changed = true;
            }
        }

        if (!$changed) {
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
        } else {
            echo "FAILED";
        }
    }

    public function actionUpdate($class) {
        FormField::$inEditor = true;
        $class = FormBuilder::classPath($class);
        Yii::app()->session['FormBuilder_' . $class] = null;

        $this->layout = "//layouts/blank";
        $fb = FormBuilder::load($class);
        $classPath = $class;
        $class = array_pop(explode(".", $class));
        
        
        if (is_subclass_of($fb->model, 'ActiveRecord')) {
            $formType = "ActiveRecord";
            FormsController::setModelFieldList($class::model()->attributesList, "AR", $class);
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
            'classPath' => $classPath,
            'formType' => $formType,
            'toolbarData' => @$toolbar['data'],
            'fieldData' => $fieldData,
        ));
    }

}
