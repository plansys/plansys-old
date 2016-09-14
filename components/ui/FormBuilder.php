<?php

/**
 * Class FormBuilder
 * @author rizky
 */
class FormBuilder extends CComponent {

    const NEWLINE_MARKER = "!@#$%^&*NEWLINE&^%$#@!";

    private static $_buildRenderID = [];
    public $model                  = null;
    public $timestamp;
    public $fieldNameTemplate      = "";
    private $countRenderID         = 1;
    private $sourceFile            = '';
    private $originalClass         = '';
    private $findFieldCache        = null;
    private $crudGeneratorOptions  = [];
    private $methods               = [];
    private $file                  = [];

    public static function resetSession($class) {
        Yii::app()->session['FormBuilder_' . $class] = null;
    }

    /**
     * load
     * Fungsi ini digunakan untuk me-load FormBuilder
     * @param array $class
     * @param array $findByAttributes
     * @return mixed me-return null jika class tidak ada, jika ada maka me-return array $model
     */
    public static function load($class, $findByAttributes = []) {
        if (!is_string($class))
            return null;

        $originalClass = $class;
        if (strpos($class, ".") !== false) {
            $classFile = FormBuilder::classPath($class);
            $class     = Helper::explodeLast(".", $classFile);

            try {
                Yii::import($classFile);
            } catch (Exception $e) {
                if (isset(Yii::app()->controller) && isset(Yii::app()->controller->module)) {
                    $basePath  = Yii::app()->controller->module->basePath;
                    $classFile = str_replace(".", DIRECTORY_SEPARATOR, $classFile) . ".php";
                    $classFile = $basePath . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . $classFile;

                    require_once($classFile);
                }
            }

            if (!class_exists($class)) {
                throw new CException("Class \"{$class}\" does not exists");
            }
        }
        $model = new FormBuilder();

        if (!empty($findByAttributes) && method_exists($class, 'model')) {
            if (is_subclass_of($class, 'ActiveRecord')) {
                $model->model = $class::model()->findByAttributes($findByAttributes);

                if (is_null($model->model)) {
                    $model->model = new $class;
                }
            }
        } else {
            $model->model = new $class;
        }

        $model->originalClass = $originalClass;

        if (!is_null($findByAttributes)) {
            $model->model->attributes = $findByAttributes;
        }

        ## get method line and length
        if (isset(Yii::app()->session)) {
            if (is_null(Yii::app()->session['FormBuilder_' . $originalClass])) {
                $reflector         = new ReflectionClass($class);
                $model->sourceFile = $reflector->getFileName();
                $model->file       = file($model->sourceFile, FILE_IGNORE_NEW_LINES);
                $methods           = $reflector->getMethods();
                foreach ($methods as $m) {
                    if ($m->class == $class) {
                        $line                     = $m->getStartLine() - 1;
                        $length                   = $m->getEndLine() - $line;
                        $model->methods[$m->name] = [
                            'line'   => $line,
                            'length' => $length
                        ];
                    }
                }

                Yii::app()->session['FormBuilder_' . $originalClass] = [
                    'sourceFile' => $model->sourceFile,
                    'file'       => $model->file,
                    'methods'    => $model->methods
                ];
            } else {
                $s = Yii::app()->session['FormBuilder_' . $originalClass];

                $model->sourceFile = $s['sourceFile'];
                $model->file       = $s['file'];
                $model->methods    = $s['methods'];
                if (isset($s['timestamp'])) {
                    $model->timestamp = $s['timestamp'];
                }
            }
        }
        return $model;
    }

    public static function classPath($class) {
        $classArr  = explode(".", $class);
        $class     = "";
        $classFile = "";
        $prevC     = "";
        foreach ($classArr as $k => $c) {
            if ($k < count($classArr) - 1) {
                if ($c == "FormFields") {
                    $classFile .= $c . ".";
                } else {
                    if ($prevC == "modules") {
                        $classFile .= strtolower($c) . ".";
                    } else {
                        $classFile .= $c . ".";
                    }
                }
            } else {
                $classFile .= $c;
            }
            $prevC = $c;
        }


        return $classFile == "" ? $class : $classFile;
    }

