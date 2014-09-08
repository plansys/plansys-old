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
        $dir = explode(DIRECTORY_SEPARATOR, Yii::getPathOfAlias('application'));
        $static = "/" . array_pop($dir) . "/static";
        return $this->url($static . $path);
    }

    public function renderForm($class, $model = null, $options = array()) {
        $fb = FormBuilder::load($class);
        $this->pageTitle = $fb->form['title'];
        $this->layout = '//layouts/form';

        $renderOptions = array(
            'wrapForm' => true,
            'action' => $this->action->id,
        );

        $renderOptions = array_merge($renderOptions, $options);        
        $mainform = $fb->render($model, $renderOptions);

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
        $default = array(
            array(
                'label' => 'Login',
                'url' => array('/site/login'),
                'visible' => Yii::app()->user->isGuest
            ),
            array(
                'label' => '' . ucfirst(Yii::app()->user->name),
                'url' => '#',
                'items' => array(
                    array(
                        'label' => 'Logout',
                        'url' => array('/site/logout'),
                    )
                ),
                'itemOptions' => array(
                    'style' => 'border-right:1px solid rgba(0,0,0,.1)'
                ),
                'visible' => !Yii::app()->user->isGuest
            ),
        );
        if (Yii::app()->user->isGuest) {
            return $default;
        } else {
            $module = Yii::app()->user->role;

            $path = Yii::getPathOfAlias("application.modules.{$module}.menus.MainMenu") . ".php";
            if (!is_file($path)) {
                $path = Yii::getPathOfAlias("app.modules.{$module}.menus.MainMenu") . ".php";
            }
            
            $menuModule = include($path);
            return array_merge($default, $menuModule);
        }
    }

    public function loadModel($id, $form) {
        $model = $form::model()->findByPk($id);

        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

}
