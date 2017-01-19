<?php
use PhpParser\Error;
use PhpParser\ParserFactory;
class SqlCriteria extends FormField {

    /** @var string $toolbarName */
    public static $toolbarName = "Sql Criteria";

    /** @var string $category */
    public static $category = "Data & Tables";

    /** @var string $toolbarIcon */
    public static $toolbarIcon  = "fa fa-database";
    public        $name         = '';
    public        $label        = '';
    public        $paramsField  = '';
    public        $params       = [];
    public        $baseClass    = '';
    public        $value        = [];
    public        $options      = [];
    public        $modelClassJS = ''; //digunakan untuk menggenerate Preview SQL

    public function getFieldProperties() {        
        return array(
            array(
                'label' => 'Base Class',
                'name' => 'baseClass',
                'options' => array(
                    'ng-model' => 'active.baseClass',
                    'ng-change' => 'save();',
                ),
                'listExpr' => 'array(\'DataSource\',\'RelationField\',\'DataGrid\', \'DataFilter\');',
                'type' => 'DropDownList',
            ),
            array(
                'label' => 'Criteria Field',
                'name' => 'name',
                'options' => array(
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                ),
                'type' => 'DropDownList',
            ),
            array(
                'label' => 'Params Field',
                'name' => 'paramsField',
                'options' => array(
                    'ng-model' => 'active.paramsField',
                    'ng-change' => 'save()',
                    'ps-list' => 'modelFieldList',
                ),
                'type' => 'DropDownList',
            ),
            array(
                'label' => 'Label',
                'name' => 'label',
                'options' => array(
                    'ng-model' => 'active.label',
                    'ng-change' => 'save();',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array(
                'label' => 'ModelClassJS',
                'name' => 'modelClassJS',
                'options' => array(
                    'ng-model' => 'active.modelClassJS',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array(
                'type' => 'Text',
                'value' => '<pre style=\"color:#999;font-size:11px;padding:6px;\"><i class=\"fa fa-info-circle\"></i> be sure to set $scope.modelClass in ModelClassJS File
</pre>',
            ),
            array(
                'label' => 'Options',
                'name' => 'options',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    public function includeJS() {
        return ['sql-criteria.js'];
    }

    public function actionPreviewSQL() {
        $postdata  = file_get_contents("php://input");
        $post      = json_decode($postdata, true);
        $criteria  = @$post['criteria'] ? $post['criteria'] : [];
        $params    = @$post['params'] ? $post['params'] : [];
        $baseClass = $post['baseclass'];

        switch ($baseClass) {
            case "DataGrid":
            case "DataFilter":
            case "RelationField":
            case "TextField":
                $rel            = 'currentModel';
                $name           = $post['rfname'];
                $classPath      = $post['rfclass'];
                $modelClassPath = $post['rfmodel'];

                $modelClass = Helper::explodeLast(".", $modelClassPath);
                Yii::import($modelClassPath);

                $class = Helper::explodeLast(".", $classPath);
                Yii::import($classPath);

                $model   = new $modelClass;
                $builder = $model->commandBuilder;

                $fb                   = FormBuilder::load($classPath);
                $field                = $fb->findField(['name' => $name]);
                $rf                   = new RelationField();
                $rf->modelClass       = $modelClassPath;
                $rf->builder          = $fb;
                $rf->attributes       = $field;
                $rf->relationCriteria = $criteria;

                if (isset($post['params'])) {
                    $rf->params = $post['params'];
                }
                
                $criteria = $rf->generateCriteria('', []);
                $criteria = new CDbCriteria($criteria);

                break;
            case "DataSource":
                $rel       = $post['rel'];
                $name      = $post['dsname'];
                $classPath = $post['dsclass'];

                $class = Helper::explodeLast(".", $classPath);
                Yii::import($classPath);

                $model   = new $class;
                $builder = $model->commandBuilder;

                $fb        = FormBuilder::load($classPath);
                $fb->model = new $model;

                $field          = $fb->findField(['name' => $name]);
                $ds             = new DataSource();
                $ds->attributes = $field;

                $criteria = DataSource::generateCriteria($params, $criteria, $ds);
                $criteria = SqlCriteria::convertPagingCriteria($criteria);
                $criteria = new CDbCriteria($criteria);

                break;
        }

        if (!isset($rel)) {
            echo json_encode([
                "sql" => '',
                "error" => ''
            ]);
            return false;
        }

        $isRelated = false;
        if ($rel == 'currentModel') {
            $tableSchema = $model->tableSchema;
        } else {
            $parent   = $model::model()->find();
            $relMeta  = $model->getMetadata()->relations[$rel];
            $relClass = $relMeta->className;

            if (!is_subclass_of($relClass, 'ActiveRecord')) {
                throw new CException("Class $relClass harus merupakan subclass dari ActiveRecord");
            }
            $tableSchema = $relClass::model()->tableSchema;

            if (!is_null($parent)) {
                $parentPrimaryKey = $parent->metadata->tableSchema->primaryKey;
                switch (get_class($relMeta)) {
                    case 'CHasOneRelation':
                    case 'CBelongsToRelation':
                        if (is_string($relMeta->foreignKey)) {
                            $criteria->addColumnCondition([$model->quoteCol($relMeta->foreignKey) => $parent->{$parentPrimaryKey}]);
                            $isRelated = true;
                        }
                        break;
                    case 'ManyManyRelation':
                        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
                        $stmts       = $parser->parse('<?php ' . $relMeta->foreignKey . ';');
                        $bridgeTable = $stmts[0]->name->parts[0];
                        $arg0        = $stmts[0]->args[0]->value->name->parts[0];
                        $arg1        = $stmts[0]->args[1]->value->name->parts[0];
                        $criteria->join .= " " . $relMeta->joinType . " {$bridgeTable} ON t.{$tableSchema->primaryKey} = {$bridgeTable}.$arg1 ";
                        break;
                    case 'CHasManyRelation':
                        //without through
                        if (is_string($relMeta->foreignKey)) {
                            $criteria->addColumnCondition([$model->quoteCol($relMeta->foreignKey) => $parent->{$parentPrimaryKey}]);
                            $isRelated = true;
                        }
                        
                        //with through
                        //todo..
                        break;
                }
            }
        }

        $command     = $builder->createFindCommand($tableSchema, $criteria);
        $commandText = $command->text;
        if ($isRelated) {
            if (!is_null($parent) && get_class($relMeta) == 'CHasManyRelation') { 
                $commandText = str_replace(":ycp0", "\n" . '"{{model.' . $parentPrimaryKey . '}}"', $commandText);
            } else {
                $commandText = str_replace(":ycp0", "\n" . '"{{model.' . $relMeta->foreignKey . '}}"', $commandText);
            }
        }
        $commandText = SqlFormatter::highlight($commandText);

        $errMsg = '';
        try {
            $command->queryScalar();
        } catch (Exception $e) {
            $errMsg = $e->getMessage();
            $errMsg = str_replace("CDbCommand gagal menjalankan statement", "", $errMsg);
        }

        echo json_encode([
            "sql" => $commandText,
            "error" => $errMsg
        ]);
    }

    public static function convertPagingCriteria($criteria) {
        if (isset($criteria['paging'])) {
            if (is_array(@$criteria['paging']) && count($criteria) == 2) {
                $criteria['page']     = $criteria['paging']['currentPage'];
                $criteria['pageSize'] = $criteria['paging']['pageSize'];
            } else if (is_string($criteria['paging'])) {
                $criteria['page']     = 1;
                $criteria['pageSize'] = 25;
            }
            unset($criteria['paging']);
        }

        if (!isset($criteria['limit'])) {
            if (isset($criteria['page']) && isset($criteria['pageSize'])) {

                $criteria['limit']  = $criteria['pageSize'];
                $criteria['offset'] = ($criteria['page'] - 1) * $criteria['pageSize'];

                unset($criteria['pageSize'], $criteria['page']);
            }
        } else {
            if (isset($criteria['page']))
                unset($criteria['page']);
            if (isset($criteria['pageSize']))
                unset($criteria['pageSize']);
            if (isset($criteria['paging']))
                unset($criteria['paging']);
        }


        return $criteria;
    }

    public function getInlineJS() {
        $script    = '';
        $reflector = new ReflectionClass(get_class($this->model));
        $fn        = dirname($reflector->getFileName());
        $jsfile    = realpath($fn . "/" . $this->modelClassJS);

        if (is_file($jsfile)) {
            $js = file_get_contents($jsfile);
            return $js;
        }

        return '';
    }

    public function render() {
        $this->options['id']   = $this->renderID;
        $this->options['name'] = $this->renderName;
        $this->addClass('field-box');

        $this->setDefaultOption('ng-model', "model['{$this->originalName}']", $this->options);

        return $this->renderInternal('template_render.php');
    }

}