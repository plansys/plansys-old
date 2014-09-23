<?php

class TodoWidget extends Widget {

    public $icon = "fa fa-check-square-o fa-2x ";
    public $badge = '';

    public function includeJS() {
        return array(
            'todo-widget.js'
        );
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
