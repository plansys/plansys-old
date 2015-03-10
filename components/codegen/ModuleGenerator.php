<?php

class ModuleGenerator extends CodeGenerator {

    protected $basePath = "app.modules";
    protected $baseClass = "WebModule";
    public $defaultRule = '';
    public $accessType = '';
    public $rolesRule = [];
    public $usersRule = [];

    public static function listModuleForMenuTree() {
        $list = [];
        $devMode = Setting::get('app.mode') === "plansys";
        if ($devMode) {
            $dir = Yii::getPathOfAlias("application.modules") . DIRECTORY_SEPARATOR;
            $items = glob($dir . "*", GLOB_ONLYDIR);
            $plansysList = [];
            foreach ($items as $k => $f) {
                $label = str_replace($dir, "", $f);
                $classPath = $f . DIRECTORY_SEPARATOR . ucfirst($label) . 'Module.php';
                if (is_file($classPath)) {
                    $plansysList[$label] = [
                        'label'  => $label,
                        'module' => 'plansys',
                        'icon'   => 'fa-empire',
                        'active' => @$_GET['active'] == 'plansys.' . $label,
                        'url'    => Yii::app()->controller->createUrl('/dev/genModule/index', [
                            'active' => 'plansys.' . $label
                        ])
                    ];
                }
            }

            $list[] = [
                'label'  => 'Plansys',
                'module' => 'plansys',
                'items'  => $plansysList
            ];
        }

        $dir = Yii::getPathOfAlias("app.modules") . DIRECTORY_SEPARATOR;
        $items = glob($dir . "*", GLOB_ONLYDIR);
        $appList = [];
        foreach ($items as $k => $f) {
            $label = str_replace($dir, "", $f);
            $classPath = $f . DIRECTORY_SEPARATOR . ucfirst($label) . 'Module.php';
            if (is_file($classPath)) {

                $appList[$label] = [
                    'label'  => $label,
                    'module' => 'app',
                    'icon'   => 'fa-empire',
                    'active' => @$_GET['active'] == 'app.' . $label,
                    'url'    => Yii::app()->controller->createUrl('/dev/genModule/index', [
                        'active' => 'app.' . $label
                    ])
                ];
            }
        }

        $list[] = [
            'label'  => 'App',
            'module' => 'app',
            'items'  => $appList
        ];
        return $list;
    }

    public static function create($classAlias) {
        $module = ModuleGenerator::init($classAlias);
    }