    /**
     * build
     * Fungsi ini berfungsi untuk menentukan ID render dan merender-nya
     * @param string $class name
     * @param array $attributes
     * @return array me-return array field
     */
    public static function build($class, $attributes, $model = null, $return = false) {
        $field             = new $class;
        $field->attributes = $attributes;
        
        if (!is_null($model)) {
            $fb             = new FormBuilder();
            $fb->model      = $model;
            $field->builder = $fb;
        }

        ## make sure there is no duplicate renderID
        do {
            $renderID = rand(0, 1000000);
        } while (in_array($renderID, FormBuilder::$_buildRenderID));
        FormBuilder::$_buildRenderID[] = $renderID;

        $field->renderID = $renderID;
        if ($return) {
            return $field;
        } else {
            return $field->render();
        }
    }

    public static function renderUI($class, $attributes, $js = []) {

        $init = "";
        if (isset($js['init'])) {
            $init = 'ng-init="' . $js['init'] . '"';
        }

        $load = "";
        if (isset($js['load'])) {
            if (!isset($attributes['options'])) {
                $attributes['options'] = [];
            }

            $attributes['options']['ng-init'] = $js['load'];
        }

        
        $field = self::build($class, $attributes, null, true);
        $files = $field->renderScript();
        $html  = $field->render();
        $files = json_encode($files);

        echo <<<html
<div {$init}>
    <div oc-lazy-load='{name:"main", files:{$files}}' >
        <div ng-include-fill-content>
            {$html}
        </div>
    </div>
</div>
html;
    }

    /**
     * @param string $module
     * @return array Fungsi ini akan me-return sebuah array list controller .
     */
    public static function listController($module) {
        $ctr_dir = Yii::getPathOfAlias("application.modules.{$module}.controllers") . DIRECTORY_SEPARATOR;
        $items   = glob($ctr_dir . "*.php");
        $list    = [];

        foreach ($items as $k => $f) {
            $f                 = str_replace($ctr_dir, "", $f);
            $f                 = str_replace('Controller.php', "", $f);
            $list[lcfirst($f)] = lcfirst($f);
        }

        return $list;
    }

