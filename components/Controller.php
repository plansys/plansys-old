<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController {

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout       = '//layouts/main';
    public $reportLayout = '//layouts/report';
    public $enableCsrf   = true;
    public $enableDebug  = true;

    public function staticUrl($path = '') {
        $static = "/static";
        if (!isset($_GET['errorBeforeInstall'])) {
            $dir    = explode(DIRECTORY_SEPARATOR, Yii::getPathOfAlias('application'));
            $static = "/" . array_pop($dir) . "/static";
        }

        return $this->url($static . $path);
    }

    public function url($path) {
        return Yii::app()->request->baseUrl . $path;
    }

    public function staticAppUrl($path) {
        $dir    = explode(DIRECTORY_SEPARATOR, Yii::getPathOfAlias('app'));
        $static = "/" . array_pop($dir) . "/static";
        return $this->url($static . $path);
    }

    public function isPosted($class) {
        $valid = true;
        $args  = func_get_args();
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

    public function getPost($class) {
        if (isset($this->module)) {
            $module = $this->module->id;
            if (substr($class, 0, strlen($module)) != $module) {
                $class = ucfirst($module) . ucfirst($class);
            }
        }

        return @$_POST[$class];
    }
    
    public function includeFile($file, $params = []) {
        $key = array_search(__FUNCTION__, array_column(debug_backtrace(), 'function'));
        $dbg = debug_backtrace();
        $dfile = $dbg[$key]['file'];
        $ddir = dirname($dfile);
        $included = false;
        
        extract($params);
		$themeName = Setting::get('app.theme');
		
		
		if (!is_null($themeName)) {
		    $appDir = Yii::getPathOfAlias('app.themes.' . $themeName . ".views");
		    $psDir = Yii::getPathOfAlias('plansys.themes.' . $themeName . ".views");
		    
		    if (strpos($dfile, $psDir) === 0) {
		        $fpath = substr($dfile, strlen($psDir));
		        $fpath = substr($fpath, 0, strlen($fpath) - strlen(basename($dfile)));
		        
		        if (is_file($appDir . $fpath . $file)) {
		            include($appDir . $fpath . $file);
		            $included = true;
		        }
		    }
		}
		
		if (!is_null($themeName)) {
		    $appDir = Yii::getPathOfAlias('app.themes.' . $themeName . ".views");
		    $psDir = Yii::getPathOfAlias('plansys.themes.' . $themeName . ".views");
		    
		    if (strpos($dfile, $appDir) === 0) {
		        $fpath = substr($dfile, strlen($appDir));
		        $fpath = substr($fpath, 0, strlen($fpath) - strlen(basename($dfile)));
		        
		        if (is_file($psDir . $fpath . $file)) {
		            include($psDir . $fpath . $file);
		            $included = true;
		        }
		    }
		}
		
        if (!$included && is_file($ddir . DIRECTORY_SEPARATOR . $file)) {
            include($ddir . DIRECTORY_SEPARATOR . $file);
        } 
        
    }

	public function resolveViewFile($viewName,$viewPath,$basePath,$moduleViewPath=null)
	{
		if(empty($viewName))
			return false;

		if($moduleViewPath===null)
			$moduleViewPath=$basePath;

		if(($renderer=Yii::app()->getViewRenderer())!==null)
			$extension=$renderer->fileExtension;
		else
			$extension='.php';
			
		if($viewName[0]==='/')
		{
			if(strncmp($viewName,'//',2)===0) {
				$viewFile=$basePath.$viewName;
				$themeName = Setting::get('app.theme');
            
				if (!is_null($themeName)) {
				    $appViewFile = Yii::getPathOfAlias('app.themes.' . $themeName . ".views") . $viewName;
				    
				    if (is_file($appViewFile . $extension)) {
				        $viewFile = $appViewFile;
				    }
				}
				
			}
			else
				$viewFile=$moduleViewPath.$viewName;
		}
		elseif(strpos($viewName,'.'))
			$viewFile=Yii::getPathOfAlias($viewName);
		else
			$viewFile=$viewPath.DIRECTORY_SEPARATOR.$viewName;

		if(is_file($viewFile.$extension))
			return Yii::app()->findLocalizedFile($viewFile.$extension);
		elseif($extension!=='.php' && is_file($viewFile.'.php'))
			return Yii::app()->findLocalizedFile($viewFile.'.php');
		else
			return false;
	}

    public function getViewFile($viewName) {
        $basePath = $this->getBaseViewPath();
        $moduleViewPath = null;
        $viewPath = $basePath . DIRECTORY_SEPARATOR . $this->id;
        if (($module         = $this->getModule()) !== null) {
            $moduleViewPath = $module->getViewPath();
            $viewPath = $moduleViewPath . DIRECTORY_SEPARATOR . $this->id; 
        } 

        $result = $this->resolveViewFile($viewName, $viewPath, $basePath, $moduleViewPath);
        
        ## if file is not found, try in plansys theme dir
        if (!$result) {
            $basePath = Yii::getPathOfAlias('application.themes.' . Setting::getDefaultTheme() . '.views');
            $viewPath = $basePath . DIRECTORY_SEPARATOR . $this->id;
            $result = $this->resolveViewFile($viewName, $viewPath, $basePath, $moduleViewPath);
        }
        return $result;
    }

    public function getLayoutFile($layoutName) {
        if ($layoutName === false)
            return false;

        if (empty($layoutName)) {
            $module = $this->getModule();
            while ($module !== null) {
                if ($module->layout === false)
                    return false;
                if (!empty($module->layout))
                    break;
                $module = $module->getParentModule();
            }
            if ($module === null)
                $module     = Yii::app();
            $layoutName = $module->layout;
        }
        elseif (($module = $this->getModule()) === null)
            $module = Yii::app();
        
        
        return $this->resolveViewFile($layoutName, $module->getLayoutPath(), $this->getBaseViewPath(), $module->getViewPath());
    }

    public function getBaseViewPath() {
        return Setting::getViewPath();
    }

    public function renderForm($class, $model = null, $params = [], $options = []) {
        if (is_array($model)) {
            $options = $params;
            $params  = $model;
            $model   = null;
        }
        $fb = FormBuilder::load($class);

        ## check if layout property is declared by rendering controller
        $reflection  = new ReflectionObject($this);
        $layoutClass = $reflection->getProperty('layout')->getDeclaringClass()->getName();

        ## if layout property is not declared, then set it to default form layout '//layouts/form'
        if ($layoutClass != get_class($this)) {
            $this->layout = '//layouts/form';
        }

        ## set page title & layout to options
        $this->pageTitle = isset($options['pageTitle']) ? $options['pageTitle'] : @$fb->form['title'];
        $this->layout    = isset($options['layout']) ? $options['layout'] : $this->layout;

        $renderOptions = [
            'wrapForm' => true,
            'action'   => $this->action->id,
        ];

        if (is_object($model)) {
            if (get_class($model) != Helper::explodeLast(".", $class)) {
                throw new Exception("Invalid model name, please instantiate model from {$class} class");
            }
            $fb->model = $model;
        }

        $options['params'] = $params;
        $renderOptions     = array_merge($renderOptions, $options);
        $mainform          = $fb->render($model, $renderOptions);
        $data              = $fb->form['layout']['data'];
        $renderSection     = @$_GET['render_section'];

        foreach ($data as $k => $d) {
            if ($d['type'] == "mainform") {
                $data[$k]['content'] = $mainform;
            }
            if (isset($data[$renderSection]) && $k != $renderSection) {
                unset($data[$k]);
            }
        }
        Yii::trace('Rendering Form: ' . $class);

        if ($this->beforeRender($class)) {
            $layout = Layout::render($fb->form['layout']['name'], $data, $model, true);
            $this->renderText($layout, false);
        }
    }

    public function prepareFormName($class, $module = null) {
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
                        $path       = $reflection->getFileName();

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

    public function flash($info = '') {
        Yii::app()->user->setFlash('info', $info);
    }

    public function getMainMenu() {
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
                'url'   => ['/sys/profile/index'],
            ],
            [
                'label' => '---',
            ],
            [
                'label' => 'Logout',
                'url'   => ['/site/logout'],
            ]
        ];
        
        if (!Yii::app()->user->isGuest) {
            if (Yii::app()->user->model) {
                $roles = Yii::app()->user->model->getRoles(true);
                if (count($roles) > 1) {
                    $roleItems = [];
                    foreach ($roles as $k => $r) {
                        $rc = ($r['role_name'] == Yii::app()->user->fullRole ? 'fa-check-square-o' : 'fa-square-o');
    
                        array_push($roleItems, [
                            'label' => '&nbsp; <i class="fa ' . $rc . '"></i> &nbsp;' . $r['role_description'],
                            'url'   => ['/sys/profile/changeRole', 'id' => $r['id']]
                        ]);
                    }
                    array_unshift($userItems, [
                        'label' => 'Switch Role',
                        'items' => $roleItems
                    ]);
                }
            }
        }

        $default = [
            [
                'label'   => 'Login',
                'url'     => ['/site/login'],
                'visible' => Yii::app()->user->isGuest
            ],
            [
                'label'       => ucfirst($name),
                'url'         => '#',
                'items'       => $userItems,
                'itemOptions' => [
                    'class' => 'user-menu'
                ],
                'visible'     => !Yii::app()->user->isGuest
            ],
        ];

        $baseMenu = Yii::getPathOfAlias('app.menus.BaseMenu') . ".php";
        if (is_file($baseMenu)) {
            include($baseMenu);
        }

        if (Yii::app()->user->isGuest) {
            return $default;
        } else {
            $module   = Yii::app()->user->role;
            $menuPath = Yii::app()->user->menuPath;
            $menuPath = $menuPath == '' ? 'MainMenu' : $menuPath;

            if (strpos($menuPath, ".") > 0) {
                $path = Yii::getPathOfAlias($menuPath) . ".php";
            } else {
                $path = Yii::getPathOfAlias("application.modules.{$module}.menus.{$menuPath}") . ".php";
                if (!is_file($path)) {
                    $path = Yii::getPathOfAlias("app.modules.{$module}.menus.{$menuPath}") . ".php";
                }
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

    public function newModel($class) {
        $class = $this->prepareFormName($class);
        return new $class;
    }

    public function loadAllModel($class, $attr) {
        ## kalo ternyata $form dan $attr kebalik
        if (is_string($attr)) {
            $temp  = $attr;
            $attr  = $class;
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

    public function loadModel($attr, $class) {
        ## kalo ternyata $form dan $attr kebalik
        if (is_array($class) || is_numeric($class)) {
            $temp  = $attr;
            $attr  = $class;
            $class = $temp;
        }

        if (trim($class) == '') {
            throw new CHttpException(404, 'The requested page does not exist.');
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

    public function setInfo($info) {
        Yii::app()->user->setFlash('info', $info);
    }
    
    protected function beforeAction($action) {
        ## Make sure service daemon is started
        ServiceManager::startDaemon();

        ## when mode is init or install then redirect to installation mode
        if (Setting::$mode == "init" || Setting::$mode == "install") {
            if ($this->id != "default") {
                $this->redirect(['/install/default/index']);
                return false;
            }
        }
        
        ## Setup actual url reference, for console command...
        $tempUrlPath = Yii::getPathOfAlias('root.assets.url') . ".txt";
        if (is_writeable(Setting::$path)) { 
            ## if settings.json is writeable then we dont need tempurl file
            if (is_file($tempUrlPath) && is_writeable($tempUrlPath)) {
                @unlink($tempUrlPath);
            }
        }
        
        $appUrl    = Setting::get('app.url');
        $actualUrl = Yii::app()->getRequest()->getHostInfo() . Yii::app()->getRequest()->getBaseUrl();
        if ($appUrl != $actualUrl) {
            try {
                Setting::set('app.url', $actualUrl);
            } catch(Exception $e) {
                @file_put_contents($tempUrlPath, $actualUrl);
            }
        }

        if (!$this->enableDebug) {
            foreach (Yii::app()->log->routes as $key=> $route)
            {
                $route->enabled = false;
            }
        }
        
        parent::beforeAction($action);
        
        if (Setting::$mode == 'running' && $this->enableDebug) {
            Yii::beginProfile('PlansysRenderForm');
        }
        return true;
    }

    protected function afterAction($action) {

        if (Setting::$mode == 'running' && $this->enableDebug) {
            Yii::endProfile('PlansysRenderForm');
        }
        parent::afterAction($action);

        return true;
    }

}
