<?php

class FormsController extends Controller {

    public static $modelField     = array();
    public static $modelFieldList = array();
    public static $relFieldList   = array(); // list of all fields in current model
    public $countRenderID         = 1;

    public function actionDelFolder($p) {
        $dir = Yii::getPathOfAlias($p);
        if (is_dir($dir)) {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
                $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
            }
            rmdir($dir);
        }
    }

    public function actionDelForm($p) {
        $file = Yii::getPathOfAlias($p) . ".php";
        if (is_file($file)) {
            @unlink($file);
        }
    }

    public function actionNewForm() {
        $model = new DevFormNewForm;
        $this->renderForm("DevFormNewForm", $model, ['prefix' => 'JS ERROR!'], [
            'layout' => '//layouts/blank'
        ]);
    }

    public function actionAddFolder($n, $p) {
        $dir = Yii::getPathOfAlias($p);
        if (is_dir($dir) && Helper::isValidVar($n)) {
            $dirname          = $dir . DIRECTORY_SEPARATOR . $n;
            $lastErrorHandler = set_error_handler(function ($errno, $errstr, $errfile, $errline ) {
                throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            });

            try {
                mkdir($dirname);
            } catch (ErrorException $ex) {
                echo json_encode([
                    "success" => false,
                    "error"   => $ex->getMessage() . " [$dirname]"
                ]);
                die();
            }

            echo json_encode([
                "success" => true,
                "id"      => time(),
                "name"    => $n,
                "alias"   => Helper::getAlias($dirname) . "."
            ]);
            set_error_handler($lastErrorHandler);
        } else {
            echo json_encode([
                "success" => false,
                "error"   => "ERROR: Folder name is not valid"
            ]);
        }
    }

    public function actionCode($c, $s = null) {
        $ext    = ".php";
        $script = null;

        if (isset($s)) {
            $file        = explode(".", $c);
            $scriptToken = explode(".", $s);
            $ext         = "." . array_pop($scriptToken);
            array_pop($file);
            $script      = implode(".", $file) . "." . implode(".", $scriptToken);
            $filePath    = Yii::getPathOfAlias(implode(".", $file)) . DIRECTORY_SEPARATOR . $s;
            if (!file_exists($filePath)) {
                file_put_contents($filePath, "");
                $content = "";
            } else {
                $content = file_get_contents($filePath);
            }
        } else {
            $content = file_get_contents(Yii::getPathOfAlias($c) . $ext);
        }

        Asset::registerJS('application.static.js.lib.ace');
        $this->renderForm('DevEditScript', null, [
            'content'   => $content,
            'mode'      => $ext == '.php' ? 'php' : 'javascript',
            'status'    => 'Ctrl+S to Save',
            'name'      => $c,
            'script'    => $script,
            'ext'       => $ext,
            'shortname' => isset($s) ? $s : Helper::explodeLast('.', $c)
                ], [
            'layout' => '//layouts/blank'
        ]);
    }

    public function actionCodeSave() {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);
        $filePath = Yii::getPathOfAlias($post['name']) . $post['ext'];

        if (is_file($filePath)) {
            file_put_contents($filePath, $post['content']);
        }
    }

    public function actionAddForm($c, $e, $p, $m) {
        if (@class_exists($e)) {
            $appFormsPath = Yii::getPathOfAlias('app.forms');
            if (!is_dir($appFormsPath)) {
                mkdir($appFormsPath, "0777", true);
            }

            $class = $this->prepareFormName($c, $m);
            if (Helper::isValidVar($c)) {
                $extends = $e;
                $dir     = Yii::getPathOfAlias($p);
                $path    = $dir . DIRECTORY_SEPARATOR . $class . ".php";
                if (!is_file($path)) {
                    $source = <<<EOF
<?php

class {$class} extends {$extends} {

}
EOF;
                    if (!is_dir(dirname($path))) {
                        mkdir(dirname($path), 0777, true);
                    }

                    file_put_contents($path, $source);

                    echo json_encode([
                        "success" => true,
                        "class"   => $class
                    ]);
                }
            } else {
                echo json_encode([
                    "success" => false,
                    "error"   => "ERROR: Class {$class} already exists"
                ]);
            }
        } else {
            echo json_encode([
                "success" => false,
                "error"   => "ERROR: Class {$e} not found!"
            ]);
        }
    }

    public function actionRenderProperties($class = null) {
        if ($class == null) {
            return true;
        }

        $a             = new $class;
        $field         = $a->attributes;
        $field['name'] = $class::$toolbarName;
        if (isset($array['label'])) {
            $field['label'] = $class::$toolbarName;
        }
        echo $this->renderPropertiesForm($field);
    }

    public function renderPropertiesForm($field) {
        FormField::$inEditor = false;
        $fbp                 = FormRenderer::load($field['type']);
        return '<script>var dv = ' . json_encode($field) . ';editor.activeTab.active = $.extend(dv, editor.activeTab.active); </script>' . $fbp->render($field, array(
                    'wrapForm'          => false,
                    'FormFieldRenderID' => $this->countRenderID++
        ));
    }

    public function actionRenderTemplate($class = null) {
        if ($class == null) {
            return true;
        }

        echo $class::template();
    }

    public function actionRenderBuilder($class, $layout) {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);
        if (isset($post['form'])) {
            $form = $post['form'];
        } else {
            $fb   = FormRenderer::load($class);
            $form = $fb->form;
        }

        $builder         = $this->renderPartial('form_builder', array(), true);
        $mainFormSection = Layout::getMainFormSection($form['layout']['data']);
        $data            = $form['layout']['data'];
        if ($layout != $form['layout']['name']) {
            unset($data[$mainFormSection]);
            $mainFormSection = Layout::defaultSection($layout);
        }

        $data['editor']                    = true;
        $data[$mainFormSection]['content'] = $builder;

        Layout::render($layout, $data);
    }

    public function actionRenderHiddenField() {
        $this->renderPartial('form_fields_hidden');
    }

    public function actionAsset($p) {
        echo "HAI";
    }

    public function actionFormList($m = '') {
        $list = FormRenderer::listFile();

        $return = [];
        if ($m == '') {
            foreach ($list as $k => $l) {
                array_push($return, [
                    'module' => $l['module'],
                    'count'  => $l['count'],
                    'alias'  => $l['alias'],
                    'items'  => [
                        [
                            'name'  => 'Loading...',
                            'items' => []
                        ]
                    ]
                ]);
            }
        } else {
            foreach ($list as $k => $l) {
                if ($m == $l['module']) {
                    $return = $l['items'];
                }
            }
        }

        echo json_encode($return);
    }

    public function actionIndex() {
        $toolbar = $this->renderAllToolbar();
        $this->render('index', array(
            'forms'       => array(),
            'toolbarData' => @$toolbar['data'],
        ));
    }

    public function actionGetFields($alias) {
        Yii::import($alias);
        $class   = Helper::explodeLast(".", $alias);
        $model   = new $class;
        $form    = [];
        $fields  = [];
        $props   = [];
        $rels    = [];
        $methods = [];
        $refl    = new ReflectionClass($model);
        foreach ($refl->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (strlen($method->name) > 3 && $method->class == $refl->getName()) {
                $dm = substr($method->name, 0, 3);
                if ($method->name != "getForm" && $method->name != "getFields" &&
                        ($dm == "get" || $dm == "set")) {
                    $methods[lcfirst(substr($method->name, 3))] = lcfirst(substr($method->name, 3));
                }
            }
        }

        if (is_subclass_of($model, 'ActiveRecord')) {
            $formType = "ActiveRecord";
            $props    = $class::model()->getAttributesList();
            $rel      = isset($props['Relations']) ? $props['Relations'] : array();
            $rels     = array_merge(array(
                ''             => '-- None --',
                '---'          => '---',
                'currentModel' => 'Current Model',
                '--'           => '---',
                    ), $rel);

            if (method_exists($model, 'getFields')) {
                $fields = $model->getFields();
            }
        } else if (is_subclass_of($model, 'Form')) {
            $formType = 'Form';
            if (method_exists($model, 'getFields')) {
                $fields = $model->getFields();
            }
        } else if (is_subclass_of($model, 'FormField')) {
            $formType = 'FormField';
            if (method_exists($model, 'getFieldProperties')) {
                $fields = $model->getFieldProperties();
            }
        }

        if (method_exists($model, 'getForm')) {
            $form = $model->getForm();
            if (is_subclass_of($model, 'ActiveRecord')) {
                $form['extendsFrom'] = get_parent_class($model);
            }
        }

        if (is_subclass_of($model, 'FormField') || is_subclass_of($model, 'Form')) {
            $props = $model->attributes;
            foreach ($model->attributes as $name => $field) {
                $props[$name] = $name;
            }
            unset($props['type']);
        }

        if (!empty($methods))
            $props['Dynamic Properties'] = $methods;

        echo json_encode([
            'fields'         => FormBuilder::expandFields($fields),
            'form'           => $form,
            'formType'       => $formType,
            'modelFieldList' => $props,
            'relFieldList'   => $rels,
            'modelList'      => ModelGenerator::listModels()
        ]);
    }

    public function actionEmpty() {
        $this->layout = "//layouts/blank";
        $this->render('empty');
    }

    public function actionSave($class, $timestamp) {
        $post   = file_get_contents("php://input");
        $post   = json_decode($post, true);
        $file   = Yii::getPathOfAlias($class) . ".php";
        $fields = $post['fields'];

        ini_set('xdebug.max_nesting_level', 3000);
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $stmts  = $parser->parse(file_get_contents($file));

        if (!empty($stmts)) {
            $getFields = new PhpParser\Node\Stmt\ClassMethod('getFields', [
                'type'       => PhpParser\Node\Stmt\Class_::MODIFIER_PUBLIC,
                'returnType' => null,
                'stmts'      => [
                    new PhpParser\Node\Stmt\Return_(
                            new PhpParser\Node\Expr\Array_($this->getFieldAST($fields, $parser))
                    )
                ],
            ]);


            foreach ($stmts[0]->stmts as $k => $s) {
                if ($s->name == "getFields") {
                    $stmts[0]->stmts[$k] = $getFields;
                }
            }

            $printer = new CodePrinter;
            $code    = $printer->save($stmts, $file);
        }
    }

    public function actionUpdate($class) {
        FormField::$inEditor = true;
        $isPHP               = Helper::explodeLast(".", $class);
        $class               = $isPHP == "php" ? substr($class, 0, -4) : $class;
        $class               = FormRenderer::classPath($class);
        $this->layout        = "//layouts/blank";

        ## load form builder class and session
        $fb = FormRenderer::load($class);
        $fb->resetTimestamp();
        $fb->updateExtendsFrom('Blog');

        $classPath = $class;
        $class     = Helper::explodeLast(".", $class);

        $methods = [];
        $refl    = new ReflectionClass($fb->model);
        foreach ($refl->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (strlen($method->name) > 3 && $method->class == $refl->getName()) {
                $dm = substr($method->name, 0, 3);
                if ($method->name != "getForm" && $method->name != "getFields" &&
                        ($dm == "get" || $dm == "set")) {
                    $methods[lcfirst(substr($method->name, 3))] = lcfirst(substr($method->name, 3));
                }
            }
        }

        if (is_subclass_of($fb->model, 'ActiveRecord')) {
            $formType = "ActiveRecord";

            $props                       = $class::model()->getAttributesList();
            if (!empty($methods))
                $props['Dynamic Properties'] = $methods;

            FormsController::setModelFieldList($props, "AR", $class);
        } else if (is_subclass_of($fb->model, 'FormField')) {
            $formType = "FormField";
            $mf       = new $class;

            $props                       = $mf->attributes;
            if (!empty($methods))
                $props['Dynamic Properties'] = $methods;

            FormsController::setModelFieldList($props, "FF");
        } else if (is_subclass_of($fb->model, 'Form')) {
            $formType = "Form";
            $mf       = new $class;

            $props                       = $mf->attributes;
            if (!empty($methods))
                $props['Dynamic Properties'] = $methods;

            FormsController::setModelFieldList($props, "FF");
        }

        $fieldData                   = $fb->fields;
        FormsController::$modelField = $fieldData;

        $toolbar = $this->renderAllToolbar($formType);
        Yii::import('application.modules.' . $fb->module . '.controllers.*');

        echo $this->render('form', array(
            'fb'          => $fb,
            'class'       => $class,
            'classPath'   => $classPath,
            'formType'    => $formType,
            'moduleName'  => Helper::explodeFirst(".", $classPath),
            'toolbarData' => @$toolbar['data'],
            'fieldData'   => $fieldData,
                ), true);
    }

    public static function setModelFieldList($data, $type = "AR", $class = "") {
        if (count(FormsController::$modelFieldList) == 0) {
            if ($type == "AR") {
                FormsController::$modelFieldList = $data;
                $rel                             = isset($data['Relations']) ? $data['Relations'] : array();

                FormsController::$relFieldList = array_merge(array(
                    ''             => '-- None --',
                    '---'          => '---',
                    'currentModel' => 'Current Model',
                    '--'           => '---',
                        ), $rel);
            } else {
                foreach ($data as $name => $field) {
                    FormsController::$modelFieldList[$name] = $name;
                }
                unset(FormsController::$modelFieldList['type']);
            }
        }
    }

    public function renderAllToolbar() {
        FormField::$inEditor = false;

        $toolbarData = FormField::allSorted();

        foreach ($toolbarData as $k => $f) {
            $ff      = new $f['type'];
            $scripts = array_merge($ff->renderScript(), $ff->renderEditorScript());

            foreach ($scripts as $script) {
                $ext = Helper::explodeLast(".", $script);
                if ($ext == "js") {
                    Yii::app()->clientScript->registerScriptFile($script, CClientScript::POS_END);
                } else if ($ext == "css") {
                    Yii::app()->clientScript->registerCSSFile($script);
                }
            }
        }

        FormField::$inEditor = true;

        return array(
            'data' => $toolbarData
        );
    }

}