    public static function init($classAlias, $mode = 'load') {
        $m = new ModuleGenerator;
        $path = explode('.', $classAlias);
        $class = ucfirst(array_pop($path));

        $path[count($path) - 1] = lcfirst($path[count($path) - 1]);
        $m->basePath = implode(".", $path);

        if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $class)) {
            $m->load($class);

            if ($mode == 'create') {
                if (!is_dir($m->baseDir . DIRECTORY_SEPARATOR . 'controllers')) {
                    mkdir($m->baseDir . DIRECTORY_SEPARATOR . 'controllers');
                }

                if (!is_dir($m->baseDir . DIRECTORY_SEPARATOR . 'forms')) {
                    mkdir($m->baseDir . DIRECTORY_SEPARATOR . 'forms');
                }

                $m->generateImport(true);
            }
            $m->loadAccessControl();
            return $m;
        } else {
            return null;
        }
    }

    private function expandAccessControlArray($array, $key) {
        $prepared = [
            'deny'  => [],
            'allow' => []
        ];

        $inserted = [];

        foreach ($array as $a) {
            if (!isset($a['access']) || !isset($a[$key]) || $a[$key] == '' || in_array($a[$key], $inserted))
                continue;

            $access = strtolower($a['access']);

            if (!in_array($a[$key], $prepared[$access])) {
                $prepared[$access][] = $a[$key];
            }
            $inserted[] = $a[$key];
        }

        $prepared['allow'] = array_filter($prepared['allow']);
        $prepared['deny'] = array_filter($prepared['deny']);
        return $prepared;
    }

    private function flattenAccessControlArray($array, $key) {
        $result = [];
        foreach ($array['allow'] as $d) {
            $result[] = [
                $key . "" => $d,
                'access'  => 'allow'
            ];
        }
        foreach ($array['deny'] as $d) {
            $result[] = [
                $key . "" => $d,
                'access'  => 'deny'
            ];
        }
        return $result;
    }

    public function loadAccessControl() {
        $startLine = false;
        $lineLength = false;
        $func = $this->getFunctionBody('beforeControllerAction');
        foreach ($func as $k => $f) {
            $tf = trim($f);
            if (!$startLine) {
                if (substr($tf, 0, 10) == substr(ModuleGenerator::GEN_COMMENT_START, 0, 10)) {
                    $startLine = $k + 1;
                }
            } else if ($startLine && !$lineLength) {
                if (substr($tf, 0, 10) == substr(ModuleGenerator::GEN_COMMENT_END, 0, 10)) {
                    $lineLength = $k - $startLine;
                }
            }
        }
        $func = array_splice($func, $startLine, $lineLength);
        $func = implode("\n", $func);
        eval($func);

        if (isset($mode, $defaultRule, $rolesRule, $usersRule)) {
            $this->defaultRule = $defaultRule;
            $this->accessType = $mode;
            $this->rolesRule = $this->flattenAccessControlArray($rolesRule, 'role');
            $this->usersRule = $this->flattenAccessControlArray($usersRule, 'user');
        }

        return [];
    }

    CONST GEN_COMMENT_START = "####### GENERATED CODE - DO NOT EDIT #######";
    CONST GEN_COMMENT_END = "####### END OF PLANSYS GENERATED CODE ######";

    public function updateAccessControl($post) {
        $code = [];
        $accessType = $post['accessType'];
        if ($accessType) {
            ## PREPARE VARS
            $defaultRule = $post['defaultRule'] != 'allow' ? 'deny' : 'allow';
            $roles = $this->expandAccessControlArray($post['roles'], 'role');
            $rolesCode = CodeGenerator::varExport($roles);
            $users = $this->expandAccessControlArray($post['users'], 'user');
            $usersCode = CodeGenerator::varExport($users);

            ## PREPARE GENERATED VARS
            $code[] = ModuleGenerator::GEN_COMMENT_START;
            $code[] = '$mode = "DEFAULT";';
            $code[] = '$defaultRule = "' . $defaultRule . '";';
            $code[] = '$rolesRule = ' . $rolesCode . ';';
            $code[] = '$usersRule = ' . $usersCode . ';';
            $code[] = ModuleGenerator::GEN_COMMENT_END;
            $code[] = '';

            ## START ACTUAL CODE
            $code[] = 'parent::beforeControllerAction($controller, $action);';
            $code[] = '$allowed = ($defaultRule == "allow");';
            $code[] = '$roleId = Yii::app()->user->roleId;';
            $code[] = '$userId = Yii::app()->user->id;';
            $code[] = '';
            $code[] = 'if (in_array($roleId, $rolesRule["deny"]))  { ';
            $code[] = '    $allowed = false; ';
            $code[] = '}';
            $code[] = 'if (in_array($roleId, $rolesRule["allow"])) { ';
            $code[] = '    $allowed = true; ';
            $code[] = '}';
            $code[] = 'if (in_array($userId, $usersRule["deny"]))  { ';
            $code[] = '    $allowed = false; ';
            $code[] = '}';
            $code[] = 'if (in_array($userId, $usersRule["allow"])) { ';
            $code[] = '    $allowed = true;';
            $code[] = '}';
            $code[] = '';
            $code[] = 'if (!$allowed) {';
            $code[] = '    throw new CHttpException(403);';
            $code[] = '}';
            $code[] = '';
            $code[] = 'return true;';
        } else {
            
        }

        $space = '        ';
        $code = $space . implode("\n{$space}", $code);

        $this->updateFunction('beforeControllerAction', $code, [
            'params' => ['$controller', '$action']
        ]);
    }

    public function updateImport($code) {
        $this->updateFunction('init', $code);
    }

    public function getControllers() {
        $dir = Yii::getPathOfAlias($this->basePath) . DIRECTORY_SEPARATOR . "controllers";
        if (!is_dir($dir))
            return [];

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        $controllers = [];
        foreach ($iterator as $path => $item) {
            if ($item->isFile()) {
                $file = $item->getFilename();
                $class = basename($item->getFilename(), ".php");
                $controllers[] = [
                    'file'  => $file,
                    'class' => $class,
                    'alias' => $this->basePath . '.controllers.' . $class,
                    'path'  => $path
                ];
            }
        }
        return $controllers;
    }

    public function getAliasArray($dirs = [], $options = [
        'append'  => '',
        'prepend' => ''
    ]) {
        $result = [];
        foreach ($dirs as $dir) {
            $path = $this->baseDir . DIRECTORY_SEPARATOR . $dir;
            if (is_dir($path)) {
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
                foreach ($iterator as $p => $i) {
                    if ($i->isDir() && $i->getFilename() != '..') {
                        $str = substr(str_replace([$path, '/', '\\'], [$this->basePath . ".{$dir}", '.', '.'], $p), 0, -2);
                        $str = @$options['prepend'] . $str . @$options['append'];
                        $result[] = $str;
                    }
                }
            }
        }
        return $result;
    }

    public function listImport() {
        $imports = $this->getFunctionBody('init');
        array_pop($imports);
        array_shift($imports);
        return implode("\n", $imports);
    }

    public function generateImport($executeImport = false) {
        $importedFolders = ['controllers', 'forms', 'components', 'models', 'consoles'];
        $space = "            ";
        $imports = $space . implode(",\n{$space}", $this->getAliasArray($importedFolders, [
                            'append'  => '.*\'',
                            'prepend' => '\''
        ]));
        $source = <<<EOF
        // import the module-level controllers and forms
        \$this->setImport(array(
{$imports}
        ));
EOF;

        if ($executeImport) {
            $this->updateFunction('init', $source);
        }

        return $source;
    }

}
