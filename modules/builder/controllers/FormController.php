<?php

class FormController extends Controller {

    private $vpath = 'application.modules.builder.views.1_form';

    public function getViewPath() {
        parent::getViewPath();
        return Yii::getPathOfAlias($this->vpath);
    }

    public function actionTree() {
        FormBuilder::renderUI('TreeView', [
            'name'    => 'formtree',
            'initUrl' => Yii::app()->createUrl('/builder/form/treeInit'),
        ]);
    }

    public function actionTreeInit() {
        echo json_encode([
            [
                'icon'  => 'fa fa-folder-o',
                'title' => 'Hello world',
                'items' => [
                    [
                        'icon'  => 'fa fa-file-',
                        'title' => 'Hello world'
                    ],
                    [
                        'icon'  => 'fa fa-folder-o',
                        'title' => 'Hello world'
                    ]
                ]
            ],
        ]);
    }

    public function actionEditor() {
//        echo $this->renderPartial("index");
    }

    public function actionProperties() {
        echo "Editor";
    }

}
