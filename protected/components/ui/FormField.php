<?php

class FormField extends CComponent {

	/** 
	* @var array variable untuk menampung error. 
	* @access private	
	*/
    private $_errors = array();
	
	/** 
	* @var array variable untuk menampung property form. 
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
	* @var array variable untuk menampung builder. 
	* @access private	
	*/
    private $_builder = null;
	
	/** @var array variable untuk menampung list form field yang akan di parsing */
    public $parseField = array(); // list of form fields to be parsed array('from'=>'to')
	
	/** @var string variable untuk menampung ID render */
    public $renderID = ""; //to distinguish one field to another, will be filled when rendering, -NOT- in editor
	
	/** @var boolean variable untuk menampung kondisi tampilan form field dengan kondisi default false atau not-hidden*/
    public $isHidden = false;
	
	/** @var string variable untuk menampung toolbarName */
    public static $toolbarName;
	
	/** @var string variable untuk menampung category */
    public static $category;
	
	/** @var string variable untuk menampung toolbarIcon */
    public static $toolbarIcon;
	
	/** @var boolean variable untuk menampung kondisi dalam editor dengan default false */
    public static $inEditor = false;
	
	/** @var array variable untuk menampung setting category */
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
	 * @return string Fungsi ini akan me-return tipe class.
	*/
    public function getType() {
        return get_class($this);
    }

    ## builder

	/**
	 * @return array Fungsi ini akan me-return value builder.
	*/
    public function getBuilder() {
        return $this->_builder;
    }

	/**
	 * @param array $builder Parameter untuk manampung value builder.
	 * @return array Fungsi ini akan men-set value builder.
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
	 * @param array $value Parameter untuk manampung value property form.
	 * @return array Fungsi ini akan men-set property form field.
	*/
    public function setFormProperties($value) {
        $this->_form_properties = $value;
    }

	/**
	 * @return array Fungsi ini akan me-return array property form field.
	*/
    public function getFormProperties() {
        return $this->_form_properties;
    }

    ## errors

	/**
	 * @return array Fungsi ini akan me-return error.
	*/
    public function getErrors() {
        return $this->_errors;
    }

	/**
	 * @param array $error Parameter untuk manampung value error.
	 * @return array Fungsi ini akan men-set error.
	*/
    public function setErrors($error) {
        return $this->_errors = $error;
    }

	/**
	 * @param array $expr Parameter untuk manampung expression.
	 * @param array $return Parameter untuk manampung kondisi return dengan default false.
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
	 * @return array Fungsi ini akan me-return form beserta property field.
	*/
    public function getForm() {
        return $this->_form_properties;
    }

	/**
	 * @return array Fungsi ini akan me-return template.
	*/
    public static function template() {
        return self::renderTemplate('template_editor.php');
    }

	/**
	 * @return field Fungsi ini untuk me-render form field.
	 */
    public function render() {
        return $this->renderInternal('template_render.php');
    }
	
	/**
	 * @param array $values Parameter untuk manampung value atribut.
	 * @return array Fungsi ini akan men-set value atribut.
	*/
    public function setAttributes($values) {
        foreach ($values as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }

	/**
	 * @return array Fungsi ini akan me-return atribut field.
	*/
    public static function attributes() {
        $field = new static();
        return $field->attributes;
    }

	/**
	 * @return array Fungsi ini akan me-return array yang berisi atribut-atribut field.
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
	 * @return array Fungsi ini akan me-return array yang berisi default fields.
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
	 * @param string $file Parameter untuk manampung file tamplate.
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
	 * @return null Fungsi ini akan melakukan render script.
	*/
    public function renderScript() {
        $includeJS = $this->includeJS();
        $html = array();
        if (count($includeJS) > 0) {
            foreach ($includeJS as $js) {
                $class = get_class($this);
                $html[] = Yii::app()->assetManager->publish(
                    Yii::getPathOfAlias("application.components.ui.FormFields.{$class}") . '/' . $js
                );
            }
        }
        return $html;
    }

	/**
	 * @param array $file Parameter untuk manampung file.
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
	 * @param array $class Parameter untuk manampung class.
	 * @param string $fieldName Parameter untuk manampung nama field.
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

	/**
	 * @param array $attributes Parameter untuk manampung atribut-atribut.
	 * @return  Fungsi ini untuk meng-expand atribut.
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
