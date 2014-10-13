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
    public $value = array();
    public $defaultValue = array(
        'criteria' => array(
            'distinct' => 'false',
            'alias' => 't',
            'condition' => '{[where]}',
            'order' => '{[order]}',
            'paging' => '{[paging]}',
            'group' => '',
            'having' => '',
            'join' => ''
        ),
        'params' => array()
    );
    
    public $options = array();
    public $modelClassJS = ''; //digunakan untuk menggenerate Preview SQL

    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Field Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
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

    public function prepareCriteriaPreview($criteria) {
        if (isset($criteria['paging'])) {
            unset($criteria['paging']);
            
            $criteria['limit'] = 25;
            $criteria['offset'] = 1;
        }

        return $criteria;
    }

    public function actionPreviewSQL() {
        $postdata = file_get_contents("php://input");
        $post = json_decode($postdata, true);
        $classPath = $post['class'];
        $criteria = $post['criteria'];
        $class = array_pop(explode(".", $classPath));
        Yii::import($classPath);

        $model = new $class;
        $tableSchema = $model->tableSchema;
        $builder = $model->commandBuilder;
        
        $criteria = $this->prepareCriteriaPreview($criteria);
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