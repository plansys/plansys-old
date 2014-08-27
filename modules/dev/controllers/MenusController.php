<?php

class MenusController extends Controller {

    public function actionIndex() {
        $menus = MenuTree::listAllFile();
        
        //print_r($menus); die;
        
        $this->render('index', array(
            'menus' => $menus
        ));
    }

    public function actionRenderProperties() {
        $properties = FormBuilder::load('DevMenuEditor');

        if ($this->beginCache('DevMenuProperties', array(
                    'dependency' => new CFileCacheDependency(
                            Yii::getPathOfAlias('application.modules.dev.forms.DevMenuEditor') . ".php"
            )))) {
            echo $properties->render();
            $this->endCache();
        } 
    }

    public function actionSave($class) {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);

        if (isset($post['list'])) {
            MenuTree::cleanMenuItems($post['list']);
            $code = "<?php \n\n return " . FormBuilder::formatCode($post['list'], '') . ";";
            file_put_contents(Yii::getPathOfAlias($class) . ".php", $code);
        }
    }

    public function actionRename() {

        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);

        $from = @$post['from'];
        $to = @$post['to'];

        if (!is_null($from) && !is_null($to)) {
            $path = explode(".", $from);
            array_pop($path);
            $path = implode(".", $path);
            $f = Yii::getPathOfAlias($from) . ".php";
            $t = Yii::getPathOfAlias($path . "." . $to) . ".php";

            if (file_exists($f)) {
                rename($f, $t);
            } else {
                file_put_contents($f, '<?php return array(); ');
            }
        }
    }

    public function actionDelete() {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);

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

    public function actionUpdate($class) {
        $this->layout = "//layouts/blank";

        $list = include(Yii::getPathOfAlias($class) . ".php");
        MenuTree::fillMenuItems($list);

        $path = $class;
        $class_path = explode(".", $class);
        $class = $class_path[count($class_path) - 1];
        $properties = FormBuilder::load('DevMenuEditor');
        $properties->registerScript();

        $this->render('form', array(
            'list' => $list,
            'class' => $class,
            'path' => $path
        ));
    }

}