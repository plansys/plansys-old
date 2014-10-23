<?php

/**
 * Class FormBuilder
 * @author rizky
 */
class FormBuilder extends CComponent {

    /** @var model $model */
    public $model = null;

    /**
     * @var array $_buildRenderID 
     * @access private	
     */
    private static $_buildRenderID = array();

    /**
     * @var integer $countRenderID
     * @access private	
     */
    private $countRenderID = 1;
    private $methods = array();
    private $file = array();
    private $sourceFile = '';
    private $originalClass = '';

    public static function classPath($class) {
        $classArr = explode(".", $class);
        $class = "";
        $classFile = "";
        foreach ($classArr as $k => $c) {
            if ($k < count($classArr) - 1) {
                if ($c == "FormFields") {
                    $classFile .= $c . ".";
                } else {
                    $classFile .= strtolower($c) . ".";
                }
            } else {
                $classFile .= $c;
            }
        }


        return $classFile == "" ? $class : $classFile;
    }

    /**
     * load 
     * Fungsi ini digunakan untuk me-load FormBuilder
     * @param array $class
     * @param array $attributes
     * @return mixed me-return null jika class tidak ada, jika ada maka me-return array $model 
     */
    public static function load($class, $attributes = null) {
        if (!is_string($class))
            return null;


        $originalClass = $class;
        if (strpos($class, ".") !== false) {
            $classFile = FormBuilder::classPath($class);
            $class = array_pop(explode(".", $classFile));

            try {
                Yii::import($classFile);
            } catch (Exception $e) {
                if (isset(Yii::app()->controller) && isset(Yii::app()->controller->module)) {
                    $basePath = Yii::app()->controller->module->basePath;
                }


                $classFile = str_replace(".", DIRECTORY_SEPARATOR, $classFile) . ".php";
                $classFile = $basePath . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . $classFile;
                require_once($classFile);
            }

            if (!class_exists($class)) {
                return null;
            }
        }

        $model = new FormBuilder();
        $model->model = new $class;
        $model->originalClass = $originalClass;

        if (!is_null($attributes)) {
            $model->model->attributes = $attributes;
        }

        ## get method line and length
        if (is_null(Yii::app()->session['FormBuilder_' . $originalClass])) {
            $reflector = new ReflectionClass($class);
            $model->sourceFile = $reflector->getFileName();
            $model->file = file($model->sourceFile, FILE_IGNORE_NEW_LINES);
            $methods = $reflector->getMethods();
            foreach ($methods as $m) {
                if ($m->class == $class) {
                    $line = $m->getStartLine() - 1;
                    $length = $m->getEndLine() - $line;
                    $model->methods[$m->name] = array(
                        'line' => $line,
                        'length' => $length
                    );
                }
            }

            Yii::app()->session['FormBuilder_' . $originalClass] = array(
                'sourceFile' => $model->sourceFile,
                'file' => $model->file,
                'methods' => $model->methods
            );
        } else {
            $s = Yii::app()->session['FormBuilder_' . $originalClass];
            $model->sourceFile = $s['sourceFile'];
            $model->file = $s['file'];
            $model->methods = $s['methods'];
        }
        return $model;
    }

    /**
     * @return array me-return array fields
     */
    public function getFields() {
        return $this->getFieldsInternal();
    }

    /**
     * @param boolean $processExpr
     * @return array me-return sebuah array fields internal
     */
    public function getFieldsInternal($processExpr = true) {

        ## if form class does not have getFields method, then create it
        $class = get_class($this->model);
        $reflector = new ReflectionClass($class);

        $functionName = 'getFields';
        if (is_subclass_of($this->model, 'FormField')) {
            $functionName = 'getFieldProperties';
        }

        if (!$reflector->hasMethod($functionName)) {
            $this->model = new $class;
            $fields = $this->model->defaultFields;
            $this->fields = $fields;
        } else {
            $fields = $this->model->$functionName();
        }

        if ($processExpr) {
            //process expression value
            $fields = $this->processFieldExpr($fields);
        }

        ## parse child field
        $processed = $this->parseFields($fields);
        return $processed;
    }

    private $_findFieldCache = null;

