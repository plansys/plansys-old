<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/main';
    public $reportLayout = '//layouts/report';

    public function staticUrl($path = '')
    {
        $static = "/static";

        if (!isset($_GET['errorBeforeInstall'])) {
            $dir = explode(DIRECTORY_SEPARATOR, Yii::getPathOfAlias('application'));
            $static = "/" . array_pop($dir) . "/static";
        }

        return $this->url($static . $path);
    }

    public function url($path)
    {
        return Yii::app()->request->baseUrl . $path;
    }

    public function staticAppUrl($path)
    {
        $dir = explode(DIRECTORY_SEPARATOR, Yii::getPathOfAlias('app'));
        $static = "/" . array_pop($dir) . "/static";
        return $this->url($static . $path);
    }

    public function isPosted($class)
    {
        $valid = true;
        $args = func_get_args();
        foreach ($args as $c) {
            if (is_object($c)) {
                $c = get_class($c);
            }

            if (null === $this->getPost($c)) {
                $valid = false;
                break;
            }
        }

        return $valid;
    }

    public function getPost($class)
    {
        if (isset($this->module)) {
            $module = $this->module->id;
            if (substr($class, 0, strlen($module)) != $module) {
                $class = ucfirst($module) . ucfirst($class);
            }
        }

        return @$_POST[$class];
    }

    public function renderReport($file, $data = null, $return = false)
    {
        $report = new Report;

        $filePath = $this->getReportFile($file);

        $output = $report->load($filePath, $data);

        if (($layoutFile = $this->getLayoutFile($this->reportLayout)) !== false) {
            $output = $this->renderFile($layoutFile, array('content' => $output), true);
        }

        $output = $this->processOutput($output);

        $report->createPdf($output);
    }

    public function getReportFile($reportFile)
    {
        $ds = DIRECTORY_SEPARATOR;
        if (strpos($reportFile, '.')) {
            $filePath = Yii::getPathOfAlias($reportFile) . '.php';
        } else {
            if ($this->getModule() !== null) {
                $basePath = $this->module->basePath;
                $filePath = $basePath . $ds . "reports" . $ds . $reportFile . ".php";
            } else {
                $alias = Helper::getAlias($this);
                $location = Helper::explodeFirst('.', $alias);
                $reportAlias = $location . '.reports';
                $filePath = Yii::getPathOfAlias($reportAlias) . $ds . $reportFile . '.php';
            }
        }
        if (is_file($filePath)) {
            return $filePath;
        } else {
            throw new CException(Yii::t('yii', '{controller} cannot find the requested report "{view}".', array('{controller}' => get_class($this), '{view}' => $reportFile)));
        }
    }

    public function renderForm($class, $model = null, $params = [], $options = [])
    {
        if (is_array($model)) {
            $options = $params;
            $params = $model;
            $model = null;
        }
        $fb = FormBuilder::load($class);

        ## check if layout property is declared by rendering controller
        $reflection = new ReflectionObject($this);
        $layoutClass = $reflection->getProperty('layout')->getDeclaringClass()->getName();

        ## if layout property is not declared, then set it to default form layout '//layouts/form'
        if ($layoutClass != get_class($this)) {
            $this->layout = '//layouts/form';
        }

        ## set page title & layout to options
        $this->pageTitle = isset($options['pageTitle']) ? $options['pageTitle'] : $fb->form['title'];
        $this->layout = isset($options['layout']) ? $options['layout'] : $this->layout;

        $renderOptions = [
            'wrapForm' => true,
            'action' => $this->action->id,
        ];

        if (is_object($model)) {
            if (get_class($model) != Helper::explodeLast(".", $class)) {
                throw new Exception("Invalid model name, please instantiate model from {$class} class");
            }
            $fb->model = $model;
        }

        $options['params'] = $params;
        $renderOptions = array_merge($renderOptions, $options);
        $mainform = $fb->render($model, $renderOptions);
        $data = $fb->form['layout']['data'];
        $renderSection = @$_GET['render_section'];

        foreach ($data as $k => $d) {
            if ($d['type'] == "mainform") {
                $data[$k]['content'] = $mainform;
            }
            if (isset($data[$renderSection]) && $k != $renderSection) {
                unset($data[$k]);
            }
        }

        $layout = Layout::render($fb->form['layout']['name'], $data, $model, true);
        $this->renderText($layout, false);
    }

    public function prepareFormName($class, $module = null)
    {
        if (isset($module)) {
            if (is_string($module)) {
                $moduleList = Setting::getModules();
                if (isset($moduleList[$module])) {
                    $moduleAlias = $moduleList[$module]['class'];
                    $moduleClass = Helper::explodeLast(".", $moduleAlias);
                    Yii::import($moduleAlias);

                    if (@class_exists($moduleClass)) {
                        $module = new $moduleClass($module, null);
                    }
                }
            }

            if (!is_object($module)) {
                $module = null;
            }
        } else if (!isset($module)) {
            if (isset($this->module)) {
                $module = $this->module;
            }
        }


        if (strpos($class, '.') > 0) {
            $className = Helper::explodeLast(".", $class);

            if (!class_exists($className, false)) {
                try {
                    Yii::import($class);
                } catch (CException $e) {

                    if ($module) {
                        $moduleAlias = Helper::getAlias($module->basePath);
                        Yii::import($moduleAlias . ".forms." . $class);
                    } else {
                        $reflection = new ReflectionClass($this);
                        $path = $reflection->getFileName();

                        if (strpos($path, Yii::getPathOfAlias('app')) === 0) {
                            Yii::import('app.forms.' . $class);
                        } else if (strpos($path, Yii::getPathOfAlias('application')) === 0) {
                            Yii::import('application.forms.' . $class);
                        }
                    }
                }
            }

            $class = $className;
        } else {
            if (isset($module)) {
                $module = $module->id;
                if (stripos($class, $module) !== 0) {
                    if (!@class_exists($class)) {
                        $class = ucfirst($module) . ucfirst($class);
                    }
                }
            }
        }

        return $class;
    }

    public function flash($info = '')
    {
        Yii::app()->user->setFlash('info', $info);
    }

    public function getMainMenu()
    {
        if (Setting::$mode == "init" || Setting::$mode == "install") {
            if ($this->module->id != "install") {
                $this->redirect(["/install/default/index"]);
            }
            return [
                [
                    'label' => 'Plansys Installer'
                ]
            ];
        }


        $name = "";
        if (!Yii::app()->user->isGuest) {
            if (isset(Yii::app()->user->model)) {
                $name = Yii::app()->user->model->username;
            } else {
                Yii::app()->user->logout();
                $this->redirect(["/"]);
            }
        }

        $userItems = [
            [
                'label' => 'Edit Profile',
                'url' => ['/sys/profile/index'],
            ],
            [
                'label' => '---',
            ],
            [
                'label' => 'Logout',
                'url' => ['/site/logout'],
            ]
        ];

        if (Yii::app()->user->model) {
            $roles = Yii::app()->user->model->getRoles(true);
            if (count($roles) > 1) {

                $roleItems = [];
                foreach ($roles as $k => $r) {
                    $rc = ($r['role_name'] == Yii::app()->user->fullRole ? 'fa-check-square-o' : 'fa-square-o');

                    array_push($roleItems, [
                        'label' => '&nbsp; <i class="fa ' . $rc . '"></i> &nbsp;' . $r['role_description'],
                        'url' => ['/sys/profile/changeRole', 'id' => $r['id']]
                    ]);
                }
                array_unshift($userItems, [
                    'label' => 'Switch Role',
                    'items' => $roleItems
                ]);
            }
        }

        $default = [
            [
                'label' => 'Login',
                'url' => ['/site/login'],
                'visible' => Yii::app()->user->isGuest
            ],
            [
                'label' => ucfirst($name),
                'url' => '#',
                'items' => $userItems,
                'itemOptions' => [
                    'style' => 'border-right:1px solid rgba(0,0,0,.1)'
                ],
                'visible' => !Yii::app()->user->isGuest
            ],
        ];

        if (Yii::app()->user->isGuest) {
            return $default;
        } else {
            $module = Yii::app()->user->role;
            $menuPath = Yii::app()->user->menuPath;
            $menuPath = $menuPath == '' ? 'MainMenu' : $menuPath;

            $path = Yii::getPathOfAlias("application.modules.{$module}.menus.{$menuPath}") . ".php";
            if (!is_file($path)) {
                $path = Yii::getPathOfAlias("app.modules.{$module}.menus.{$menuPath}") . ".php";
            }

            $menuModule = [];
            if (is_file($path)) {
                $menuModule = include($path);
            }

            if (is_array($menuModule)) {
                return array_merge($default, $menuModule);
            } else {
                return $default;
            }
        }
    }

    public function newModel($class)
    {
        $class = $this->prepareFormName($class);
        return new $class;
    }

    public function loadAllModel($class, $attr)
    {
        ## kalo ternyata $form dan $attr kebalik
        if (is_string($attr)) {
            $temp = $attr;
            $attr = $class;
            $class = $temp;
        }

        ## proses load model
        $class = $this->prepareFormName($class);
        $model = $class::model($class)->findAllByAttributes($attr);
        if (empty($model)) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

    public function loadModel($class, $attr)
    {
        ## kalo ternyata $form dan $attr kebalik
        if (is_array($class) || is_numeric($class)) {
            $temp = $attr;
            $attr = $class;
            $class = $temp;
        }

        ## proses load model
        $class = $this->prepareFormName($class);
        if (is_array($attr)) {
            $model = $class::model($class)->findByAttributes($attr);
        } else {
            $model = $class::model($class)->findByPk($attr);
        }

        if (!is_null($model) && method_exists($model, 'loadAllRelations')) {
            $model->loadAllRelations();
        }

        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $model;
    }

    public function setInfo($info)
    {
        Yii::app()->user->setFlash('info', $info);
    }

    public function beforeAction($action)
    {
        ## when mode is init or install then redirect to installation mode
        if (Setting::$mode == "init" || Setting::$mode == "install") {
            if ($this->id != "default") {
                $this->redirect(['/install/default/index']);
                return false;
            }
        }

        parent::beforeAction($action);
        return true;
    }

}
