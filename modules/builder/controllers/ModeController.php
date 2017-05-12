<?php

if (!class_exists('ContentMode', false)) {
    Yii::import('application.modules.builder.components.ContentMode');
}

class ModeController extends Controller {

    public $enableCsrf = false;

    public function createAction($actionID) {
        $controller = explode('.', $actionID);
        if (count($controller) > 1) {
            $action = $controller[1];
            $mode = $controller[0];
            $class = ucfirst($mode) . 'Controller';
            $ctrlpath = Yii::getPathOfAlias(ContentMode::$ctrlalias . '.' . $class);

            if (is_file($ctrlpath . '.php')) {
                require $ctrlpath . '.php';
                $ctrl = new $class($mode, 'builder');

                if (method_exists($ctrl, 'action' . ucfirst($action))) {
                    return new CInlineAction($ctrl, $action);
                }
            } 
        }
        throw new CHttpException('404', 'Action Not Found');
    }
}