    public function findField($attributes, $recursive = null) {
        if (is_null($this->_findFieldCache)) {
            ## cache the fields
            $class = get_class($this->model);
            $reflector = new ReflectionClass($class);

            $functionName = 'getFields';
            if (is_subclass_of($this->model, 'FormField')) {
                $functionName = 'getFieldProperties';
            }

            if (!$reflector->hasMethod($functionName)) {
                $this->model = new $class;
                $fields = $this->model->defaultFields;
            } else {
                $fields = $this->model->$functionName();
            }

            $this->_findFieldCache = $this->parseFields($fields);
        }

        if (is_null($recursive)) {
            $fields = $this->_findFieldCache;
        } else {
            $fields = $recursive;
        }

        foreach ($fields as $k => $f) {
            if (!is_array($f))
                continue;

            $valid = 0;
            foreach ($f as $key => $value) {

                if (isset($f['name']))
                    foreach ($attributes as $attrKey => $attrVal) {
                        if ($key == $attrKey && $value == $attrVal) {
                            $valid++;
                        }
                    }
            }
            if ($valid == count($attributes)) {
                return $f;
            }

            if (isset($f['parseField']) && count($f['parseField']) > 0) {
                foreach ($f['parseField'] as $i => $j) {
                    $result = $this->findField($attributes, $f[$i]);
                    if ($result !== false) {
                        return $result;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param array $fields
     * @return array me-return array fields hasil dari proses expression field
     */
    public function processFieldExpr($fields) {
        foreach ($fields as $k => $f) {

            if (is_string($f)) {
                $fields[$k] = stripslashes($f);
                continue;
            }

            $class = @$f['type'];
            if ($class == null)
                continue;

            if ($class == "ColumnField") {
                $columnField = new ColumnField;
                $defaultTotalColumns = $columnField->totalColumns;
                $totalColumns = (isset($f['totalColumns']) ? $f['totalColumns'] : $defaultTotalColumns);
                for ($i = 1; $i <= $totalColumns; $i++) {
                    if (!isset($f['column' . $i])) {
                        continue;
                    }

                    $fields[$k]['column' . $i] = $this->processFieldExpr($f['column' . $i]);
                }
            } else if (method_exists($class, 'processExpr')) {

                $field = new $class;
                $field->attributes = $f;
                $field->builder = $this;
                ob_start();
                $processedField = $field->processExpr();
                $error = ob_get_clean();

                if ($error == "") {
                    $fields[$k] = array_merge($f, $processedField);
                }
            }
        }
        return $fields;
    }

    private function stripSlashesRecursive($array) {

        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $array[$key] = stripslashes($value);
            }
            if (is_array($value)) {
                $array[$key] = $this->stripSlashesRecursive($value);
            }
        }
        return $array;
    }

    /**
     * Fungsi ini akan mem-format, membenahi, dan mengevaluasi setiap field yang ada di dalam $fields
     * 
     * @param mixed $fields parameter dapat berupa array atau jika bukan array maka akan diubah menjadi array pada prosesnya
     * @return array me-return sebuah array fields hasil parseFields
     */
    public function parseFields($fields) {
        $processed = array();
        if (!is_array($fields))
            return $processed;

        foreach ($fields as $k => $f) {
            if (is_array($f)) {
                $field = new $f['type'];

                $f = $this->stripSlashesRecursive($f);
                $processed[$k] = array_merge($field->attributes, $f);

                if (count($field->parseField) > 0) {
                    foreach ($field->parseField as $i => $j) {
                        if (!isset($fields[$k][$i]))
                            continue;

                        $processed[$k][$i] = $this->parseFields($fields[$k][$i]);
                    }
                }
            } else {
                $value = $f;
                $processed[$k] = array(
                    'type' => 'Text',
                    'value' => str_replace("\'", "'", $value)
                );
            }
        }
        return $processed;
    }

    /**
     * @return array me-return array module.
     */
    public function getModule() {
        $class = get_class($this->model);
        $reflector = new ReflectionClass($class);
        $f = $reflector->getFileName();
        $dir = Yii::getPathOfAlias('application.modules');
        $f = str_replace($dir . DIRECTORY_SEPARATOR, "", $f);
        $f = explode(DIRECTORY_SEPARATOR, $f);
        return $f[0];
    }

    /** @const string Constanta NEWLINE_MARKER */
    const NEWLINE_MARKER = "!@#$%^&*NEWLINE&^%$#@!";

    /**
     * tidyAttributes
     * Fungsi ini berfungsi untuk menghapus atribut yang sama dengan default
     * @param array $data
     * @param array $fieldlist
     * @param boolean $preserveMultiline
     * @return array me-return array atribut
     */
    public function tidyAttributes($data, &$fieldlist, &$preserveMultiline = false) {
        if (isset($data['type'])) {
            if (!isset($fieldlist[$data['type']])) {
                $fieldlist[$data['type']] = $data['type']::attributes();
            }
            $defaultAttributes = $fieldlist[$data['type']];
        } else {
            $defaultAttributes = array();
        }

        foreach ($data as $i => $j) {
            if ($i == 'type' || $i == 'fields')
                continue;

            if (is_string($j)) {
                $j = addslashes($j);
                $data[$i] = $j;
            }

            if (is_array($preserveMultiline) && is_string($j)) {
                if (strpos($j, "\n") !== FALSE || strpos($j, PHP_EOL) !== FALSE) {
                    $hash = '---' . sha1($j) . '---';
                    $preserveMultiline[$hash] = $j;
                    $data[$i] = $hash;
                }
            }

            if (count($defaultAttributes) > 0) {
                if (!array_key_exists($i, $defaultAttributes) || $defaultAttributes[$i] == $j) {
                    unset($data[$i]);
                }
            }
        }

        return $data;
    }

    public function tidyRecursive(&$fields, &$multiline) {

        ## traverse attributes
        foreach ($fields as $k => $f) {
            if (is_array($f)) {
                $this->tidyRecursive($f, $multiline);

                ## tidying attributes, remove attribute that same as default attribute
                $f = $this->tidyAttributes($f, $fieldlist, $multiline);
            }


            ## okay, assign new attributes to field
            $fields[$k] = $f;
        }
    }

    /**
     * @param array $fields
     */
    public function setFields($fields) {
        $fieldlist = array();
        $multiline = array();

        $this->tidyRecursive($fields, $multiline);

        if (is_subclass_of($this->model, 'FormField')) {
            $this->updateFunctionBody('getFieldProperties', $fields, "", $multiline);
        } else {
            $this->updateFunctionBody('getFields', $fields, "", $multiline);
        }
    }

    /**
     * @return array me-return array atribut form.
     */
    public function getForm() {
        ## if form class does not have getFields method, then create it
        $class = get_class($this->model);
        $reflector = new ReflectionClass($class);

        if (!$reflector->hasMethod('getForm')) {
            $this->model = new $class;

            $defaultFields = array(
                'title' => str_replace(ucfirst($this->module), '', $class),
                'layout' => array(
                    'name' => 'full-width',
                    'data' => array(
                        'col1' => array(
                            'type' => 'mainform'
                        )
                    )
                ),
            );

            if (empty($this->getFunctionBody($reflector->getFileName(), 'getForm'))) {
                $this->form = $defaultFields;
            }

            $functionName = 'getFields';
            if (is_subclass_of($this->model, 'FormField')) {
                $functionName = 'getFieldProperties';
            }

            return $defaultFields;
        }

        return $this->model->form;
    }

    /**
     * @param array $form
     */
    public function setForm($form) {
        if (count($form['layout']['data']) > 0) {
            foreach ($form['layout']['data'] as $k => $d) {
                if (@$d['type'] == '' && @$d['size'] == '') {
                    unset($form['layout']['data'][$k]);
                }
            }
        }
        $this->updateFunctionBody('getForm', $form);
    }

    /**
     * build
     * Fungsi ini berfungsi untuk menentukan ID render dan merender-nya
     * @param array $class
     * @param array $attributes
     * @return array me-return array field 
     */
    public static function build($class, $attributes) {
        $field = new $class;
        $field->attributes = $attributes;

        ## make sure there is no duplicate renderID
        do {
            $renderID = rand(0, 1000000);
        } while (in_array($renderID, FormBuilder::$_buildRenderID));
        FormBuilder::$_buildRenderID[] = $renderID;

        $field->renderID = $renderID;
        return $field->render();
    }

    /**
     * @return array me-return array
     */
    public function registerScript() {
        $modelClass = get_class($this->model);
        $id = "NGCTRL_{$modelClass}_" . rand(0, 1000);
        Yii::app()->clientScript->registerScript($id, $this->renderAngularController(), CClientScript::POS_END);
        return $this->registerScriptInternal($this, $this->fields);
    }

    /**
     * @param array $fb form builder
     * @param array $fields
     * @return array me-return array
     */
    public function registerScriptInternal($fb, $fields) {

        foreach ($fields as $k => $f) {
            if (is_array($f)) {
                $field = new $f['type'];

                ## render all additional fields inside of particular field
                if (count($field->parseField) > 0) {
                    foreach ($field->parseField as $i => $j) {
                        $this->registerScriptInternal($fb, $f[$i]);
                    }
                }

                $field->registerScript();
            }
        }
    }

    /**
     * @return array me-return array
     */
    public function renderScript() {
        return $this->renderScriptInternal($this, $this->fields);
    }

    /**
     * @param array $fb form builder
     * @param array $fields
     * @param array $html
     * @return string me-return string berupa tag html.
     */
    public function renderScriptInternal($fb, $fields, $html = array()) {
        foreach ($fields as $k => $f) {
            if (is_array($f)) {
                $field = new $f['type'];

                ## render all additional fields inside of particular field
                if (count($field->parseField) > 0) {
                    foreach ($field->parseField as $i => $j) {
                        $this->registerScriptInternal($fb, $f[$i], $html);
                    }
                }

                $html = array_unique(array_merge($html, $field->renderScript()));
            }
        }

        return $html;
    }

    /**
     * @param array $formdata
     * @return string me-return string script yang di-include
     */
    public function renderAngularController($formdata = null, $renderParams = array()) {
        $modelClass = get_class($this->model);

        ## define formdata
        $data = $this->defineFormData($formdata);

        $reflector = new ReflectionClass($this->model);
        $inlineJSPath = dirname($reflector->getFileName()) . DIRECTORY_SEPARATOR . @$this->form['inlineJS'];
        $inlineJS = @file_get_contents($inlineJSPath);
        $script = include("FormBuilder.js.php");

        return $script;
    }

    /**
     * @param boolean $isAjax
     */
    public function renderAdditionalJS($isAjax = false) {
        $reflector = new ReflectionClass($this->model);
        $formDir = dirname($reflector->getFileName()) . DIRECTORY_SEPARATOR;

        if (count(@$this->form['includeJS']) > 0):
            foreach ($this->form['includeJS'] as $script):
                $src = $formDir . $script;
                if (is_file($src)) {
                    $scriptUrl = Yii::app()->assetManager->publish($src);

                    if ($isAjax) {
                        echo '
                    <script type="text/javascript" src="' . $scriptUrl . '"></script>';
                    } else {
                        Yii::app()->clientScript->registerScriptFile($scriptUrl, CClientScript::POS_END);
                    }
                }
            endforeach;
        endif;
    }

    /**
     * render
     * Fungsi ini untuk me-render Form Builder sesuai dengan $formdata
     * @param array $formdata
     * @param array $options 
     * @return string me-return string tag html hasil generate dari fungsi ini.
     */
    public function render($formdata = null, $options = array()) {

        return $this->renderInternal($formdata, $options, $this, $this->fields);
    }

    private function defineFormData($formdata) {
        $data = array();
        if (is_array($formdata)) {
            $data['data'] = $formdata;
        } else if (is_subclass_of($formdata, 'Form')) {
            $data['data'] = $formdata->attributes;
            $data['errors'] = $formdata->errors;
        } else if (is_subclass_of($formdata, 'ActiveRecord')) {
            $this->model = $formdata;

            $data['data'] = $formdata->attributes;
            $data['errors'] = $formdata->errors;
            $data['isNewRecord'] = $formdata->isNewRecord;
        }
        return $data;
    }

    /**
     * renderInternal
     * me-render field dan atribut-nya dalam form builder
     * @param array $formdata
     * @param array $options
     * @param array $fb 
     * @param array $fields
     * @return string me-return string berupa tag html 
     */
    private function renderInternal($formdata = null, $options = array(), $fb, $fields) {
        $html = "";

        $form = $fb->form;
        $moduleName = $fb->module;
        $modelClass = get_class($fb->model);


        ## setup default options
        $wrapForm = isset($options['wrapForm']) ? $options['wrapForm'] : true;
        $action = isset($options['action']) ? $options['action'] : 'create';
        $renderWithAngular = isset($options['renderWithAngular']) ? $options['renderWithAngular'] : true;
        $renderInAjax = isset($options['renderInAjax']) ? $options['renderInAjax'] : false;
        $FFRenderID = isset($options['FormFieldRenderID']) ? $options['FormFieldRenderID'] . '_' : '';
        $renderParams = isset($options['params']) ? $options['params'] : array();
        $renderParams = array_merge($_GET, $renderParams);

        ## wrap form
        if ($wrapForm) {
            $url = "#";
            $ngctrl = $renderWithAngular ? 'ng-controller="' . $modelClass . 'Controller"' : '';

            $formOptions = (is_array(@$form['options']) ? @$form['options'] : array());
            $formDefaultAttr = array(
                'action' => $url,
                'method' => 'POST',
                'class' => 'form-horizontal ' . @$formOptions['class'],
                'role' => 'form',
            );

            $formAttr = array_merge($formOptions, $formDefaultAttr);
            $formAttr = Helper::expandAttributes($formAttr);
            $html .= "<div style='opacity:0' {$ngctrl}><form {$formAttr}>";
            $html .= "<div ng-if='flash' class='error-container alert alert-success text-center' style='margin:0px'>{{flash}}</div>";
            $html .= "<div ng-if='errors' class='error-container alert alert-danger' style='margin:0px'><ul><li ng-repeat='(k,e) in errors' style='white-space:pre-wrap;'>{{ e[0] }}</li></ul></div>";
        }

        ## define formdata
        $data = $this->defineFormData($formdata);


        ## render semua html
        foreach ($fields as $k => $f) {
            if (is_array($f)) {
                $field = new $f['type'];

                ## assign existing field configuration to newly created field
                $field->attributes = $f;

                if (property_exists($field, 'name')) {
                    $field->name = preg_replace("/[^0-9a-zA-Z_]/", "", $field->name);
                    $field->name = preg_replace("/^\d+\.\s*/", "", $field->name);

                    if (property_exists($field, 'value') && isset($data['data'][$f['name']])) {
                        $field->value = $data['data'][$f['name']];
                    }

                    if (isset($data['errors'][$f['name']])) {
                        $field->errors = $data['errors'][$f['name']];
                    }
                }


                ## assign builder reference to this object
                $field->builder = $this;

                ## render all additional fields inside of particular field
                if (count($field->parseField) > 0) {
                    foreach ($field->parseField as $i => $j) {
                        $o = $options;
                        $o['wrapForm'] = false;
                        $field->$j = $this->renderInternal($formdata, $o, $fb, $f[$i]);
                    }
                }

                $field->formProperties = $form;

                ## assign field render id
                $field->renderID = $modelClass . '_' . $FFRenderID . $this->countRenderID++;

                ## then render the field, (including registering script)
                $html .= $field->render();
            } else {
                $html .= $f;
            }
        }

        ## wrap form
        if ($wrapForm) {
            $html .= '
                <input type="submit" style="display:none;"/>
                </form>
            </div>';

            if ($renderInAjax) {
                if ($renderWithAngular) {
                    ob_start();
                    ?>
                    <script type="text/javascript">
                    <?php echo $this->renderAngularController($formdata, $renderParams); ?>
                        registerController('<?= $modelClass ?>Controller');
                    </script>
                    <?php
                    $this->renderAdditionalJS(true);
                    ?>
                    <?php
                    $html .= ob_get_clean();
                }
            } else {
                if ($renderWithAngular) {
                    $id = "NGCTRL_{$modelClass}_" . rand(0, 1000);
                    $angular = $this->renderAngularController($formdata, $renderParams);
                    Yii::app()->clientScript->registerScript($id, $angular, CClientScript::POS_END);
                    $this->renderAdditionalJS();
                }
            }
        }

        return $html;
    }

    /**
     * formatCode
     * Fungsi ini untuk format code dan pengecekan code sesuai dengan pattern atau tidak
     * @param array $fields
     * @param array $indent
     * @return 
     */
    public static function formatCode($fields, $indent = "        ") {

        ## get fields
        $fields = var_export($fields, true);

        ## strip numerical array keys
        $fields = preg_replace("/[0-9]+\s*\=\> /i", '', $fields);

        ## replace unwanted formatting
        $replace = array(
            "  " => '    ',
            "=> \n" => "=>"
        );
        $fields = str_replace(array_keys($replace), $replace, $fields);
        $replace = array(
            "=>        array (" => '=> array (',
        );
        $fields = str_replace(array_keys($replace), $replace, $fields);
        $fields = explode("\n", $fields);

        ## indent each line
        $count = count($fields);
        foreach ($fields as $k => $f) {
            if ($k == 0)
                continue;

            $fields[$k] = $indent . $f;
            if ($k > 0 && $k < $count - 1) {
                if (trim($f) == "") {
                    unset($fields[$k]);
                }
            }
        }
        $fields = implode("\n", $fields);

        ## remove unwanted formattings
        $fields = preg_replace('/array\s*\([\n\r\s]*\),/', 'array (),', $fields);
        $fields = preg_replace('/=>\s*array \(/', '=> array (', $fields);

        return $fields;
    }

    protected function prepareLineForProperty() {
        ## get first line of the class
        $reflector = new ReflectionClass($this->model);
        $line = $reflector->getStartLine();

        ## when last line is like "{}" then separate it to new line
        $lastline = trim($this->file[count($this->file) - 1]);

        if (substr($lastline, 0, 5) == "class" && substr($lastline, -1) == "}") {
            $lastline[strlen($lastline) - 1] = " ";
            $this->file[$line - 1] = $lastline;
            $this->file[] = "";
            $this->file[] = "}";
        }

        if (substr($lastline, -1) == "}" && substr(trim($this->file[count($this->file) - 2]), -1) == "{") {
            array_splice($this->file, count($this->file) - 1, 0, '');

            foreach ($this->methods as $k => $m) {
                if ($m['line'] >= count($this->file) - 1) {
                    $this->methods[$k]['line'] += 1;
                }
            }
        }
        return $line;
    }

    protected function prepareLineForMethod() {
        $first_line = $this->prepareLineForProperty();

        foreach ($this->file as $line => $content) {
            if (preg_match('/\s*(private|protected|public)\s+function\s+.*/x', $content)) {
                break;
            }
        }

        ## prepare the line
        array_splice($this->file, $line, 0, "");

        ## adjust methods line and number
        foreach ($this->methods as $k => $m) {
            if (@$m['line'] >= $line) {
                $this->methods[$k]['line'] += 1;
            }
        }

        return $line;
    }

    private function getLineOfClass($class, $name) {
        $isNewFunc = false;
        ## get first line of the class       
        if (!isset($this->methods[$name])) {
            $line = $this->prepareLineForMethod();
            $length = 0;
            $isNewFunc = true;
        } else {
            $line = $this->methods[$name]['line'];
            $length = $this->methods[$name]['length'];
            $endline = $line + $length;

            ## when last line is like "}}" then separate it to new line
            $lastline = trim($this->file[$endline - 1]);
            if (substr($lastline, -2) == "}}") {
                $lastline[strlen($lastline) - 1] = " ";
                $this->file[$endline - 1] = $lastline;
                $this->file[] = "\n";
                $this->file[] = "}";
            }
        }
        return array(
            'file' => $this->file,
            'length' => $length,
            'line' => $line,
            'sourceFile' => $this->sourceFile,
            'isNewFunc' => $isNewFunc
        );
    }

    /**
     * @param string $functionName
     * @param array $fields Parameter
     * @param array $class Parameter
     * @param array $replaceString
     * @return field Fungsi ini digunakan untuk update model.
     */
    public function updateFunctionBody($functionName, $fields, $class = "", $replaceString = null) {
        if ($class == "") {
            $using_another_class = false;
            $class = get_class($this->model);
        } else {
            $using_another_class = true;
        }

        ## get class data
        extract($this->getLineOfClass($class, $functionName));

        if (is_array($fields)) {
            $fields = FormBuilder::formatCode($fields);

            ## replace multiline string to preserve indentation
            if (is_array($replaceString)) {
                $fields = str_replace(array_keys($replaceString), $replaceString, $fields);
            }

            ## generate function
            $func = <<<EOF
    public function {$functionName}() {
        return {$fields};
    }
EOF;
        } else {
            $func = $fields;
        }

        ## put function to class 
        array_splice($file, $line, $length, explode("\n", $func));

        ## adjust other methods line and length
        $newlength = count(explode("\n", $func));
        foreach ($this->methods as $k => $m) {
            if (@$m['line'] >= $line && $k != $functionName) {
                if (!$isNewFunc) {
                    $this->methods[$k]['line'] -= $length;
                }

                $this->methods[$k]['line'] += $newlength;
            }
        }
        $this->methods[$functionName]['length'] = $newlength;
        $this->methods[$functionName]['line'] = $line;

        $this->file = $file;

        $fp = fopen($sourceFile, 'r+');
        ## write new function to sourceFile
        if (flock($fp, LOCK_EX)) { // acquire an exclusive lock
            ftruncate($fp, 0); // truncate file
            fwrite($fp, implode("\n", $file));
            fflush($fp); // flush output before releasing the lock
            flock($fp, LOCK_UN); // release the lock
            //print_r(Yii::app()->session['FormBuilder_' . $this->originalClass]['methods']);

            Yii::app()->session['FormBuilder_' . $this->originalClass] = array(
                'sourceFile' => $this->sourceFile,
                'file' => $file,
                'methods' => $this->methods
            );

            //print_r(Yii::app()->session['FormBuilder_' . $this->originalClass]['methods']);
        } else {
            echo "ERROR: Couldn't lock source file '{$sourceFile}'!";
            die();
        }

        if (!$using_another_class) {
            ## update model instance
            $this->model = new $class;
        }
    }

    /**
     * @param array $sourceFile
     * @param string $functionName
     * @return array me-return sebuah array function.
     */
    public function getFunctionBody($sourceFile, $functionName) {
        $fd = fopen($sourceFile, "r");
        $ret = array();
        while (!feof($fd)) {
            $content = fgets($fd);
            if ($content == "") {
                continue;
            }
            if (isset($ret['args'])) {
                if ($content == "//EOF")
                    break;
                if (preg_match('/^\s*function\s+/', $content)) {
                    // EOFunction?
                    break;
                }
                $ret['body'] .= $content;
                continue;
            }
            if (preg_match('/function\s+(.*)\s*\((.*)\)\s*\{\s*/', $content, $resx)) {
                if ($resx[1] == $functionName) {
                    $ret['args'] = $resx[2];
                    $ret['body'] = "";
                }
            }
        }
        fclose($fd);
        return $ret;
    }

    /**
     * @param string $module
     * @return array Fungsi ini akan me-return sebuah array list controller .
     */
    public static function listController($module) {
        $ctr_dir = Yii::getPathOfAlias("application.modules.{$module}.controllers") . DIRECTORY_SEPARATOR;
        $items = glob($ctr_dir . "*.php");
        $list = array();

        foreach ($items as $k => $f) {
            $f = str_replace($ctr_dir, "", $f);
            $f = str_replace('Controller.php', "", $f);
            $list[lcfirst($f)] = lcfirst($f);
        }

        return $list;
    }

    /**
     * @param string $module
     * @return array Fungsi ini akan me-return sebuah array list form .
     */
    public static function listForm($module = null, $useAlias = true, $excludeModule = true) {
        $list = array();
        $list[''] = '-- NONE --';
        $modules = FormBuilder::listFile(false);
        foreach ($modules as $m) {
            if (!is_null($module) && strtolower($m['module']) != strtolower($module))
                continue;


            if ($excludeModule !== true) {
                if (in_array($m['module'], $excludeModule))
                    continue;
            }

            $list[$m['module']] = array();
            foreach ($m['items'] as $file) {
                $f = &$file;
                while (!isset($f['alias'])) {
                    $f = array_pop($f);
                }

                if ($useAlias) {
                    $list[$file['alias']] = $file['name'];
                } else {
                    $list[$file['class']] = $file['name'];
                }
            }
        }

        return $list;
    }

    private static function formatGlob($items, $item_dir, $module, $func, $alias, $format = true, $return = array()) {
        $subdir_val = array();
        $id = 0;
        foreach ($items as $k => $i) {
            $item = array();
            $i = realpath($i);
            $file_dir = dirname($i) . DIRECTORY_SEPARATOR;
            $subdir = trim(str_replace(DIRECTORY_SEPARATOR, '.', str_replace($item_dir, '', $file_dir)), '.');

            $item = str_replace($file_dir, "", $i);
            $item = str_replace('.php', "", $item);
            $newAlias = trim(trim($alias, '.') . '.' . $subdir, '.');
            $item = $func($item, $module, $newAlias, $i);
            $item['id'] = $id++;


            if ($subdir == '' || !$format) {
                $return[$k] = $item;
            } else {
                $subarr = explode(".", $subdir);

                $curpath = &$return;
                foreach ($subarr as $s) {
                    $id++;
                    if (!isset($curpath[$s])) {
                        $curpath[$s] = array(
                            'items' => array(),
                            'name' => ucfirst($s),
                            'id' => $id++
                        );
                    }
                    $curpath = &$curpath[$s]['items'];
                }

                if (!isset($subdir_val[$subdir]))
                    $subdir_val[$subdir] = array();

                $subdir_val[$subdir][] = $item;
                $curpath = $subdir_val[$subdir];
            }
        }
        $return = Helper::arrayValuesRecursive($return);
        return $return;
    }

    /**
     * @param string $dir
     * @param string $func
     * @return array me-return sebuah array list file .
     */
    public static function listFile($formatRecursive = true) {
        $files = array();

        $func = function($m, $module = "", $aliaspath = "", $path) {
            return array(
                'name' => str_replace(ucfirst($module), '', $m),
                'class' => $m,
                'alias' => $aliaspath . "." . $m,
                'items' => array()
            );
        };

        ## add files in FormFields Dir
        $forms_dir = Yii::getPathOfAlias("application.components.ui.FormFields") . DIRECTORY_SEPARATOR;
        $items = glob($forms_dir . "*.php");
        foreach ($items as $k => $f) {
            $items[$k] = str_replace($forms_dir, "", $f);
            $items[$k] = str_replace('.php', "", $items[$k]);
            if (!is_null($func)) {
                $items[$k] = $func($items[$k], "", "application.components.ui.FormFields", $f);
            }
        }

        $files[] = array(
            'module' => 'Form Fields',
            'items' => $items,
        );

        ##  add files in Root Form dir
        $forms_dir = Yii::getPathOfAlias("application.forms") . DIRECTORY_SEPARATOR;
        $items = Helper::globRecursive($forms_dir . "*.php");
        foreach ($items as $k => $f) {
            $f = realpath($f);
            $file_dir = dirname($f) . DIRECTORY_SEPARATOR;
            $items[$k] = str_replace($file_dir, "", $f);
            $items[$k] = str_replace('.php', "", $items[$k]);
            if (!is_null($func)) {
                $alias = trim(str_replace($forms_dir, '', $file_dir), DIRECTORY_SEPARATOR);
                $alias = trim('application.forms.' . $alias, '.');
                $items[$k] = $func($items[$k], "", $alias, $f);
            }
        }

        $files[] = array(
            'module' => 'Root Form',
            'items' => $items
        );

        ## add files in Plansys Modules Dir
        $module_dir = Yii::getPathOfAlias('application.modules');
        if (file_exists($module_dir)) {
            $modules = glob($module_dir . DIRECTORY_SEPARATOR . "*");
            foreach ($modules as $m) {
                $module = ucfirst(str_replace($module_dir . DIRECTORY_SEPARATOR, '', $m));
                $alias = "application.modules.{$module}.forms.";
                $item_dir = $m . DIRECTORY_SEPARATOR . "forms" . DIRECTORY_SEPARATOR;
                $items = Helper::globRecursive($item_dir . "*.php");
                $items = FormBuilder::formatGlob($items, $item_dir, $module, $func, $alias, $formatRecursive);

                if (count($items) > 0) {
                    $files[] = array(
                        'module' => $module,
                        'items' => $items
                    );
                }
            }
        }

        ## add files in App Modules Dir
        $module_dir = Yii::getPathOfAlias('app.modules');
        if (file_exists($module_dir)) {
            $modules = glob($module_dir . DIRECTORY_SEPARATOR . "*");
            foreach ($modules as $m) {
                $module = ucfirst(str_replace($module_dir . DIRECTORY_SEPARATOR, '', $m));
                $alias = "app.modules.{$module}.forms.";
                $item_dir = $m . DIRECTORY_SEPARATOR . "forms" . DIRECTORY_SEPARATOR;
                $items = Helper::globRecursive($item_dir . "*.php");
                $items = FormBuilder::formatGlob($items, $item_dir, $module, $func, $alias, $formatRecursive);

                if (count($items) > 0) {
                    $files[] = array(
                        'module' => $module,
                        'items' => $items
                    );
                }
            }
        }

        return $files;
    }

}
