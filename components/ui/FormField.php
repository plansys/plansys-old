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
    private $_errors = [];

    /**
     * @var array $_form_properties 
     * @access private	
     */
    private $_form_properties = [
        'formTitle' => '',
        'layout' => [
            'name' => 'full-width',
            'data' => [
                'col1' => [
                    'type' => 'mainform'
                ]
            ]
        ],
    ];

    /**
     * @var array $_builder
     * @access private	
     */
    private $_builder = null;

    /** @var array $parseField */
    public $parseField = []; // list of form fields to be parsed array('from'=>'to')

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
    public static $categorySettings = [
        'User Interface' => [
            'icon' => 'fa-cubes',
        ],
        'Layout' => [
            'icon' => 'fa-image',
        ],
        'Data & Tables' => [
            'icon' => 'fa-th-large',
        ],
        'Charts' => [
            'icon' => 'fa-bar-chart',
        ]
    ];

    /**
     * @return array me-return array javascript yang di-include.
     */
    public function includeJS() {
        return [];
    }

    public function includeCSS() {
        return [];
    }

    /**
     * @return string me-return string nama class sebuah object.
     */
    public function getType() {
        return get_class($this);
    }

    ## builder

    /**
     * @return array me-return array $_builder.
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
     * @return array me-return array value model.
     */
    public function getModel() {

        if (!is_null($this->_builder) && get_class($this->_builder) == "FormBuilder") {
            return $this->_builder->model;
        }
        return [];
    }

    ## parent form properties

    /**
     * @param array $value
     */
    public function setFormProperties($value) {
        $this->_form_properties = $value;
    }

    /**
     * @return array me-return array yang berisi property form.
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
    public function evaluate($expr, $return = false, $variables = []) {
        $error_level = error_reporting(0);
        
        if (!isset($this->builder->model)) {
            $result = $this->evaluateExpression($expr, $variables);
        } else {
            $result = $this->evaluateExpression($expr, array_merge($variables, [
                'model' => $this->builder->model,
            ]));
        }

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
     * render
     * Fungsi ini untuk me-render form field beserta atributnya
     * @return mixed me-return sebuah form field 
     */
    public function render() {
        return $this->renderInternal('template_render.php');
    }

    /**
     * setAttribute
     * Fungsi ini akan mengisi nilai atribut field dengan nilai dari parameter $values
     * @param array $values
     */
    public function setAttributes($values) {
        if (is_array(@$values)) {
            foreach ($values as $k => $v) {
                if (property_exists($this, $k)) {
                    $this->$k = $v;
                }
            }
        }
    }

    /**
     * @return string me-return string nama class 
     */
    public function getRenderName() {
        if (property_exists($this, 'name')) {

            if (is_array($this->model)) {
                return $this->name;
            } else {
                return get_class($this->model) . "[" . $this->name . "]";
            }
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
        $result = [];
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
            $name = str_replace([" ", "]"], "", $name[count($name) - 1]);
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
        $exclude = [
            'data',
            'id'
        ];
        foreach ($fields as $k => $f) {
            if (in_array($k, $exclude)) {
                unset($fields[$k]);
                continue;
            }

            $fields[] = [
                'name' => $k,
                'type' => 'Text'
            ];
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
                $jspath = realpath(Yii::getPathOfAlias("application.components.ui.FormFields.{$class}") . '/' . $js);

                if (is_dir($jspath)) {
                    $path = Asset::publish($jspath);
                    $files = glob($jspath . "/*");

                    foreach ($files as $p) {
                        if (pathinfo($p, PATHINFO_EXTENSION) != "js") {
                            continue;
                        }

                        $p = str_replace($jspath, '', realpath($p));
                        Yii::app()->clientScript->registerScriptFile($path . str_replace("\\", "/", $p), CClientScript::POS_END);
                    }
                } else {
                    Yii::app()->clientScript->registerScriptFile(
                            Asset::publish($jspath, true), CClientScript::POS_END
                    );
                }
            }
        }

        $includeCSS = $this->includeCSS();
        foreach ($includeCSS as $css) {
            $class = get_class($this);
            $csspath = Yii::getPathOfAlias("application.components.ui.FormFields.{$class}") . '/' . $css;
            Yii::app()->clientScript->registerCSSFile(Asset::publish($csspath, true), 'ALL');
        }
    }

    /**
     * @return array Fungsi ini akan melakukan render script dan me-return array $html.
     */
    public function renderScript() {
        $includeJS = $this->includeJS();
        
        $html = [];
        if (count($includeJS) > 0) {
            foreach ($includeJS as $js) {
                $class = get_class($this);
                $jspath = realpath(Yii::getPathOfAlias("application.components.ui.FormFields.{$class}") . '/' . $js);

                if (is_dir($jspath)) {
                    $path = Asset::publish($jspath);
                    $files = glob($jspath . "/*");
                    foreach ($files as $p) {
                        $p = str_replace($jspath, '', realpath($p));
                        $html[] = $path . str_replace("\\", "/", $p);
                    }
                } else {
                    $html[] = Asset::publish($jspath);
                }
            }
        }

        $includeCSS = $this->includeCSS();
        if (count($includeCSS) > 0) {
            foreach ($includeCSS as $css) {
                $class = get_class($this);
                $html[] = Asset::publish(
                                Yii::getPathOfAlias("application.components.ui.FormFields.{$class}") . '/' . $css, true
                );
            }
        }
        return $html;
    }

    /**
     * @param array $file
     * @return field Fungsi ini untuk me-render form field dan atributnya.
     */
    public function renderInternal($file, $attr = []) {

        $reflector = new ReflectionClass($this);
        $path = str_replace(".php", DIRECTORY_SEPARATOR . $file, $reflector->getFileName());

        $attributes = $attr + [
            'field' => $this->attributes,
            'form' => $this->_form_properties,
            'errors' => $this->_errors,
            'model' => $this->model
        ];

        extract($attributes);

        $this->registerScript();

        ob_start();
        if (file_exists($path)) {
            include($path);
        }
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

    /**
     * setOption
     * Fungsi ini untuk menambahkan class
     * @param array $key
     * @param array $value
     * @param string $fieldName
     */
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
     * @return string me-return string atribut hasil dari proses expand
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
     * @param array $raw
     * @return string me-return json hasil dari proses extract
     */
    public function extractJson($raw) {
        if (count($raw) == 0)
            return null;
        $list = [];
        foreach ($raw as $key => $content) {
            $keyArr = explode('.', $key);
            $key = "['" . implode("']['", $keyArr) . "']";
            
            if ($content === 'true' || $content === 'false') {
                $content = ($content === 'true');
            }

            eval('$list' . $key . '= $content;');
        }

        return $list;
    }

    /**
     * @return array me-return array semua atribut formfield.
     */
    public static function all() {
        $ffdir = Yii::getPathOfAlias('application.components.ui.FormFields') . DIRECTORY_SEPARATOR;
        $dir = glob($ffdir . "*.php");

        $return = [];
        foreach ($dir as $k => $d) {
            $class = str_replace($ffdir, "", $d);
            $class = str_replace(".php", "", $class);

            if (property_exists($class, 'toolbarName')) {
                $a = new $class;
                $array = $a->attributes;
                $array['name'] = $class::$toolbarName;
                if (isset($array['label'])) {
                    $array['label'] = $class::$toolbarName;
                }

                $return [] = $array;
            }
        }
        return $return;
    }

    /**
     * @return array me-return array atribut formfield yang telah diurutkan.
     */
    public static function allSorted() {

        $all = FormField::all();
        $cat = [];

        foreach ($all as $u) {
            if (!isset($cat[$u['type']::$category])) {
                $cat[$u['type']::$category] = [];
            }
            $cat[$u['type']::$category][] = $u;
        }
        $return = [];
        foreach ($cat as $a) {
            foreach ($a as $b) {
                $return[] = $b;
            }
        }

        return $return;
    }

    /**
     * @param string $formType
     * @return array me-return array atribut formfield
     */
    public static function settings($formType) {
        $ffdir = Yii::getPathOfAlias('application.components.ui.FormFields') . DIRECTORY_SEPARATOR;
        $dir = glob($ffdir . "*.php");
        $result = [
            'icon' => [],
            'category' => []
        ];
        foreach ($dir as $d) {
            $class = str_replace($ffdir, "", $d);
            $class = str_replace(".php", "", $class);

            if (property_exists($class, 'toolbarIcon') && property_exists($class, 'category')) {
                $result['icon'][$class] = $class::$toolbarIcon;
                $result['category'][$class] = $class::$category;
            }
        }

        return $result;
    }

}
