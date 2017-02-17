<?php

class ModuleGenerator extends CodeGenerator {

    protected $basePath = "app.modules";
    protected $baseClass = "WebModule";
    public $defaultRule = '';
    public $accessType = '';
    public $rolesRule = [];
    public $usersRule = [];
    public $acSource = '';

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
                        'label' => $label,
                        'module' => 'plansys',
                        'icon' => 'fa-empire',
                        'active' => @$_GET['active'] == 'plansys.' . $label,
                        'url' => Yii::app()->controller->createUrl('/dev/genModule/index', [
                            'active' => 'plansys.' . $label
                        ]),
                        'target' => 'col2'
                    ];
                }
            }

            $list[] = [
                'label' => 'Plansys',
                'module' => 'plansys',
                'items' => $plansysList
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
                    'label' => $label,
                    'module' => 'app',
                    'icon' => 'fa-empire',
                    'active' => @$_GET['active'] == 'app.' . $label,
                    'url' => Yii::app()->controller->createUrl('/dev/genModule/index', [
                        'active' => 'app.' . $label
                    ]),
                    'target' => 'col2'
                ];
            }
        }

        $list[] = [
            'label' => 'App',
            'module' => 'app',
            'items' => $appList
        ];
        return $list;
    }
    
    public static function listAppModules() {
        
        $dir = Yii::getPathOfAlias("app.modules") . DIRECTORY_SEPARATOR;
        $items = glob($dir . "*", GLOB_ONLYDIR);
        $appList = [];
        foreach ($items as $k => $f) {
            $label = str_replace($dir, "", $f);
            $classPath = $f . DIRECTORY_SEPARATOR . ucfirst($label) . 'Module.php';
            if (is_file($classPath)) {
                $appList[$label] = $label;
            }
        }
        return $appList;
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

        if (Helper::isValidVar($class)) {
            $m->load($class);

            if ($mode == 'create') {
                if (!is_dir($m->baseDir . DIRECTORY_SEPARATOR . 'controllers')) {
                    mkdir($m->baseDir . DIRECTORY_SEPARATOR . 'controllers');
                    chmod($m->baseDir . DIRECTORY_SEPARATOR . 'controllers', 0755);
                }

                if (!is_dir($m->baseDir . DIRECTORY_SEPARATOR . 'forms')) {
                    mkdir($m->baseDir . DIRECTORY_SEPARATOR . 'forms');
                    chmod($m->baseDir . DIRECTORY_SEPARATOR . 'forms', 0755);
                }

                $m->generateImport(true);
                $m->updateAccessControl([
                    'accessType' => 'DEFAULT',
                    'defaultRule' => 'deny',
                    'roles' => [],
                    'users' => []
                ]);
            } else {
                $m->loadAccessControl();
            }
            return $m;
        } else {
            return null;
        }
    }

    private function customFuncStart($key, $v) {
        $marker = strtoupper($key . ':' . $v);
        return '### FUNCTION ' . $marker . ' START [:.v.:]';
    }

    private function customFuncEnd($key, $v) {
        $marker = strtoupper($key . ':' . $v);
        return '### FUNCTION ' . $marker . '  END  [:.^.:]';
    }

    private function expandAccessControlArray($array, $key) {
        $prepared = [
            'deny' => [],
            'allow' => [],
            'custom' => []
        ];
        $inserted = [];

        foreach ($array as $a) {
            if (!isset($a['access']) || !isset($a[$key]) || $a[$key] == '' || in_array($a[$key], $inserted))
                continue;

            $access = strtolower($a['access']);
            if (!in_array($a[$key], $prepared[$access])) {
                switch ($access) {
                    case "deny":
                    case "allow":
                        $prepared[$access][] = $a[$key];
                        break;
                    case "custom":
                        $code = trim($this->addIndent($a['func'], '                '));
                        $prepared[$access][$a[$key]] = $this->markExecute('
                function($controller, $action) {
                ' . $this->customFuncStart($key, $a[$key]) . '
                ' . $code . ' 
                ' . $this->customFuncEnd($key, $a[$key]) . ' 
                }
');
                        break;
                }
            }
            $inserted[] = $a[$key];
        }

        $prepared['allow'] = array_filter($prepared['allow']);
        $prepared['deny'] = array_filter($prepared['deny']);
        return $prepared;
    }

    private function flattenAccessControlArray($array, $key, $func) {
        $result = [];
        foreach ($array['allow'] as $d) {
            $result[] = [
                $key . "" => $d,
                'access' => 'allow'
            ];
        }
        foreach ($array['deny'] as $d) {
            $result[] = [
                $key . "" => $d,
                'access' => 'deny'
            ];
        }
        if (is_array(@$array['custom'])) {
            foreach ($array['custom'] as $k => $d) {
                $code = Helper::getStringBetween($func, $this->customFuncStart($key, $k), $this->customFuncEnd($key, $k));
                $result[] = [
                    $key => $k . '',
                    'func' => trim($code),
                    'access' => 'custom',
                    'customMode' => 'custom'
                ];
            }
        }

        return $result;
    }

    public function checkAccessType() {
        $func = $this->getFunctionBody('accessControl');
        if (empty($func)) {
            return "DEFAULT";
        } else {
            $startLine = false;
            $lineLength = false;
            foreach ($func as $k => $f) {
                $tf = trim($f);
                if (!$startLine) {
                    if ($tf == ModuleGenerator::GEN_COMMENT_START) {
                        $startLine = $k + 1;
                    }
                } else if ($startLine && !$lineLength) {
                    if ($tf == ModuleGenerator::GEN_COMMENT_END) {
                        $lineLength = $k - $startLine;
                    }
                }
            }
            if (!!$startLine && !!$lineLength) {
                $func = array_splice($func, $startLine, $lineLength);
                $func = implode("\n", $func);
                ob_start();
                eval($func);
                ob_get_clean();

                if (isset($accessType)) {
                    return $accessType;
                } else {
                    return "CUSTOM";
                }
            } else {
                return "CUSTOM";
            }
        }
    }

    public function loadAccessControl() {
        $startLine = false;
        $lineLength = false;
        $func = $this->getFunctionBody('accessControl');
        $this->acSource = $this->varToString($func);
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

        if (!!$startLine && !!$lineLength) {
            $func = array_splice($func, $startLine, $lineLength);
            $func = implode("\n", $func);
            eval($func);

            if (isset($accessType, $defaultRule, $rolesRule, $usersRule)) {
                $this->defaultRule = $defaultRule;
                $this->accessType = $accessType;
                $this->rolesRule = $this->flattenAccessControlArray($rolesRule, 'role', $func);
                $this->usersRule = $this->flattenAccessControlArray($usersRule, 'user', $func);
            }
        }
    }

    CONST GEN_COMMENT_START = "####### PLANSYS GENERATED CODE: START #######";
    CONST GEN_COMMENT_END = "####### PLANSYS GENERATED CODE:  END  #######";

    public function updateAccessControl($post) {
        $code = [];
        $accessType = $post['accessType'];
        if ($accessType == 'DEFAULT') {
            ## PREPARE VARS
            $defaultRule = $post['defaultRule'] != 'allow' ? 'deny' : 'allow';
            $roles = $this->expandAccessControlArray($post['roles'], 'role');
            $rolesCode = CodeGenerator::varExport($roles);
            $users = $this->expandAccessControlArray($post['users'], 'user');
            $usersCode = CodeGenerator::varExport($users);

            ## PREPARE GENERATED VARS
            $code[] = ModuleGenerator::GEN_COMMENT_START;
            $code[] = '#######    DO NOT EDIT CODE BELOW     #######';
            $code[] = '$accessType = "' . $accessType . '";';
            $code[] = '$defaultRule = "' . $defaultRule . '";';
            $code[] = '$rolesRule = ' . $rolesCode . ';';
            $code[] = '$usersRule = ' . $usersCode . ';';
            $code[] = '#######    DO NOT EDIT CODE ABOVE     #######';
            $code[] = ModuleGenerator::GEN_COMMENT_END;
            $code[] = '';

            ## START ACTUAL CODE
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
            $code[] = 'if (array_key_exists($roleId, $rolesRule["custom"])) { ';
            $code[] = '    call_user_func($rolesRule["custom"][$roleId], $controller, $action); ';
            $code[] = '}';
            $code[] = 'if (in_array($userId, $usersRule["deny"]))  { ';
            $code[] = '    $allowed = false; ';
            $code[] = '}';
            $code[] = 'if (in_array($userId, $usersRule["allow"])) { ';
            $code[] = '    $allowed = true;';
            $code[] = '}';
            $code[] = 'if (array_key_exists($userId, $usersRule["custom"])) { ';
            $code[] = '    call_user_func($usersRule["custom"][$userId], $controller, $action); ';
            $code[] = '}';
            $code[] = '';
            $code[] = 'if (!$allowed) {';
            $code[] = '    throw new CHttpException(403);';
            $code[] = '}';

            $space = '        ';
            $code = $space . implode("\n{$space}", $code);

            $this->accessType = 'DEFAULT';
            $this->defaultRule = $defaultRule;
            $this->rolesRule = $post['roles'];
            $this->usersRule = $post['users'];
            $this->acSource = $this->removeIndent($code);
        } else {
            $code = explode("\n", $post['code']);
            if (count($code) > 1 && trim($code[1]) == '$accessType = "DEFAULT";') {
                $code[1] = '$accessType = "CUSTOM";';
            }
            $code = implode("\n", $code);
            $this->accessType = "CUSTOM";
            $this->acSource = $code;
            $code = $this->addIndent($code);
        }
        $this->updateFunction('accessControl', $code, [
            'params' => ['$controller', '$action']
        ]);
    }

    public function updateImport($code) {
        $this->updateFunction('init', $this->addIndent($code));
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
                    'file' => $file,
                    'class' => $class,
                    'alias' => $this->basePath . '.controllers.' . $class,
                    'path' => $path
                ];
            }
        }
        return $controllers;
    }

    public function getAliasArray($dirs = [], $options = [
        'append' => '',
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

    public function loadImport() {
        $imports = $this->getFunctionBody('init');
        return $this->varToString($imports);
    }

    public function generateImport($executeImport = false) {
        $importedFolders = ['controllers', 'forms', 'components', 'models', 'consoles'];
        $space = "            ";
        $imports = $space . implode(",\n{$space}", $this->getAliasArray($importedFolders, [
                            'append' => '.*\'',
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

    public static function parseModule($module) {
        $m = explode(".", $module);
        if (count($m) == 2 && $m[1] != '') {
            $name = lcfirst($m[1]);
            $class = ucfirst($name) . "Module";
            $basePath = $m[0] == "app" ? Setting::getAppPath() : Setting::getApplicationPath();
            $alias = ($m[0] == "app" ? 'app' : 'application') . ".modules.{$name}.{$class}";
            $path = $basePath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $name;
            $classPath = $path . DIRECTORY_SEPARATOR . $class . ".php";

            if (!Helper::isValidVar($class)) {
                return [];
            }

            return [
                'name' => $name,
                'class' => $class,
                'alias' => $alias,
                'path' => $path,
                'classPath' => $classPath
            ];
        }
        return [];
    }

    public static function rename($from, $to) {
        $from = ModuleGenerator::parseModule($from);
        $to = ModuleGenerator::parseModule($to);

        if (empty($from)) {
            throw new CException("Invalid source name.");
            return false;
        }

        if (empty($to)) {
            throw new CException("Invalid destination name.");
            return false;
        }

        if (is_dir($from['path']) && is_file($from['classPath'])) {
            if (!is_file($to['classPath'])) {
                ## rename module class
                $file = file_get_contents($from['classPath']);
                $file = preg_replace('/class\s+' . $from['class'] . '/', 'class ' . $to['class'], $file, 1);
                file_put_contents($from['classPath'], $file);

                ## rename directory
                rename($from['classPath'], $from['path'] . DIRECTORY_SEPARATOR . $to['class'] . ".php");
                rename($from['path'], $to['path']);

                ## rename forms
                $formsDir = $to['path'] . DIRECTORY_SEPARATOR . 'forms';
                $forms = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($formsDir));
                foreach ($forms as $d) {
                    if ($d->isFile() && $d->getExtension() == "php") {

                        ## determine old and new file
                        $oldClass = substr($d->getFilename(), 0, -4);
                        $newClass = ucfirst($to['name']) . substr($oldClass, strlen($from['name']));
                        $oldFilePath = $d->getRealPath();
                        $newFilePath = $d->getPath() . DIRECTORY_SEPARATOR . $newClass . ".php";

                        ## change class name inside file
                        $file = file_get_contents($d->getRealPath());
                        $file = preg_replace('/class\s*' . $oldClass . '/', 'class ' . $newClass, $file, 1);
                        file_put_contents($oldFilePath, $file);

                        ## rename file
                        rename($oldFilePath, $newFilePath);
                    }
                }

                return $to;
            } else {
                throw new CException("Destination module already exist.");
                return false;
            }
        } else {
            throw new CException("Invalid source name.");
            return false;
        }
    }

}
