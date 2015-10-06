<?php

class ModelGeneratorController extends Controller {

    public function actionEmpty() {
        $this->layout = "//layouts/blank";
        $this->render('empty');
    }

    public function actionIndex() {
        $models = ModelGenerator::listAllFile();
        $this->render('index', array(
            'models' => $models,
        ));
    }

    public function actionRenderProperties() {
        $properties = FormBuilder::load('DevModelEditor');

        if ($this->beginCache('DevModelProperties', array(
            'dependency' => new CFileCacheDependency(
                Yii::getPathOfAlias('application.modules.dev.forms.DevModelEditor') . ".php"
            )))
        ) {
            echo $properties->render();
            $this->endCache();
        }
    }

    public function actionUpdate($class, $type) {
        $this->layout = "//layouts/blank";
        $className    = Helper::explodeLast('.', $class);
        $model        = new ModelGenerator($className, $type);
        $modelDetail  = $model->modelInfo;

        $this->render('form', array(
            'class' => $class,
            'type' => $type,
            'modelDetail' => $modelDetail,
        ));
    }

}

?>