<?php

class SqlCriteria extends FormField {

    /** @var string $toolbarName */
    public static $toolbarName = "Sql Criteria";

    /** @var string $category */
    public static $category = "Data & Tables";



    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-database";
    public $name = '';
    public $label = '';
    public $paramsField = '';
    public $params = array();
    public $baseClass = '';
    public $value = array();
    public $defaultValue = array(
        'distinct' => 'false',
        'alias' => 't',
        'condition' => '{[where]}',
        'order' => '{[order]}',
        'paging' => '{[paging]}',
        'group' => '',
        'having' => '',
        'join' => ''
    );
    
    public $options = array();
    public $modelClassJS = ''; //digunakan untuk menggenerate Preview SQL

    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Base Class',
                'name' => 'baseClass',
                'options' => array (
                    'ng-model' => 'active.baseClass',
                    'ng-change' => 'save();',
                ),
                'list' => array (),
                'listExpr' => 'array(\\\'DataSource\\\');',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Criteria Field',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                ),
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Params Field',
                'name' => 'paramsField',
                'options' => array (
                    'ng-model' => 'active.paramsField',
                    'ng-change' => 'save()',
                    'ps-list' => 'modelFieldList',
                ),
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Label',
                'name' => 'label',
                'options' => array (
                    'ng-model' => 'active.label',
                    'ng-change' => 'save();',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'ModelClassJS',
                'name' => 'modelClassJS',
                'options' => array (
                    'ng-model' => 'active.modelClassJS',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'value' => '<pre style=\"color:#999;font-size:11px;padding:6px;\"><i class=\"fa fa-info-circle\"></i> be sure to set $scope.modelClass in ModelClassJS File
</pre>',
                'type' => 'Text',
            ),
            array (
                'label' => 'Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    public function includeJS() {
        return array('sql-criteria.js');
    }

    public static function convertPagingCriteria($criteria) {
        if (is_array(@$criteria['paging']) && count($criteria) == 2) {
            $criteria['page'] = $criteria['paging']['currentPage'];
            $criteria['pageSize'] = $criteria['paging']['pageSize'];
            unset($criteria['paging']);
        }

        if (isset($criteria['page']) && isset($criteria['pageSize'])) {

            $criteria['limit'] = $criteria['pageSize'];
            $criteria['offset'] = ($criteria['page'] - 1) * $criteria['pageSize'];

            unset($criteria['pageSize'], $criteria['page']);
        }

        return $criteria;
    }

    public function actionPreviewSQL() {
        $postdata = file_get_contents("php://input");
        $post = json_decode($postdata, true);
        $classPath = $post['class'];
        $criteria = $post['criteria'];
        $params = $post['params'];
        $baseClass = $post['baseclass'];

        $class = array_pop(explode(".", $classPath));
        Yii::import($classPath);

        $model = new $class;
        $tableSchema = $model->tableSchema;
        $builder = $model->commandBuilder;

        $fb = FormBuilder::load($class);
        $fb->model = $model;
        $this->builder = $fb;
        
        switch ($baseClass) {
            case "DataSource":
                $field = $this->builder->findField(array('name'=>$post['dsname']));
                $criteria = DataSource::generateCriteria(array(), $field);
                var_dump($criteria);
                die();
            break;
        }

        $command = $builder->createFindCommand($tableSchema, new CDbCriteria($criteria));

        echo $command->text;
    }

    public function getInlineJS() {
        $script = '';
        $reflector = new ReflectionClass(get_class($this->model));
        $fn = dirname($reflector->getFileName());
        $jsfile = realpath($fn . "/" . $this->modelClassJS);
        
        if (is_file($jsfile)) {
            $js = file_get_contents($jsfile);
            return $js;
        }

        return '';
    }

    public function render() {
        if (count($this->value) == 0) {
            $this->value = $this->defaultValue;
        }
        
        $this->options['id'] = $this->renderID;
        $this->options['name'] = $this->renderName;
        $this->addClass('field-box');
        
        $this->setDefaultOption('ng-model', "model.{$this->originalName}", $this->options);

        return $this->renderInternal('template_render.php');
    }

}