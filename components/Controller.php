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
    public $layout = '//layouts/main';

    public function url($path) {
        return Yii::app()->request->baseUrl . $path;
    }

    public function staticUrl($path) {
        $static = "/static";
        if (substr(Yii::app()->baseUrl, -7) != "plansys") {
            $dir    = explode(DIRECTORY_SEPARATOR, Yii::getPathOfAlias('application'));
            $static = "/" . array_pop($dir) . "/static";
        }

        return $this->url($static . $path);
    }

    public function staticAppUrl($path) {
        $dir    = explode(DIRECTORY_SEPARATOR, Yii::getPathOfAlias('app'));
        $static = "/" . array_pop($dir) . "/static";
        return $this->url($static . $path);
    }

    public function renderForm($class, $model = null, $params = [], $options = []) {
        $fb              = FormBuilder::load($class);
        $this->pageTitle = $fb->form['title'];
        $this->layout    = '//layouts/form';

        $renderOptions = [
            'wrapForm' => true,
            'action'   => $this->action->id,
        ];

        if (is_array($model)) {
            $params = $model;
            $model  = null;
        }
        $options['params'] = $params;

        $renderOptions = array_merge($renderOptions, $options);
        $mainform      = $fb->render($model, $renderOptions);

        $data = $fb->form['layout']['data'];

        foreach ($data as $k => $d) {
            if ($d['type'] == "mainform") {
                $data[$k]['content'] = $mainform;
            }
        }

        $layout = Layout::render($fb->form['layout']['name'], $data, $model, true);

        $this->renderText($layout, false);
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
            $name = Yii::app()->user->model->fullname;
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

        if (Yii::app()->user->model) {
            $roles = Yii::app()->user->model->getRoles(true);

            if (count($roles) > 1) {

                $roleItems = [];
                foreach ($roles as $k => $r) {
                    $rc = ($r['role_name'] == Yii::app()->user->fullRole ? 'fa-check-square-o' : 'fa-square-o');

                    array_push($roleItems, [
                        'label' => '&nbsp; <i class="fa ' . $rc . '"></i> &nbsp;' . $r['role_description'],
                        'url'   => ['/sys/profile/changeUser', 'id' => $r['id']]
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
                'label'   => 'Login',
                'url'     => ['/site/login'],
                'visible' => Yii::app()->user->isGuest
            ],
            [
                'label'       => ucfirst($name),
                'url'         => '#',
                'items'       => $userItems,
                'itemOptions' => [
                    'style' => 'border-right:1px solid rgba(0,0,0,.1)'
                ],
                'visible'     => !Yii::app()->user->isGuest
            ],
        ];
        if (Yii::app()->user->isGuest) {
            return $default;
        } else {
            $module   = Yii::app()->user->role;
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

    public function loadAllModel($id, $form) {
        if (strpos($form, '.') > 0) {
            Yii::import($form);
            $form = Helper::explodeLast(".", $form);
        }

        $model = $form::model($form)->findAllByAttributes($id);
        if (empty($model)) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }

        return $model;
    }

    public function beforeAction($action) {
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

    public function loadModel($idOrAttributes, $form) {
        if (strpos($form, '.') > 0) {
            Yii::import($form);
            $form = Helper::explodeLast(".", $form);
        }
        if (is_array($idOrAttributes)) {
            $model = $form::model($form)->findByAttributes($idOrAttributes);
        } else {
            $model = $form::model($form)->findByPk($idOrAttributes);
        }

        if (!is_null($model) && method_exists($model, 'loadAllRelations')) {
            $model->loadAllRelations();
        }

        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $model;
    }

}
