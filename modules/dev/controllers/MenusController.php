<?php

class MenusController extends Controller {

    public function actionIndex() {
        $menus = MenuTree::listAllFile();
        $this->render('index', array(
            'menus' => $menus
        ));
    }

    public function actionRenderProperties() {
        $properties = FormBuilder::load('DevMenuEditor');

        if ($this->beginCache('DevMenuProperties', array(
                    'dependency' => new CFileCacheDependency(
                            Yii::getPathOfAlias('application.modules.dev.forms.DevMenuEditor') . ".php"
            )))
        ) {
            echo $properties->render();
            $this->endCache();
        }
    }

    public function actionSaveSource($class) {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);
        $file     = Yii::getPathOfAlias($class) . ".php";
        if (isset($post['code']) && is_file($file)) {
            file_put_contents($file, $post['code']);
            ob_start();
            include($file);
            ob_get_clean();

            echo MenuTree::isModeLocked($class) ? "locked" : "unlocked";
        }
    }

    public function actionSave($class) {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);

        if (isset($post['list'])) {
            MenuTree::cleanMenuItems($post['list']);
            $options = MenuTree::getOptions($class);

            $code = "<?php \n
" . MenuTree::OPTIONS_COMMENT_START . "
\$options = " . FormBuilder::formatCode($options, '') . ";
" . MenuTree::OPTIONS_COMMENT_END . "
\nreturn " . FormBuilder::formatCode($post['list'], '') . ";";

            file_put_contents(Yii::getPathOfAlias($class) . ".php", $code);
        }
    }

    public function actionSwitchMode($path, $mode) {
        $filename = Yii::getPathOfAlias($path) . ".php";
        if (is_file($filename)) {

            $options         = MenuTree::getOptions($path);
            $options['mode'] = $mode;
            $code            = MenuTree::setOptions($path, $options);

            file_put_contents($filename, $code);
        }
    }

    public function actionRename() {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);

        $from = @$post['from'];
        $to   = @$post['to'];

        if (!is_null($from) && !is_null($to)) {
            $path = explode(".", $from);
            array_pop($path);
            $path = implode(".", $path);
            $f    = Yii::getPathOfAlias($from) . ".php";
            $t    = Yii::getPathOfAlias($path . "." . $to) . ".php";

            if (file_exists($f)) {
                rename($f, $t);
            } else {
                file_put_contents($f, '<?php return array(); ');
            }
        }
    }

    public function actionDelete() {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);

        $item = @$post['item'];

        if (!is_null($item)) {
            $f = Yii::getPathOfAlias($item) . ".php";
            if (file_exists($f)) {
                unlink($f);
            }
        }
    }

    public function actionEmpty() {
        $this->layout = "//layouts/blank";
        $this->render('empty');
    }

    public function actionGetModeLocked($path) {
        echo MenuTree::isModeLocked($path) ? "locked" : "unlocked";
    }

    public function actionGetMode($path) {
        $options = MenuTree::getOptions($path);

        echo $options['mode'];
    }

    public function actionGetCode($path) {
        echo file_get_contents(Yii::getPathOfAlias($path) . ".php");
    }

    public function actionGetList($path) {
        $list = include(Yii::getPathOfAlias($path) . ".php");
        MenuTree::fillMenuItems($list);
        echo json_encode($list);
    }

    public function actionUpdate($class) {
        $this->layout = "//layouts/blank";

        $path = $class;

        $class_path = explode(".", $class);
        $class      = $class_path[count($class_path) - 1];
        $properties = FormBuilder::load('DevMenuEditor');

        $properties->registerScript();
        Asset::registerJS('application.static.js.lib.ace');

        $this->render('form', array(
            'class' => $class,
            'path'  => $path
        ));
    }

}
