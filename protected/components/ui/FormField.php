<?php

class FormField extends CComponent {

    private $_errors = array();
    private $_form_properties = array(
        'title' => '',
        'layout' => array(
            'name' => 'full-width',
            'data' => array(
                'col1' => array(
                    'type' => 'mainform'
                )
            )
        ),
    );
    private $_builder = null;
    public $parseField = array(); // list of form fields to be parsed array('from'=>'to')
    public $renderID = ""; //to distinguish one field to another, will be filled when rendering, -NOT- in editor
    public $isHidden = false;
    public static $toolbarName;
    public static $category;
    public static $toolbarIcon;
    public static $inEditor = false;
    public static $categorySettings = array(
        'User Interface' => array(
            'icon' => 'fa-cubes',
        ),
        'Layout' => array(
            'icon' => 'fa-image',
        ),
        'Developer Fields' => array(
            'icon' => 'fa-warning',
        ),
    );

    public function includeJS() {
        return array();
    }

    public function getType() {
        return get_class($this);
    }

    ## builder

    public function getBuilder() {
        return $this->_builder;
    }

    public function setBuilder($builder) {
        $this->_builder = $builder;
    }

    ## model

    public function getModel() {

        if (!is_null($this->_builder) && get_class($this->_builder) == "FormBuilder") {
            return $this->_builder->model;
        }
        return array();
    }

    ## parent form properties

    public function setFormProperties($value) {
        $this->_form_properties = $value;
    }

    public function getFormProperties() {
        return $this->_form_properties;
    }

    ## errors

    public function getErrors() {
        return $this->_errors;
    }

    public function setErrors($error) {
        return $this->_errors = $error;
    }

    public function evaluate($expr, $return = false) {
        $error_level = error_reporting();

//        error_reporting(0);
        $result = $this->evaluateExpression($expr, array(
            'model' => $this->builder->model,
        ));
        error_reporting($error_level);

        if ($return) {
            return $result;
        } else {
            echo(!$result ? "--invalid--" : json_encode($result));
        }
    }

    ## field properties - editor form field

    public function getForm() {
        return $this->_form_properties;
    }

    public static function template() {
        return self::renderTemplate('template_editor.php');
    }

    public function render() {
        return $this->renderInternal('template_render.php');
    }

    public function setAttributes($values) {
        foreach ($values as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }

    public static function attributes() {
        $field = new static();
        return $field->attributes;
    }

    public function getAttributes() {
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        $result = array();
        foreach ($props as $k => $p) {
            if (!$p->isStatic()) {
                $name = $p->getName();
                $result[$name] = $this->$name;
            }
        }
        $result['type'] = get_class($this);
        return $result;
    }

    public function setDefaultOption($key, $value, &$option) {

        if (!isset($option[$key])) {
            $option[$key] = $value;
        }
    }

    public function getOriginalName() {
        if (!property_exists($this, 'name')) {
            return "";
        }
        if (strpos($this->name, "[") !== false) {
            $name = explode("[", $this->name);
            $name = str_replace(array(" ", "]"), "", $name[count($name) - 1]);
            return $name;
        } else {
            return $this->name;
        }
    }

    public function getDefaultFields() {
        $fields = $this->attributes;
        $exclude = array(
            'data',
            'id'
        );
        foreach ($fields as $k => $f) {
            if (in_array($k, $exclude)) {
                unset($fields[$k]);
                continue;
            }

            $fields[] = array(
                'name' => $k,
                'type' => 'Text'
            );
        }
        return $fields;
    }

    public static function renderTemplate($file) {
        $reflector = new ReflectionClass(get_called_class());
        $path = str_replace(".php", DIRECTORY_SEPARATOR . $file, $reflector->getFileName());
        return file_get_contents($path);
    }

    public function registerScript() {
        $includeJS = $this->includeJS();
        if (count($includeJS) > 0) {
            foreach ($includeJS as $js) {
                $class = get_class($this);
                Yii::app()->clientScript->registerScriptFile(
                    Yii::app()->assetManager->publish(
                        Yii::getPathOfAlias("application.components.ui.FormFields.{$class}") . '/' . $js
                    ), CClientScript::POS_END
                );
            }
        }
    }

    public function renderScript() {
        $includeJS = $this->includeJS();
        $html = array();
        if (count($includeJS) > 0) {
            foreach ($includeJS as $js) {
                $class = get_class($this);
                $html[] = Yii::app()->assetManager->publish(
                    Yii::getPathOfAlias("application.components.ui.FormFields.{$class}") . '/' . $js, true
                );
            }
        }
        return $html;
    }

    public function renderInternal($file) {

        $reflector = new ReflectionClass($this);
        $path = str_replace(".php", DIRECTORY_SEPARATOR . $file, $reflector->getFileName());

        $attributes = array(
            'field' => $this->attributes,
            'form' => $this->_form_properties,
            'errors' => $this->_errors,
            'model' => $this->model
        );

        extract($attributes);

        $this->registerScript();

        ob_start();
        include($path);
        return Helper::minifyHtml(ob_get_clean());
    }

    public function addClass($class, $fieldName = "options") {
        $opt = $this->$fieldName;
        $array = explode(" ", @$opt['class']);
        $array = array_unique(array_filter($array));
        array_push($array, $class);
        $opt['class'] = implode(" ", $array);
        $this->$fieldName = $opt;
    }

    
    public function expandAttributes($attributes) {
        return Helper::expandAttributes($attributes);
    }

    public static function all() {
        $ffdir = Yii::getPathOfAlias('application.components.ui.FormFields') . DIRECTORY_SEPARATOR;
        $dir = glob($ffdir . "*.php");
        return array_map(function ($d) use ($ffdir) {
            $class = str_replace($ffdir, "", $d);
            $class = str_replace(".php", "", $class);

            $a = new $class;
            $array = $a->attributes;
            $array['name'] = $class::$toolbarName;
            if (isset($array['label'])) {
                $array['label'] = $class::$toolbarName;
            }
            return $array;
        }, $dir);
    }

    public static function allSorted() {

        $all = FormField::all();
        usort($all, function ($a, $b) {
            return strlen($a['type']::$category) - strlen($b['type']::$category);
        });

        return $all;
    }

    public static function settings($formType) {
        $ffdir = Yii::getPathOfAlias('application.components.ui.FormFields') . DIRECTORY_SEPARATOR;
        $dir = glob($ffdir . "*.php");
        $result = array(
            'icon' => array(),
            'category' => array()
        );
        foreach ($dir as $d) {
            $class = str_replace($ffdir, "", $d);
            $class = str_replace(".php", "", $class);

            if (($formType == 'ActiveRecord' || $formType == 'Form') &&
                $class::$category == 'Developer Fields'
            )
                continue;

            $result['icon'][$class] = $class::$toolbarIcon;
            $result['category'][$class] = $class::$category;
        }
        return $result;
    }

}
