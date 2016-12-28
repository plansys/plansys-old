<?php

class TagField extends FormField {

    public $type = 'TagField';
    public $name = '';
    public $value = '';
    public $dropdown = 'none';
    public $label = '';
    public $layout = 'Vertical';
    public $labelWidth = 4;
    public $fieldWidth = 8;
    public $mustChoose = 'yes';
    public $options = [];
    public $labelOptions = [];
    public $fieldOptions = [];
    public static $toolbarName = "Tag Field";
    public static $category = "User Interface";
    public static $toolbarIcon = "fa fa-tags";
    
    public $modelClass = '';
    public $params = [];
    public $criteria = [
        'select'    => '',
        'distinct'  => 'true',
        'alias'     => 't',
        'condition' => '{[search]}',
        'order'     => '',
        'group'     => '',
        'having'    => '',
        'join'      => ''
    ];
    public $idField = '';
    public $labelField = '';
    public $drPHP = "";
    public $drList = [];

    public function getLayoutClass() {
        return ($this->layout == 'Vertical' ? 'form-vertical' : '');
    }

    public function getErrorClass() {
        return (count($this->errors) > 0 ? 'has-error has-feedback' : '');
    }

    public function getlabelClass() {
        if ($this->layout == 'Vertical') {
            $class = "control-label col-sm-12";
        } else {
            $class = "control-label col-sm-{$this->labelWidth}";
        }

        $class .= @$this->labelOptions['class'];
        return $class;
    }

    public function getFieldColClass() {
        return "col-sm-" . $this->fieldWidth;
    }

    public function includeJS() {
        return ['horsey.js','insignia.js', 'tag-field.js'];
    }

    public function includeCSS() {
        return ['tag-field.css'];
    }

    public function includeEditorCSS() {
        return ['tag-field.css'];
    }

    /**
     * @return array me-return array hasil proses expression.
     */
    public function parseDropdown() {

        if ($this->drPHP != "") {
            if (FormField::$inEditor) {
                $this->drList = '';
                return ['list' => ''];
            }

            ## evaluate expression
            $this->drList = $this->evaluate($this->drPHP, true);

            if (is_array($this->drList) && !Helper::is_assoc($this->drList)) {
                if (!is_array($this->drList[0])) {
                    $this->drList = Helper::toAssoc($this->drList);
                }
            }
        } else if (is_array($this->drList) && !Helper::is_assoc($this->drList)) {
            $this->drList = Helper::toAssoc($this->drList);
        }
        

        return [
            'list' => $this->drList
        ];
    }

    public function actionRelnext() {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        
        if (count($post) == 0) { die(); }

        $start = @$post['i'];
        $fb = FormBuilder::load($post['m']);
        $ff = $fb->findField(['name' => $post['f']]);
        
        if ($ff['dropdown'] == 'rel') {
            $rf = new RelationField;
            $rf->params = $ff['params'];
            $rf->modelClass = $ff['modelClass'];
            $rf->relationCriteria = $ff['criteria'];
            $rf->relationCriteria['limit'] = 7;
            $rf->relationCriteria['offset'] = $start;

            $rf->idField = $ff['idField'];
            $rf->labelField = $ff['labelField'];
            $rf->builder = $this->builder;
            if (is_array($rf->params)) {
                foreach ($rf->params as $k => $ff) {
                    if (substr($ff, 0, 3) == "js:" && isset($post['p'][$k])) {
                        $rf->params[$k] = "'" . @$post['p'][$k] . "'";
                    }
                }
            }
            $list = [];
            $rawList = $rf->query(@$post['s'], $rf->params);
            $rawList = is_null($rawList) ? [] : $rawList;

            foreach ($rawList as $key => $val) {
                $list[] = [
                    'key' => $val['value'],
                    'value' => $val['label']
                ];
            }

            $count = $rf->count(@$post['s'], $rf->params);

            echo json_encode([
                'list' => $list,
                'count' => $count,
                's' => $post['s']
            ]);
        }
    }

    public function render() {
        $this->addClass('form-group form-group-sm', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->fieldOptions['id'] = $this->renderID;
        $this->fieldOptions['name'] = $this->renderName;
        $this->addClass('form-control', 'fieldOptions');
        $this->setDefaultOption('ng-model', "model['{$this->originalName}']", $this->options);

        if (!is_string($this->value))
            $this->value = json_encode($this->value);

        if ($this->dropdown == "normal") {
            $this->parseDropdown();
        }
        
        return $this->renderInternal('template_render.php');
    }

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
                'menuPos' => 'pull-right',
                'list' => array (),
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Label',
                'name' => 'label',
                'options' => array (
                    'ng-model' => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Layout',
                'name' => 'layout',
                'options' => array (
                    'ng-model' => 'active.layout',
                    'ng-change' => 'save();',
                ),
                'listExpr' => 'array(\'Horizontal\',\'Vertical\')',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Label Width',
                        'name' => 'labelWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options' => array (
                            'ng-model' => 'active.labelWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                            'ng-disabled' => 'active.layout == \'Vertical\'',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Field Width',
                        'name' => 'fieldWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => 12,
                        'fieldWidth' => '11',
                        'options' => array (
                            'ng-model' => 'active.fieldWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column3' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column4' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr/>',
            ),
            array (
                'label' => 'Dropdown',
                'name' => 'dropdown',
                'options' => array (
                    'ng-model' => 'active.dropdown',
                    'ng-change' => 'save();',
                ),
                'list' => array (
                    'none' => 'None',
                    '---' => '---',
                    'normal' => 'Normal',
                    'rel' => 'Relation',
                ),
                'fieldWidth' => '5',
                'otherLabel' => 'Other...',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Must Choose',
                'name' => 'mustChoose',
                'options' => array (
                    'ng-model' => 'active.mustChoose',
                    'ng-change' => 'save();',
                    'ng-if' => 'active.dropdown == \'normal\'',
                ),
                'list' => array (
                    'yes' => 'Yes',
                    'no' => 'No',
                ),
                'fieldWidth' => '4',
                'otherLabel' => 'Other...',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-if=\"active.dropdown == \'rel\'\">
<hr/>',
            ),
            array (
                'name' => 'TypeRelation',
                'subForm' => 'application.components.ui.FormFields.TextFieldRelation',
                'type' => 'SubForm',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>
<div ng-if=\"active.dropdown == \'normal\'\">
<hr/>',
            ),
            array (
                'label' => 'PHP Expression',
                'fieldname' => 'drPHP',
                'type' => 'ExpressionField',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>
<hr/>',
            ),
            array (
                'label' => 'Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Label Options',
                'name' => 'labelOptions',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Field Options',
                'name' => 'fieldOptions',
                'type' => 'KeyValueGrid',
            ),
        );
    }

}