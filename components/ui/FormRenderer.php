<?php

class FormRenderer extends CComponent {

    private static $_buildRenderID = [];
    public $model                  = null;
    public $timestamp              = null;
    public $fieldNameTemplate      = "";
    private $countRenderID         = 1;
    private $findFieldCache        = null;

    public static function load($class, $findByAttributes = []) {
        if (!is_string($class))
            return null;

        if (strpos($class, ".") !== false) {
            $classFile = FormRenderer::classPath($class);
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

        $model = new FormRenderer();

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

        if (!is_null($findByAttributes)) {
            $model->model->attributes = $findByAttributes;
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
    public static function build($class, $attributes, $model = null) {
        $field             = new $class;
        $field->attributes = $attributes;

        if (!is_null($model)) {
            $fb             = new FormRenderer();
            $fb->model      = $model;
            $field->builder = $fb;
        }

        ## make sure there is no duplicate renderID
        do {
            $renderID = rand(0, 1000000);
        } while (in_array($renderID, FormRenderer::$_buildRenderID));
        FormRenderer::$_buildRenderID[] = $renderID;

        $field->renderID = $renderID;
        return $field->render();
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
        $modules  = FormRenderer::listFile(false, $excludeExtension);
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
            $items     = FormRenderer::formatGlob($items, $forms_dir, '', $func, 'application.forms', $formatRecursive);
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
                    $items = FormRenderer::formatGlob($items, $item_dir, $module, $func, $alias, $formatRecursive);

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
        $items     = FormRenderer::formatGlob($items, $forms_dir, '', $func, 'app.forms', $formatRecursive);
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
                $items    = FormRenderer::formatGlob($items, $item_dir, $module, $func, $alias, $formatRecursive);

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

    private function getFieldsInternal($processExpr = true) {
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

    public function getModule() {
        $class     = get_class($this->model);
        $reflector = new ReflectionClass($class);
        $f         = $reflector->getFileName();
        $dir       = Yii::getPathOfAlias('application.modules');
        $f         = str_replace($dir . DIRECTORY_SEPARATOR, "", $f);
        $f         = explode(DIRECTORY_SEPARATOR, $f);
        return $f[0];
    }

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

        $script = include("FormRenderer.js.php");
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
