<?php

class TagField extends FormField {

    public $type = 'TagField';
    public $name = '';
    public $value = '';
    public $suggestion = 'none';
    public $sugPHP = '';
    public $label = '';
    public $layout = 'Vertical';
    public $labelWidth = 4;
    public $fieldWidth = 8;
    public $mustChoose = 'yes';
    public $valueMode = 'string';
    public $valueModeDelimiter = ',';
    public $options = [];
    public $tagMapper = '';
    public $unique = 'yes';
    public $ref = '';
    public $tagMapperMode = 'none';
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
        return ['tag-field.js'];
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
    public function parseDropdown($php) {
        if ($php != "") {
            if (FormField::$inEditor) {
                return [];
            }

            ## evaluate expression
            $list = $this->evaluate($php, true);

            if (is_array($list) && !Helper::is_assoc($list)) {
                if (!is_array($list[0])) {
                    $list = Helper::toAssoc($list);
                }
            }
        } else if (is_array($list) && !Helper::is_assoc($list)) {
            $list = Helper::toAssoc($list);
        }
        

        return $list;
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
    
    public function actionMapTag() {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        
        if (count($post) == 0) { die(); }
        
        $fb = FormBuilder::load($post['m']);
        $ffilter = ['name' => $post['n']];
        if (isset($post['ref'])) {
            $ffilter['ref'] = $post['ref'];
        }
        $ff = $fb->findField($ffilter);
        
        if (trim($ff['tagMapper']) == '') {
            echo "{}";
        } else {
            $res = $this->evaluate($ff['tagMapper'], true,[
                'values' => @$post['v'],
                'labels' => @$post['l'],
                'model' => @$post['mdl'],
                'params' => @$post['prm']
            ]);
            echo json_encode($res);
        }
    }
    
    public function actionGetSug() {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        
        if (count($post) == 0) { die(); }
        
        $fb = FormBuilder::load($post['m']);
        $ffilter = ['name' => $post['n']];
        if (isset($post['ref'])) {
            $ffilter['ref'] = $post['ref'];
        }
        $ff = $fb->findField($ffilter);
        
        if ($ff['sugPHP'] != '') {
            $res = $this->evaluate($ff['sugPHP'], true,[
                'search' => @$post['s'],
                'model' => @$post['mdl'],
                'params' => @$post['prm']
            ]);
            echo json_encode($res);
        } else {
            echo "[]";
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
                'label' => 'Unique',
                'name' => 'unique',
                'options' => array (
                    'ng-model' => 'active.unique',
                    'ng-change' => 'save();',
                ),
                'listExpr' => 'array(\'yes\',\'no\')',
                'fieldWidth' => '3',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Ref Name',
                'name' => 'ref',
                'options' => array (
                    'ng-model' => 'active.ref',
                    'ng-change' => 'save();',
                ),
                'type' => 'TextField',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr/>',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Value Mode',
                        'name' => 'valueMode',
                        'options' => array (
                            'ng-model' => 'active.valueMode',
                            'ng-change' => 'save();',
                        ),
                        'listExpr' => '[\'string\', \'array\']',
                        'labelWidth' => '6',
                        'fieldWidth' => '6',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'label' => 'Delimiter',
                        'name' => 'valueModeDelimiter',
                        'labelWidth' => '5',
                        'fieldWidth' => '7',
                        'options' => array (
                            'ng-show' => 'active.valueMode == \'string\'',
                            'ng-model' => 'active.valueModeDelimiter',
                            'ng-change' => 'save();',
                        ),
                        'type' => 'TextField',
                    ),
                ),
                'w1' => '60%',
                'w2' => '40%',
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr style=\\"margin-top:3px\\" />',
            ),
            array (
                'label' => 'Tag Mapper',
                'name' => 'tagMapperMode',
                'options' => array (
                    'ng-model' => 'active.tagMapperMode',
                    'ng-change' => 'save()',
                ),
                'menuPos' => 'pull-right',
                'list' => array (
                    'none' => 'None',
                    '---' => '---',
                    'insert' => 'Active, Insert unmapped tags',
                    'remove' => 'Active, Remove unmapped tags',
                ),
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Tag Mapper Function',
                'fieldname' => 'tagMapper',
                'options' => array (
                    'ng-if' => 'active.tagMapperMode != \'none\'',
                ),
                'desc' => 'Each value in tag can be maped to its label. <br/>This function will be called when there is a tag that has not been mapped.
<hr style=\"margin:5px 0px -10px\"/> 
<br/>Use $values to get the unmapped values.
<br/>Use $labels to get the unmapped labels.
<br/>Use $model to get current model value.
<br/>Use $params to get current params value.',
                'type' => 'ExpressionField',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr/>',
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
                'label' => 'Suggestion',
                'name' => 'dropdown',
                'options' => array (
                    'ng-model' => 'active.suggestion',
                    'ng-change' => 'save();',
                ),
                'list' => array (
                    'none' => 'None',
                    '---' => '---',
                    'php' => 'PHP Function',
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
                    'ng-if' => 'active.suggestion == \'php\'',
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
                'value' => '<div ng-if=\"active.suggestion == \'rel\'\">
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
<div ng-if=\"active.suggestion  == \'php\'\">
<hr/>',
            ),
            array (
                'label' => 'List Expression',
                'fieldname' => 'sugPHP',
                'desc' => 'Use $search to get current search text.
<br/>Use $model to get current model value.
<br/>Use $params to get current params value.',
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