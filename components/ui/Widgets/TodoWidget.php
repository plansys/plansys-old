<?php

class TodoWidget extends Widget {

    public $icon = "fa fa-check-square-o fa-2x ";
    public $badge = '';
    public $ext = ['permintaan_data'];

    public function renderCheck($ext, $file = 'template_check.php') {

        $reflector = new ReflectionClass($this);
        $file = 'ext' . DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR . $file;
        $path = str_replace(".php", DIRECTORY_SEPARATOR . $file, $reflector->getFileName());
        ob_start();
        include($path);
        return ob_get_clean();
    }

    public function renderNote($ext, $file = 'template_render.php') {
        $reflector = new ReflectionClass($this);
        $file = 'ext' . DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR . $file;
        $path = str_replace(".php", DIRECTORY_SEPARATOR . $file, $reflector->getFileName());
        ob_start();
        include($path);
        return ob_get_clean();
    }

    public function includeJS() {
        $reflector = new ReflectionClass($this);
        $filename = $reflector->getFileName();
        $files = [];
        foreach ($this->ext as $ext) {
            $file = 'ext' . DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR;
            $path = str_replace(".php", DIRECTORY_SEPARATOR . $file, $filename);
            $glob = glob($path . "*.js");
            foreach ($glob as $f) {
                $files[] = $file . str_replace($path, '', $f);
            }
        }

        return array_merge(array(
            'todo-widget.js'
        ), $files);
    }

    public function getList() {
        $models = Todo::model()->findAllByAttributes(array('user_id' => Yii::app()->user->id));

        $array = ActiveRecord::toArray($models);
        foreach ($array as $k => $a) {
            foreach ($a as $i => $j) {
                if (is_array($j)) {
                    unset($array[$k][$i]);
                }
            }
        }


        return $array;
    }

    public function add($array) {
        $todo = new Todo();
        $todo->attributes = $array;
        $todo->user_id = Yii::app()->user->id;
        $todo->save();
        return $todo;
    }

    public function actionClear() {
        Todo::model()->deleteAllByAttributes(array(
            'user_id' => Yii::app()->user->id,
            'status' => 1
        ));
    }

    public function actionUpdate() {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        if (isset($post['id'])) {
            $model = Todo::model()->findByPk($post['id']);
            if ($post['note'] == '') {
                $model->delete();
                echo "{}";
                return true;
            }
        } else {
            $model = new Todo();
        }
        $model->attributes = $post;
        $model->user_id = Yii::app()->user->id;
        $model->save();
        echo json_encode($model->attributes);
    }

}