    /**
     * @param string $module
     * @return array Fungsi ini akan me-return sebuah array list form .
     */
    public static function listForm($module = null, $useAlias = true, $excludeModule = true, $excludeExtension = true) {
        $list     = [];
        $list[''] = '-- NONE --';
        $modules  = FormBuilder::listFile(false, $excludeExtension);
        foreach ($modules as $m) {
            if (!is_null($module) && strtolower($m['module']) != strtolower($module))
                continue;

            if ($excludeModule !== true) {
                if (in_array($m['module'], $excludeModule))
                    continue;
            }

            $list[$m['module']] = [];
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

    /**
     * @param string $dir
     * @param string $func
     * @return array me-return sebuah array list file .
     */
    public static function listFile($formatRecursive = true, $excludeExtension = true) {
        $files = [];

        $devMode = Setting::get('app.mode') === "plansys";

        $func = function ($m, $module = "", $aliaspath = "", $path) {
            $m = str_replace(".php", "", $m);

            return [
                'name'   => str_replace($module, '', $m),
                'class'  => $m,
                'module' => $module,
                'alias'  => $aliaspath . "." . $m,
                'items'  => []
            ];
        };

        if ($devMode) {
            ## add files in FormFields Dir
            $forms_dir = Yii::getPathOfAlias("application.components.ui.FormFields") . DIRECTORY_SEPARATOR;
            $items     = glob($forms_dir . "*.php");
            $count     = 0;
            foreach ($items as $k => $f) {
                $items[$k] = str_replace($forms_dir, "", $f);
                $items[$k] = str_replace('*.php', "", $items[$k]);
                if (!is_null($func)) {
                    $items[$k] = $func($items[$k], "", "application.components.ui.FormFields", $f);
                    $count++;
                }
            }
            $files[] = [
                'module' => 'Plansys: fields',
                'items'  => $items,
                'alias'  => "application.components.ui.FormFields",
                'count'  => $count
            ];

            ## add files in Root Form dir
            $forms_dir = Yii::getPathOfAlias("application.forms") . DIRECTORY_SEPARATOR;
            $glob      = Helper::globRecursive($forms_dir . "*.php", 0, true);
            $items     = $glob['files'];
            $items     = FormBuilder::formatGlob($items, $forms_dir, '', $func, 'application.forms', $formatRecursive);
            $files[]   = [
                'module' => 'Plansys: forms',
                'alias'  => "application.forms",
                'count'  => $glob['count'],
                'items'  => $items
            ];


            ## add files in Plansys Modules Dir
            $module_dir = Yii::getPathOfAlias('application.modules');
            if (file_exists($module_dir)) {
                $modules = glob($module_dir . DIRECTORY_SEPARATOR . "*");

                foreach ($modules as $m) {
                    $module   = str_replace($module_dir . DIRECTORY_SEPARATOR, '', $m);
                    $alias    = "application.modules.{$module}.forms.";
                    $item_dir = $m . DIRECTORY_SEPARATOR . "forms" . DIRECTORY_SEPARATOR;
                    $glob     = Helper::globRecursive($item_dir . "*", 0, true);

                    $items = $glob['files'];
                    $items = FormBuilder::formatGlob($items, $item_dir, $module, $func, $alias, $formatRecursive);

                    if (count($items) > 0) {
                        $files[] = [
                            'module' => 'Plansys: ' . $module,
                            'alias'  => $alias,
                            'items'  => $items,
                            'count'  => $glob['count']
                        ];
                    }
                }
            }
        }

        ##  add files in Root Form dir
        $forms_dir = Yii::getPathOfAlias("app.forms") . DIRECTORY_SEPARATOR;
        $glob      = Helper::globRecursive($forms_dir . "*.php", 0, true);
        $items     = $glob['files'];
        $items     = FormBuilder::formatGlob($items, $forms_dir, '', $func, 'app.forms', $formatRecursive);
        $files[]   = [
            'module' => 'app',
            'alias'  => 'app.forms',
            'items'  => $items,
            'count'  => $glob['count']
        ];

        ## add files in App Modules Dir
        $module_dir = Yii::getPathOfAlias('app.modules');
        if (file_exists($module_dir)) {
            $modules = glob($module_dir . DIRECTORY_SEPARATOR . "*");
            foreach ($modules as $m) {
                $module   = str_replace($module_dir . DIRECTORY_SEPARATOR, '', $m);
                $alias    = "app.modules.{$module}.forms.";
                $item_dir = $m . DIRECTORY_SEPARATOR . "forms" . DIRECTORY_SEPARATOR;
                $glob     = Helper::globRecursive($item_dir . "*.php", 0, true);
                $items    = $glob['files'];
                $items    = FormBuilder::formatGlob($items, $item_dir, $module, $func, $alias, $formatRecursive);

                $files[] = [
                    'module' => $module,
                    'alias'  => $alias,
                    'items'  => $items,
                    'count'  => $glob['count']
                ];
            }
        }

        return $files;
    }

    private static function formatGlob($items, $item_dir, $module, $func, $alias, $format = true, $return = []) {
        $subdir_val = [];
        $id         = 0;
        foreach ($items as $k => $i) {
            $item = [];
            $i    = realpath($i);
            if (substr($i, strlen($i) - 4) != ".php") {
                $file_dir = $i . DIRECTORY_SEPARATOR;
                if (is_dir($file_dir)) {
                    $is_dir = true;
                } else {
                    continue;
                }
            } else {
                $file_dir = dirname($i) . DIRECTORY_SEPARATOR;
                $is_dir   = false;
            }
            $subdir = trim(str_replace(DIRECTORY_SEPARATOR, '.', str_replace($item_dir, '', $file_dir)), '.');

            $item              = str_replace($file_dir, "", $i);
            $item              = str_replace('.php', "", $item);
            $newAlias          = trim(trim($alias, '.') . '.' . $subdir, '.');
            $item              = $func($item, $module, $newAlias, $i);
            $item['id']        = $id++;
            $item['shortName'] = substr($item['name'], strlen($module));

            $parentSubDir = explode(".", $subdir);
            foreach ($parentSubDir as $kp => $p) {
                if (stripos($item['shortName'], $p) === 0) {
                    $item['shortName'] = substr($item['shortName'], strlen($p));
                }
            }

            if ($subdir == '' || !$format) {
                if (!$is_dir) {
                    $return[$k] = $item;
                }
            } else {
                $subarr  = explode(".", $subdir);
                $curpath = &$return;
                foreach ($subarr as $ks => $s) {
                    $id++;
                    if (!isset($curpath[$s])) {
                        $shortName = $s;
                        if ($module != '' && strpos($s, $module) === 0) {
                            $shortName = substr($s, strlen($module));
                        }

                        $curpath[$s] = [
                            'items'     => [],
                            'alias'     => $newAlias,
                            'module'    => $module,
                            'name'      => $s,
                            'shortName' => $shortName,
                            'id'        => $id++
                        ];
                    }
                    $curpath = &$curpath[$s]['items'];
                }

                if (!isset($subdir_val[$subdir]))
                    $subdir_val[$subdir] = [];

                if (!$is_dir) {
                    $subdir_val[$subdir][] = $item;
                }
                $curpath = $subdir_val[$subdir];
            }
        }

        $return = Helper::arrayValuesRecursive($return);
        return $return;
    }

    public function resetTimestamp() {
        $this->timestamp = time();
        if (isset(Yii::app()->session['FormBuilder_' . $this->originalClass])) {
            $session              = Yii::app()->session['FormBuilder_' . $this->originalClass];
            $session['timestamp'] = $this->timestamp;

            Yii::app()->session['FormBuilder_' . $this->originalClass] = $session;
        }
    }

    public function findAllField($attributes, $recursive = null, $results = []) {
        if (is_null($this->findFieldCache)) {
            ## cache the fields
            $class     = get_class($this->model);
            $reflector = new ReflectionClass($class);

            $functionName = 'getFields';
            if (is_subclass_of($this->model, 'FormField')) {
                $functionName = 'getFieldProperties';
            }

            if (!$reflector->hasMethod($functionName)) {
                $this->model = new $class;
                $fields      = $this->model->getDefaultFields();
            } else {
                $fields = $this->model->$functionName();
            }

            $this->findFieldCache = $this->parseFields($fields);
        }

        if (is_null($recursive)) {
            $fields = $this->findFieldCache;
        } else {
            $fields = $recursive;
        }

        foreach ($fields as $k => $f) {
            if (!is_array($f))
                continue;

            $valid = 0;
            foreach ($f as $key => $value) {
                if (isset($f['name'])) {
                    foreach ($attributes as $attrKey => $attrVal) {
                        if ($key == $attrKey && $value == $attrVal) {
                            $valid++;
                        }
                    }
                }
            }

            if ($valid == count($attributes)) {
                $results[] = $f;
            }

            if (isset($f['parseField']) && count($f['parseField']) > 0) {
                foreach ($f['parseField'] as $i => $j) {
                    $results = $this->findAllField($attributes, $f[$i], $results);
                }
            }
        }

        return $results;
    }

    /**
     * Fungsi ini akan mem-format, membenahi, dan mengevaluasi setiap field yang ada di dalam $fields
     *
     * @param mixed $fields parameter dapat berupa array atau jika bukan array maka akan diubah menjadi array pada prosesnya
     * @return array me-return sebuah array fields hasil parseFields
     */
    public function parseFields($fields) {
        $processed = [];
        if (!is_array($fields))
            return $processed;

        foreach ($fields as $k => $f) {
            if (is_array($f)) {
                $field = new $f['type'];

                $f             = $this->stripSlashesRecursive($f);
                $processed[$k] = array_merge($field->attributes, $f);

                if (count($field->parseField) > 0) {
                    foreach ($field->parseField as $i => $j) {
                        if (!isset($fields[$k][$i]))
                            continue;

                        $processed[$k][$i] = $this->parseFields($fields[$k][$i]);
                    }
                }
            } else {
                $value         = $f;
                $processed[$k] = [
                    'type'  => 'Text',
                    'value' => str_replace("\'", "'", $value)
                ];
            }
        }
        return $processed;
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

    public function updateField($findAttr, $values, &$fields = null, $level = 0) {
        throw new Exception("WOW");

        if ($fields == null) {
            $fields = $this->getFields();
        }

        foreach ($fields as $k => $f) {
            if (!is_array($f))
                continue;

            $valid = 0;
            foreach ($f as $key => $value) {
                if (isset($f['name'])) {
                    if (!is_array($value)) {
                        foreach ($findAttr as $attrKey => $attrVal) {
                            if ($key == $attrKey && $value == $attrVal) {
                                $valid++;
                            }
                        }
                    } else if (is_array($value) && count($value) > 0) {
                        $fields[$k][$key] = $this->updatefield($findAttr, $values, $value, $level);
                    }
                }
            }
            if ($valid == count($findAttr)) {
                foreach ($values as $vk => $val) {
                    $fields[$k][$vk] = $val;
                }
            }
        }

        return $fields;
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
        $class     = get_class($this->model);
        $reflector = new ReflectionClass($class);

        $functionName = 'getFields';
        if (is_subclass_of($this->model, 'FormField')) {
            $functionName = 'getFieldProperties';
        }

        if (!$reflector->hasMethod($functionName)) {
            $this->model = new $class;
            $this->updateExtendsFrom($reflector->getParentClass()->getName());
            if (is_subclass_of($this->model, 'FormField')) {
                $fields       = [];
                $this->fields = [];
            } else {

                if (property_exists($this->model, 'generatorOptions')) {
                    $options = json_decode($this->model->generatorOptions, true);
                    $fields  = $this->model->getDefaultFields($options);
                } else {
                    $fields = $this->model->getDefaultFields();
                }
                $this->fields = $fields;
            }
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

    public function updateExtendsFrom($extendsFrom) {
        $class = get_class($this->model);
        foreach ($this->file as $k => $f) {
            if (strpos(trim($f), 'class ' . $class) === 0) {
                $this->file[$k] = "class {$class} extends {$extendsFrom} {";
            }
        }

        return true;
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
                $columnField         = new ColumnField;
                $defaultTotalColumns = $columnField->totalColumns;
                $totalColumns        = (isset($f['totalColumns']) ? $f['totalColumns'] : $defaultTotalColumns);
                for ($i = 1; $i <= $totalColumns; $i++) {
                    if (!isset($f['column' . $i])) {
                        continue;
                    }

                    $fields[$k]['column' . $i] = $this->processFieldExpr($f['column' . $i]);
                }
            } else if (method_exists($class, 'processExpr')) {
                $field             = new $class;
                $field->attributes = $f;
                $field->builder    = $this;
                ob_start();
                $processedField    = $field->processExpr();
                $error             = ob_get_clean();

                if ($error == "") {
                    $fields[$k] = array_merge($f, $processedField);
                }
            }
        }

        return $fields;
    }

    public function findField($attributes, $recursive = null) {
        if (is_null($this->findFieldCache)) {
            ## cache the fields
            $class     = get_class($this->model);
            $reflector = new ReflectionClass($class);

            $functionName = 'getFields';
            if (is_subclass_of($this->model, 'FormField')) {
                $functionName = 'getFieldProperties';
            }

            if (!$reflector->hasMethod($functionName)) {
                $this->model = new $class;
                $fields      = $this->model->defaultFields;
            } else {
                $fields = $this->model->$functionName();
            }

            $this->findFieldCache = $this->parseFields($fields);
        }

        if (is_null($recursive)) {
            $fields = $this->findFieldCache;
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
     * @return array me-return array module.
     */
    public function getModule() {
        $class     = get_class($this->model);
        $reflector = new ReflectionClass($class);
        $f         = $reflector->getFileName();
        $dir       = Yii::getPathOfAlias('application.modules');
        $f         = str_replace($dir . DIRECTORY_SEPARATOR, "", $f);
        $f         = explode(DIRECTORY_SEPARATOR, $f);
        return $f[0];
    }

    /**
     * @param array $fields
     */
    public function setFields($fields) {
        $multiline = [];
        $this->tidyRecursive($fields, $multiline);

        if (is_subclass_of($this->model, 'FormField')) {
            return $this->updateFunctionBody('getFieldProperties', $fields, "", $multiline);
        } else {
            return $this->updateFunctionBody('getFields', $fields, "", $multiline);
        }
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
            $defaultAttributes = [];
        }

        foreach ($data as $i => $j) {
            if ($i == 'type' || $i == 'fields')
                continue;

            if (is_string($j)) {
                $j        = addslashes($j);
                $data[$i] = $j;
            }

            if (is_array($preserveMultiline) && is_string($j)) {
                if (strpos($j, "\n") !== FALSE || strpos($j, PHP_EOL) !== FALSE) {
                    $hash                     = '---' . sha1($j) . '---';
                    $preserveMultiline[$hash] = $j;
                    $data[$i]                 = $hash;
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
            $class               = get_class($this->model);
        } else {
            $using_another_class = true;
        }

        ## if current model is an ActiveRecord Model, then do not write anything!
        if (isset($this->model)) {
            $alias = Helper::getAlias($this->model);
            if (strpos($alias, 'app.models') === 0) {
                return false;
            }
        }

        ## get class data
        $isNewFunc  = false;
        $sourceFile = '';
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
        $this->methods[$functionName]['line']   = $line;

        $this->file = $file;
        $fp         = @fopen($sourceFile, 'r+');
        if (!$fp) {
            return false;
        }

        ## write new function to sourceFile
        if (flock($fp, LOCK_EX)) { // acquire an exclusive lock
            ftruncate($fp, 0); // truncate file
            $buffer = implode("\n", $file);
            //TODO: fix gigantic bug, do not allow more than 200 consecutive spaces
            $buffer = preg_replace('/\s{200,}/', ' ', $buffer);
            $buffer = str_replace('\\\\\'', '\'', $buffer);
            $file   = explode("\n", $buffer);

            fwrite($fp, $buffer);
            fflush($fp); // flush output before releasing the lock
            flock($fp, LOCK_UN); // release the lock

            Yii::app()->session['FormBuilder_' . $this->originalClass] = [
                'sourceFile' => $this->sourceFile,
                'file'       => $file,
                'methods'    => $this->methods,
                'timestamp'  => $this->timestamp
            ];

            return true;
        } else {
            echo "ERROR: Couldn't lock source file '{$sourceFile}'!";
            die();
        }

        if (!$using_another_class) {
            ## update model instance
            $this->model = new $class;
        }
    }

    private function getLineOfClass($class, $name) {
        $isNewFunc = false;
        ## get first line of the class
        if (!isset($this->methods[$name])) {
            $line      = $this->prepareLineForMethod();
            $length    = 0;
            $isNewFunc = true;
        } else {
            $line    = $this->methods[$name]['line'];
            $length  = $this->methods[$name]['length'];
            $endline = $line + $length;

            ## when last line is like "}}" then separate it to new line

            if (@$this->file[$endline - 1]) {
                $lastline = trim($this->file[$endline - 1]);
                if (substr($lastline, -2) == "}}") {
                    $lastline[strlen($lastline) - 1] = " ";
                    $this->file[$endline - 1]        = $lastline;
                    $this->file[]                    = "\n";
                    $this->file[]                    = "}";
                }
            }
        }
        return [
            'file'       => $this->file,
            'length'     => $length,
            'line'       => $line,
            'sourceFile' => $this->sourceFile,
            'isNewFunc'  => $isNewFunc
        ];
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

    protected function prepareLineForProperty() {
        ## get first line of the class
        $reflector = new ReflectionClass($this->model);
        $line      = $reflector->getStartLine();

        ## when last line is like "{}" then separate it to new line
        $lastline = trim($this->file[count($this->file) - 1]);

        if (substr($lastline, 0, 5) == "class" && substr($lastline, -1) == "}") {
            $lastline[strlen($lastline) - 1] = " ";
            $this->file[$line - 1]           = $lastline;
            $this->file[]                    = "";
            $this->file[]                    = "}";
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
        $replace = [
            "  "    => '    ',
            "=> \n" => "=>"
        ];
        $fields  = str_replace(array_keys($replace), $replace, $fields);
        $replace = [
            "=>        array (" => '=> array (',
        ];
        $fields  = str_replace(array_keys($replace), $replace, $fields);
        $fields  = explode("\n", $fields);

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
     * @return array me-return array atribut form.
     */
    public function getForm() {
        ## if form class does not have getFields method, then create it
        $class     = get_class($this->model);
        $reflector = new ReflectionClass($class);

        if (!$reflector->hasMethod('getForm')) {
            $this->model = new $class;

            $basic = explode("_", Helper::camelToUnderscore($class));
            array_shift($basic);
            $basic = implode(" ", array_map("ucfirst", $basic));
            if (substr($class, -5) == "Index") {
                $title = "Daftar " . substr($basic, 0, strlen($basic) - 5);
            } else if (substr($class, -4) == "Form") {
                $title = "Detail " . substr($basic, 0, strlen($basic) - 4);
            } else {
                $title = $basic;
            }

            $defaultFields = [
                'title'       => $title,
                'extendsFrom' => get_parent_class($this->model),
                'layout'      => [
                    'name' => 'full-width',
                    'data' => [
                        'col1' => [
                            'type' => 'mainform'
                        ]
                    ]
                ],
            ];

            if (empty($this->getFunctionBody($reflector->getFileName(), 'getForm'))) {
                if (isset($_SESSION['CrudGenerator']) && isset($_SESSION['CrudGenerator'][get_class($this->model)])) {
                    if (isset($_SESSION['CrudGenerator'][get_class($this->model)]['inlineJs'])) {
                        $defaultFields['inlineJS'] = $_SESSION['CrudGenerator'][get_class($this->model)]['inlineJs'];
                    }
                }

                $this->form = $defaultFields;
            }

            $functionName = 'getFields';
            if (is_subclass_of($this->model, 'FormField')) {
                $functionName = 'getFieldProperties';
            }

            return $defaultFields;
        }

        $form                = $this->model->form;
        $form['extendsFrom'] = get_parent_class($this->model);

        return $form;
    }

    /**
     * @param array $sourceFile
     * @param string $functionName
     * @return array me-return sebuah array function.
     */
    public function getFunctionBody($sourceFile, $functionName) {
        $fd  = fopen($sourceFile, "r");
        $ret = [];
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

        if (isset($form['extendsFrom'])) {
            if (get_parent_class($this->model) != $form['extendsFrom']) {
                if (class_exists($form['extendsFrom'])) {
                    $this->updateExtendsFrom($form['extendsFrom']);
                }
            }

            unset($form['extendsFrom']);
        }

        return $this->updateFunctionBody('getForm', $form);
    }

    /**
     * @return array me-return array
     */
    public function registerScript() {
        $modelClass = get_class($this->model);
        $id         = "NGCTRL_{$modelClass}_" . rand(0, 1000);
        Yii::app()->clientScript->registerScript($id, $this->renderAngularController(), CClientScript::POS_END);
        return $this->registerScriptInternal($this, $this->fields);
    }

    /**
     * @param array $formdata
     * @return string me-return string script yang di-include
     */
    public function renderAngularController($formdata = null, $renderParams = []) {
        $modelClass     = get_class($this->model);
        $modelClassPath = Helper::getAlias($this->model);

        ## define formdata
        if (is_array($formdata)) {
            $data = $formdata;
        } else {
            $data = $this->defineFormData($formdata);
        }

        $reflector    = new ReflectionClass($this->model);
        $inlineJSPath = dirname($reflector->getFileName()) . DIRECTORY_SEPARATOR . @$this->form['inlineJS'];
        if (isset($this->form['inlineJS']) && is_file($inlineJSPath)) {
            $tab      = '            ';
            $inlineJS = file($inlineJSPath);
            $inlineJS = $tab . implode($tab, $inlineJS);
        } else {
            $inlineJS = '';
        }

        $script = include("FormBuilder.js.php");
        return $script;
    }

    private function defineFormData($formdata) {
        $data = [];
        if (is_array($formdata)) {
            $data['data'] = $formdata;
        } else if (is_subclass_of($formdata, 'Form')) {
            $data['data']   = $formdata->attributes;
            $data['errors'] = $formdata->errors;
        } else if (is_subclass_of($formdata, 'ActiveRecord')) {
            $this->model         = $formdata;
            $data['data']        = $formdata->attributes;
            $data['errors']      = $formdata->errors;
            $data['isNewRecord'] = $formdata->isNewRecord;
        }

        return $data;
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
    public function renderScriptInternal($fb, $fields, $html = []) {
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
     * render
     * Fungsi ini untuk me-render Form Builder sesuai dengan $formdata
     * @param array $formdata
     * @param array $options
     * @return string me-return string tag html hasil generate dari fungsi ini.
     */
    public function render($formdata = null, $options = []) {
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
    private function renderInternal($formdata = null, $options = [], $fb, $fields) {
        $html = "";


        $form       = $fb->form;
        $moduleName = $fb->module;
        $modelClass = get_class($fb->model);

        ## setup default options
        $wrapForm          = isset($options['wrapForm']) ? $options['wrapForm'] : true;
        $action            = isset($options['action']) ? $options['action'] : 'create';
        $renderWithAngular = isset($options['renderWithAngular']) ? $options['renderWithAngular'] : true;
        $renderInAjax      = isset($options['renderInAjax']) ? $options['renderInAjax'] : true;
        $FFRenderID        = isset($options['FormFieldRenderID']) ? $options['FormFieldRenderID'] . '_' : '';
        $renderParams      = isset($options['params']) ? $options['params'] : [];

        ## wrap form
        if ($wrapForm) {
            $url    = "#";
            $ngctrl = $renderWithAngular ? 'ng-controller="' . $modelClass . 'Controller"' : '';

            $formOptions     = (is_array(@$form['options']) ? @$form['options'] : []);
            $formDefaultAttr = [
                'action' => $url,
                'method' => 'POST',
                'class'  => 'form-horizontal ' . @$formOptions['class'],
                'role'   => 'form',
            ];

            $formAttr = array_merge($formOptions, $formDefaultAttr);
            $formAttr = Helper::expandAttributes($formAttr);

            ## Add flash and error message to html
            $html = <<<HTML
<div style='opacity:0' {$ngctrl}>
<form {$formAttr}>
HTML;
            if (@$formOptions['customErrorPlacement'] != "true") {
                $html .= '<div ng-include="\'flash_message\'"></div>';
            }
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
                        $o             = $options;
                        $o['wrapForm'] = false;
                        $field->$j     = $this->renderInternal($data, $o, $fb, $f[$i]);
                    }
                }

                $field->formProperties = $form;
                $field->renderParams   = $renderParams;

                ## assign field render id
                $field->renderID          = $modelClass . '_' . $FFRenderID . $this->countRenderID++;
                $field->fieldNameTemplate = $this->fieldNameTemplate;

                ## then render the field, (including registering script)
                $html .= $field->render();
            } else {
                $html .= $f;
            }
        }


        ## include Yii CSRF Token
        $csrfName = Yii::app()->request->csrfTokenName;
        $csrf     = Yii::app()->request->csrfToken;
        $html .= "<input type='hidden' name='{$csrfName}' value='{$csrf}' />";

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
                    <?php echo $this->renderAngularController($data, $renderParams); ?>
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
                    $id      = "NGCTRL_{$modelClass}_" . rand(0, 1000);
                    $angular = $this->renderAngularController($data, $renderParams);
                    Yii::app()->clientScript->registerScript($id, $angular, CClientScript::POS_END);
                    $this->renderAdditionalJS();
                }
            }
        }

        $flashMsg = <<<HTML
    <script type='text/ng-template' id='flash_message' style='display:none;'>
        <div ng-show='!!flash' ng-if='!!flash' 
             class='flash-container alert alert-success text-center'>
            <div href='#' class='close' ng-click='flash = false' 
            aria-label='close' title='close'>&times;</div>
            {{flash}}
        </div>
        <div class='error-container alert alert-danger' 
             style='margin:0px' ng-if='objectSize(errors) > 0'>
             <ul ng-repeat='(fieldName,errorList) in errors' ng-if="angular.isString(errorList[0])">
                <li ng-repeat='error in errorList'
                    style='white-space:pre-wrap;' ng-bind-html='error'></li>
             </ul>
        </div>
    </script>
HTML;

        return $html . $flashMsg;
    }

    /**
     * @param boolean $isAjax
     */
    public function renderAdditionalJS($isAjax = false) {
        $reflector = new ReflectionClass($this->model);
        $formDir   = dirname($reflector->getFileName()) . DIRECTORY_SEPARATOR;

        if (count(@$this->form['includeJS']) > 0):
            foreach ($this->form['includeJS'] as $script):
                $src = $flashMsg . $script;
                if (is_file($src)) {
                    $scriptUrl = Asset::publish($src);

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

}
