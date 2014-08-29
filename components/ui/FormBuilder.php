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

        if (strpos($class, ".") !== false) {
            $classfile = $class;
            $class = array_pop(explode(".", $classfile));
            Yii::import($classfile);

            if (!class_exists($class)) {
                return null;
            }
        }

        $model = new FormBuilder();
        $model->model = new $class;

        if (!is_null($attributes)) {
            $model->model->attributes = $attributes;
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

                foreach ($f as $key => $value) {
                    if (is_string($value)) {
                        $f[$key] = stripslashes($value);
                    }
                }

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
        if (!isset($fieldlist[$data['type']])) {
            $fieldlist[$data['type']] = $data['type']::attributes();
        }
        $defaultAttributes = $fieldlist[$data['type']];


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

            if (!array_key_exists($i, $defaultAttributes) || $defaultAttributes[$i] == $j) {
                unset($data[$i]);
            }
        }

        return $data;
    }

    /**
     * @param array $fields
     */
    public function setFields($fields) {
        $fieldlist = array();
        $multiline = array();

        ## prepare attributes
        foreach ($fields as $k => $f) {
            ## when the type is text, pass it as a string (not array attribute like the other)
            if ($f['type'] == "Text" && @$f['renderInEditor'] != 'No') {
                $hash = '---' . sha1($f['value']) . '---';
                $fields[$k] = $hash;
                $multiline[$hash] = str_replace("'", "\'", $f['value']);
            } else {
                ## tidying attributes, remove attribute that same as default attribute
                $f = $this->tidyAttributes($f, $fieldlist, $multiline);

                if (isset($fieldlist[$f['type']]['parseField']) && count($fieldlist[$f['type']]['parseField']) > 0) {
                    foreach ($fieldlist[$f['type']]['parseField'] as $i => $j) {
                        if (!isset($f[$i]))
                            continue;

                        foreach ($f[$i] as $m => $o) {
                            if (is_string($f[$i][$m]))
                                continue;

                            if ($f[$i][$m]['type'] == "Text") {
                                $f[$i][$m] = $o['value'];
                            } else {
                                $f[$i][$m] = $this->tidyAttributes($o, $fieldlist, $multiline);
                            }
                        }
                    }
                }
                ## okay, assign new attributes to field
                $fields[$k] = $f;
            }
        }

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
                'formTitle' => str_replace(ucfirst($this->module), '', $class),
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
     * generateCreateAction
     * Fungsi ini akan meng-generate function dan create action.
     */
    public function generateCreateAction() {

        $class = get_class($this->model);

        ## generate function
        $functionBody = <<<EOF
    public function {$this->form['createAction']}() {
        \$model = new {$class};
        
        if (isset(\$_POST['{$class}'])) {
            \$model->attributes = \$_POST['{$class}'];
            
            if (\$model->save()) {
                \$this->redirect(array('index'));
            }
        }
        
        \$this->renderForm('{$class}', 'create', \$model);
    }

EOF;

        $this->updateFunctionBody($this->form['createAction'], $functionBody, $this->form['controller']);
    }

    /**
     * generateUpdateAction
     * Fungsi ini akan meng-generate function dan update action.
     */
    public function generateUpdateAction() {
        ## generate function
        $functionBody = <<<EOF
    public function {$this->form['updateAction']}() {
        echo "F";
    }

EOF;
        $this->updateFunctionBody($this->form['updateAction'], $functionBody, $this->form['controller']);
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
    public function renderAngularController($formdata = null) {
        $modelClass = get_class($this->model);

        ## define formdata
        if (is_array($formdata)) {
            $data['data'] = $formdata;
        } else if (
            is_subclass_of($formdata, 'ActiveRecord') ||
            is_subclass_of($formdata, 'Form')
        ) {
            $data['data'] = $formdata->attributes;
            $data['errors'] = $formdata->errors;
        }

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

        ## wrap form
        if ($wrapForm) {
            $url = "#";
            $ngctrl = $renderWithAngular ? 'ng-controller="' . $modelClass . 'Controller"' : '';

            $formDefaultAttr = array(
                'action' => $url,
                'method' => 'POST',
                'class' => 'form-horizontal',
                'role' => 'form',
            );
            $formOptions = (is_array(@$form['options']) ? @$form['options'] : array());

            $formAttr = array_merge($formOptions, $formDefaultAttr);
            $formAttr = Helper::expandAttributes($formAttr);
            $html .= "<div style='opacity:0' {$ngctrl}><form {$formAttr}>";
        }

        ## define formdata
        if (is_array($formdata)) {
            $data['data'] = $formdata;
        } else if (
            is_subclass_of($formdata, 'ActiveRecord') ||
            is_subclass_of($formdata, 'Form')
        ) {
            $data['data'] = $formdata->attributes;
            $data['errors'] = $formdata->errors;
        }

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
                    <?php echo $this->renderAngularController($formdata); ?>
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
                    $angular = $this->renderAngularController($formdata);
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

        ## get first line of the class
        $reflector = new ReflectionClass($class);
        if (!$reflector->hasMethod($functionName)) {
            $sourceFile = $reflector->getFileName();
            $line = $reflector->getStartLine();
            $file = file($sourceFile);
            $length = 0;

            ## when last line is like "{}" then separate it to new line
            $lastline = trim($file[$line - 1]);
            if (substr($lastline, 0, 5) == "class" && substr($lastline, -1) == "}") {
                $lastline[strlen($lastline) - 1] = " ";
                $file[$line - 1] = $lastline;
                $file[] = "\n";
                $file[] = "}";
                $line = $line + 1;
            }
        } else {
            $reflector = new ReflectionMethod($class, $functionName);
            $sourceFile = $reflector->getFileName();
            $line = $reflector->getStartLine() - 1;
            $file = file($sourceFile);
            $length = $reflector->getEndLine() - $line;

            ## when last line is like "}}" then separate it to new line
            $lastline = trim($file[$reflector->getEndLine() - 1]);
            if (substr($lastline, -2) == "}}") {
                $lastline[strlen($lastline) - 1] = " ";
                $file[$reflector->getEndLine() - 1] = $lastline;
                $file[] = "\n";
                $file[] = "}";
            }
        }

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
        array_splice($file, $line, $length, array($func));

        $fp = fopen($sourceFile, 'r+');
        ## write new function to sourceFile
        if (flock($fp, LOCK_EX)) { // acquire an exclusive lock
            ftruncate($fp, 0); // truncate file
            fwrite($fp, implode("", $file));
            fflush($fp); // flush output before releasing the lock
            flock($fp, LOCK_UN); // release the lock
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
    public static function listForm($module = null) {
        $list = array();
        $list[''] = '-- NONE --';
        $modules = FormBuilder::listFile();
        foreach ($modules as $module) {
            $list[$module['module']] = array();
            foreach ($module['items'] as $file) {
                $list[$file['alias']] = $file['name'];
            }
        }

        return $list;
    }

    /**
     * @param string $dir
     * @param string $func
     * @return array me-return sebuah array list file .
     */
    public static function listFile() {
        $files = array();

        $func = function($m, $module = "", $aliaspath = "") {
            return array(
                'name' => str_replace(ucfirst($module), '', $m),
                'class' => $m,
                'alias' => $aliaspath . "." . $m
            );
        };

        ## add files in FormFields Dir
        $forms_dir = Yii::getPathOfAlias("application.components.ui.FormFields") . DIRECTORY_SEPARATOR;
        $items = glob($forms_dir . "*.php");
        foreach ($items as $k => $f) {

            $items[$k] = str_replace($forms_dir, "", $f);
            $items[$k] = str_replace('.php', "", $items[$k]);
            if (!is_null($func)) {
                $items[$k] = $func($items[$k], "", "application.components.ui.FormFields");
            }
        }
        $files[] = array(
            'module' => 'Form Fields',
            'items' => $items
        );
        ## end..
        ##  add files in Root Form dir
        $forms_dir = Yii::getPathOfAlias("application.forms") . DIRECTORY_SEPARATOR;
        $items = glob($forms_dir . "*.php");
        foreach ($items as $k => $f) {

            $items[$k] = str_replace($forms_dir, "", $f);
            $items[$k] = str_replace('.php', "", $items[$k]);
            if (!is_null($func)) {
                $items[$k] = $func($items[$k], "", "application.forms");
            }
        }
        $files[] = array(
            'module' => 'Root Form',
            'items' => $items
        );
        ## end..
        ## add files in Plansys Modules Dir
        $module_dir = Yii::getPathOfAlias('application.modules');
        if (file_exists($module_dir)) {
            $modules = glob($module_dir . DIRECTORY_SEPARATOR . "*");
            foreach ($modules as $m) {
                $module = ucfirst(str_replace($module_dir . DIRECTORY_SEPARATOR, '', $m));
                $item_dir = $m . DIRECTORY_SEPARATOR . "forms" . DIRECTORY_SEPARATOR;
                $items = glob($item_dir . "*.php");
                foreach ($items as $k => $i) {
                    $items[$k] = str_replace($item_dir, "", $i);
                    $items[$k] = str_replace('.php', "", $items[$k]);

                    if (!is_null($func)) {
                        $items[$k] = $func($items[$k], $module, 'application.modules.' . $module . ".form");
                    }
                }

                $files[] = array(
                    'module' => $module,
                    'items' => $items
                );
            }
        }

        ## add files in App Modules Dir
        $module_dir = Yii::getPathOfAlias('app.modules');
        if (file_exists($module_dir)) {
            $modules = glob($module_dir . DIRECTORY_SEPARATOR . "*");
            foreach ($modules as $m) {
                $module = ucfirst(str_replace($module_dir . DIRECTORY_SEPARATOR, '', $m));
                $item_dir = $m . DIRECTORY_SEPARATOR . "forms" . DIRECTORY_SEPARATOR;
                $items = glob($item_dir . "*.php");
                foreach ($items as $k => $i) {
                    $items[$k] = str_replace($item_dir, "", $i);
                    $items[$k] = str_replace('.php', "", $items[$k]);

                    if (!is_null($func)) {
                        $items[$k] = $func($items[$k], $module, 'app.modules.' . $module . ".form");
                    }
                }

                $files[] = array(
                    'module' => $module,
                    'items' => $items
                );
            }
        }

        return $files;
    }

}
