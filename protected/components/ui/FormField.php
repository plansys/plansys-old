<?php

/**
 * Class FormField
 * @author rizky
 */
class FormField extends CComponent {

    /**
     * @var array $_errors 
     * @access private	
     */
    private $_errors = array();

    /**
     * @var array $_form_properties 
     * @access private	
     */
    private $_form_properties = array(
        'formTitle' => '',
        'layout' => array(
            'name' => 'full-width',
            'data' => array(
                'col1' => array(
                    'type' => 'mainform'
                )
            )
        ),
    );

    /**
     * @var array $_builder
     * @access private	
     */
    private $_builder = null;

    /** @var array $parseField */
    public $parseField = array(); // list of form fields to be parsed array('from'=>'to')

    /** @var string $renderID */
    public $renderID = ""; //to distinguish one field to another, will be filled when rendering, -NOT- in editor

    /** @var boolean $isHidden */
    public $isHidden = false;

    /** @var string $toolbarName */
    public static $toolbarName;

    /** @var string $category */
    public static $category;

    /** @var string $toolbarIcon */
    public static $toolbarIcon;

    /** @var boolean $inEditor */
    public static $inEditor = false;

    /** @var array $categorySettings */
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

    /**
     * @return array Fungsi ini akan me-return array javascript yang di-include.
     */
    public function includeJS() {
        return array();
    }

    /**
     * @return string Fungsi ini akan me-return string nama class sebuah object.
     */
    public function getType() {
        return get_class($this);
    }

    ## builder

    /**
     * @return array Fungsi ini akan me-return array $_builder.
     */
    public function getBuilder() {
        return $this->_builder;
    }

    /**
     * @param array $builder
     * @return null Fungsi ini akan men-set value dari parameter $builder kedalam array $_builder.  
     */
    public function setBuilder($builder) {
        $this->_builder = $builder;
    }

    ## model

    /**
     * @return array Fungsi ini akan me-return array value model.
     */
    public function getModel() {

        if (!is_null($this->_builder) && get_class($this->_builder) == "FormBuilder") {
            return $this->_builder->model;
        }
        return array();
    }

    ## parent form properties

    /**
     * @param array $value
     * @return null Fungsi ini akan men-set property form field dengan array $value.
     */
    public function setFormProperties($value) {
        $this->_form_properties = $value;
    }

    /**
     * @return array Fungsi ini akan me-return array yang berisi property form.
     */
    public function getFormProperties() {
        return $this->_form_properties;
    }

    ## errors

    /**
     * @return array Fungsi ini akan me-return array $_errors.
     */
    public function getErrors() {
        return $this->_errors;
    }

    /**
     * @param array $error
     * @return array Fungsi ini akan me-return array $_errors yang terlebih dahulu diisi dengan parameter $error.
     */
    public function setErrors($error) {
        return $this->_errors = $error;
    }

    /**
     * @param array $expr
     * @param boolean $return
     * @return array Fungsi ini digunakan untuk evaluate expression dan akan me-return hasil dalam bentuk pesan error.
     */
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

    /**
     * @return array Fungsi ini akan me-return array $_form_properties.
     */
    public function getForm() {
        return $this->_form_properties;
    }

    /**
     * @return array Fungsi ini akan me-render template.
     */
    public static function template() {
        return self::renderTemplate('template_editor.php');
    }

    /**
     * @return field Fungsi ini untuk me-render form field beserta atributnya.
     */
    public function render() {
        return $this->renderInternal('template_render.php');
    }

    /**
     * @param array $values
     * @return null Fungsi ini akan mengisi nilai atribut field dengan nilai dari parameter $values.
     */
    public function setAttributes($values) {
        foreach ($values as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }

    public function getRenderName() {
        if (property_exists($this, 'name')) {
            return get_class($this->model) . "[" . $this->name . "]";
        } else {
            return "";
        }
    }

    /**
     * @return array Fungsi ini akan me-return atribut form.
     */
    public static function attributes() {
        $field = new static();
        return $field->attributes;
    }

    /**
     * @return array Fungsi ini akan me-return array $result yang berisi atribut-atribut field.
     */
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

    /**
     * @param string $key
     * @param string $value
     * @param array $option
     * @return null Fungsi ini akan mengisi array $option[$key] dengan string $value (default value), jika array $option[$key] = null.
     */
    public function setDefaultOption($key, $value, &$option) {
        if (!isset($option[$key])) {
            $option[$key] = $value;
        }
    }

    /**
     * @return array Fungsi ini akan me-return originalName dari form field.
     */
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

    /**
     * @return array Fungsi ini akan me-return array yang berisi default atribut field.
     */
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

    /**
     * @param string $file
     * @return field Fungsi ini untuk me-render template.
     */
    public static function renderTemplate($file) {
        $reflector = new ReflectionClass(get_called_class());
        $path = str_replace(".php", DIRECTORY_SEPARATOR . $file, $reflector->getFileName());
        return file_get_contents($path);
    }

    /**
     * @return null Fungsi ini akan melakukan register script sebanyak array java script yang di-include.
     */
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

    /**
     * @return array Fungsi ini akan melakukan render script dan me-return array $html.
     */
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

    /**
     * @param array $file
     * @return field Fungsi ini untuk me-render form field dan atributnya.
     */
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

    /**
     * @param array $class
     * @param string $fieldName
     * @return null Fungsi ini untuk menambahkan class.
     */
    public function addClass($class, $fieldName = "options") {
        $opt = $this->$fieldName;
        $array = explode(" ", @$opt['class']);
        $array = array_unique(array_filter($array));
        array_push($array, $class);
        $opt['class'] = implode(" ", $array);
        $this->$fieldName = $opt;
    }

    public function setOption($key, $value, $fieldName = "options") {
        $keys = array_keys($this->$fieldName);

        if (!in_array($key, $keys)) {
            $a = $this->$fieldName;
            $a[$key] = $value;
            $this->$fieldName = $a;
        }
    }

    /**
     * @param array $attributes
     * @return string Fungsi ini untuk meng-expand atribut dan me-returnnya.
     */
    public function expandAttributes($attributes) {
        if (count($attributes) == 0)
            return "";
        return join(' ', array_map(function ($key) use ($attributes) {
                if (is_bool($attributes[$key])) {
                    return $attributes[$key] ? $key : '';
                }
                return $key . '="' . $attributes[$key] . '"';
            }, array_keys($attributes)));
    }

    /**
     * @return array Fungsi ini akan me-return semua atribut formfield.
     */
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

    /**
     * @return array Fungsi ini akan me-return array atribut formfield yang telah diurutkan.
     */
    public static function allSorted() {

        $all = FormField::all();
        usort($all, function ($a, $b) {
            return strlen($a['type']::$category) - strlen($b['type']::$category);
        });

        return $all;
    }

    /**
     * @param string $formType
     * @return array Fungsi ini digunakan untuk setting formfield yang sesuai dengan tipe form yang diterima pada parameter dan fungsi ini akan me-return array atibut.
     */
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
