<?php

class TodoWidget extends Widget {

    public $icon = "fa fa-check-square-o fa-2x ";
    public $badge = '';

    public function getExt() {
        $ext = glob($this->extDir . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR);
        foreach ($ext as $k => $e) {
            $ext[$k] = str_replace($this->extDir . DIRECTORY_SEPARATOR, '', $e);
        }
        return $ext;
    }

    public function getExtDir() {
        return Yii::getPathOfAlias('app.components.ui.Widgets.TodoWidget.ext');
    }

    public function renderCheck($ext, $file = 'template_check.php') {
        $path = $this->extDir . DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR . $file;
        ob_start();
        include($path);
        return ob_get_clean();
    }

    public function renderNote($ext, $file = 'template_render.php') {
        $path = $this->extDir . DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR . $file;
        ob_start();
        include($path);
        return ob_get_clean();
    }

    public function includeJS() {
        $files = [];
        foreach ($this->ext as $ext) {
            $path = $this->extDir . DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR;
            $glob = glob($path . "*.js");
            foreach ($glob as $f) {
                $files[] = str_replace($this->extDir, 'ext', $f);
            }
        }

        return array_merge([
            'todo-widget.js'
            ], $files);
    }

    public function getList() {
        $models = Todo::model()->findAllByAttributes(
            ['user_id' => Yii::app()->user->id], [
            'order' => 'id desc'
        ]);

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
        Todo::model()->deleteAllByAttributes([
            'user_id' => Yii::app()->user->id,
            'status' => 1
        ]);
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
