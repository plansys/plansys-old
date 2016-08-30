<?php

/**
 * Class CheckboxList
 * @author rizky
 */
class ListView extends FormField {
    public static $toolbarName        = "List View";
    public static $category           = "Data & Tables";
    public static $toolbarIcon        = "glyphicon glyphicon-align-justify";
    public        $name               = '';
    public        $fieldTemplate      = 'datasource';
    public        $templateForm       = '';
    public        $value              = '';
    public        $layout             = 'Horizontal';
    public        $label              = '';
    public        $labelWidth         = 0;
    public        $minItem            = 0;
    public        $inlineJS           = '';
    public        $fieldWidth         = 12;
    public        $datasource         = '';
    public        $options            = [];
    public        $fieldOptions       = [];
    public        $labelOptions       = [];
    public        $sortable           = 'yes';
    public        $deletable          = 'Yes';
    public        $insertable         = 'Yes';
    public        $singleView         = 'TextField';
    public        $singleViewOption   = null;
    public        $subRels            = [];
    protected     $renderTemplateForm;
    protected     $templateAttributes = [];
    

    /**
     * @return array me-return array property TextField.
     */
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'ListView Mode',
                'name' => 'fieldTemplate',
                'options' => array (
                    'ng-model' => 'active.fieldTemplate',
                    'ng-change' => 'changeListViewMode();',
                ),
                'list' => array (
                    'datasource' => 'SubForm + DataSource',
                    'form' => 'SubForm',
                    'default' => 'FormField',
                ),
                'otherLabel' => 'Other...',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr>',
            ),
            array (
                'label' => 'Label',
                'name' => 'label',
                'options' => array (
                    'ng-model' => 'active.label',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Field Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                    'ng-if' => 'active.fieldTemplate != \'datasource\'',
                ),
                'list' => array (),
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Field Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ng-delay' => '500',
                    'ng-if' => 'active.fieldTemplate == \'datasource\'',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Data Source',
                'name' => 'datasource',
                'options' => array (
                    'ng-model' => 'active.datasource',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                    'ps-list' => 'dataSourceList',
                    'ng-if' => 'active.fieldTemplate == \'datasource\'',
                ),
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'SubForm',
                'name' => 'templateForm',
                'options' => array (
                    'ng-model' => 'active.templateForm',
                    'ng-show' => 'active.fieldTemplate == \'form\' || active.fieldTemplate == \'datasource\'',
                    'ng-change' => 'save();',
                ),
                'menuPos' => 'pull-right',
                'listExpr' => 'FormBuilder::listForm()',
                'searchable' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-show=\'active.fieldTemplate == \"default\"\'>
    <div style=\'margin:10px 0px 10px 10px;border:1px solid #ccc;padding:5px 5px 0px 5px;border-radius:4px;\'>',
            ),
            array (
                'label' => 'Field Type',
                'name' => 'singleView',
                'options' => array (
                    'ng-change' => 'activeEditor.fieldTypeChange(active)',
                    'ng-model' => 'active.singleView',
                ),
                'listExpr' => '[\'TextField\',\'DropDownList\',\'RelationField\']',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Edit',
                'icon' => 'pencil',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin-top:-32px;',
                    'ng-click' => 'activeEditor.toggleEdit(active)',
                ),
                'type' => 'LinkButton',
            ),
            array (
                'type' => 'Text',
                'value' => '    <div
    ng-if=\'active.edited\'
    class=\'list-view-single-edit\'
    style=\'border-top:1px solid #ccc;margin:0px -5px;padding:5px 5px 0px 5px;\'>',
            ),
            array (
                'name' => 'singleViewOption',
                'mode' => 'single',
                'subForm' => 'application.components.ui.FormFields.TextField',
                'options' => array (
                    'ng-if' => 'active.singleView == \'TextField\'',
                    'ng-model' => 'active.singleViewOption',
                ),
                'type' => 'SubForm',
            ),
            array (
                'name' => 'singleViewOption',
                'mode' => 'single',
                'subForm' => 'application.components.ui.FormFields.DropDownList',
                'options' => array (
                    'ng-if' => 'active.singleView == \'DropDownList\'',
                    'ng-model' => 'active.singleViewOption',
                ),
                'type' => 'SubForm',
            ),
            array (
                'type' => 'Text',
                'value' => '    </div>
</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '</div><div ng-show=\'active.fieldTemplate == \\"form\\" || active.fieldTemplate == \\"datasource\\"\'>',
            ),
            array (
                'label' => 'Edit Subform',
                'icon' => 'sign-in',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin:0px 0px 5px 0px;',
                    'href' => 'url:/dev/forms/update?class={active.templateForm}',
                    'target' => '_blank',
                    'ng-if' => 'active.templateForm != \'\'',
                ),
                'type' => 'LinkButton',
            ),
            array (
                'type' => 'Text',
                'value' => '<div class=\"clearfix\"></div>
<hr/></div>',
            ),
            array (
                'label' => 'Inline JS',
                'name' => 'inlineJS',
                'options' => array (
                    'ng-model' => 'active.inlineJS',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<a ng-href=\"{{Yii.app.createUrl(\'/dev/forms/code\', {
    c: classPath,
    s: active.inlineJS
})}}\" 
    style=\"margin-bottom:5px;\"
    class=\"pull-right btn btn-default btn-xs\">
    <i class=\"fa fa-sign-in\"></i>
    Edit Inline JS
</a>
<div class=\"clearfix\"></div>',
            ),
            array (
                'label' => 'Sortable',
                'name' => 'sortable',
                'options' => array (
                    'ng-model' => 'active.sortable',
                    'ng-change' => 'save();',
                ),
                'list' => array (
                    'yes' => 'Yes',
                    'No' => 'No',
                ),
                'fieldWidth' => '5',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Deletable',
                'name' => 'deletable',
                'options' => array (
                    'ng-model' => 'active.deletable',
                    'ng-change' => 'save();',
                ),
                'listExpr' => '[\'Yes\',\'No\']',
                'fieldWidth' => '5',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Insertable',
                'name' => 'insertable',
                'options' => array (
                    'ng-model' => 'active.insertable',
                    'ng-change' => 'save();',
                ),
                'listExpr' => '[\'Yes\',\'No\']',
                'fieldWidth' => '5',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Minimum Item',
                'name' => 'minItem',
                'fieldType' => 'number',
                'fieldWidth' => '3',
                'options' => array (
                    'ng-model' => 'active.minItem',
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

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['list-view.js'];
    }

    /**
     * getLayoutClass
     * Fungsi ini akan mengecek nilai property $layout untuk menentukan nama Class Layout
     * @return string me-return string Class layout yang digunakan
     */
    public function getLayoutClass() {
        return ($this->layout == 'Vertical' ? 'form-vertical' : '');
    }

    /**
     * @return string me-return string Class error jika terdapat error pada satu atau banyak attribute.
     */
    public function getErrorClass() {
        return (count($this->errors) > 0 ? 'has-error has-feedback' : '');
    }

    /**
     * getlabelClass
     * Fungsi ini akan mengecek $layout untuk menentukan layout yang digunakan
     * dan me-load option label dari property $labelOptions
     * @return string me-return string Class label
     */
    public function getlabelClass() {
        if ($this->layout == 'Vertical') {
            $class = "control-label col-sm-12";
        } else {
            $class = "control-label col-sm-{$this->labelWidth}";
        }

        return $class;
    }

    /**
     * getFieldColClass
     * Fungsi ini untuk menetukan width field
     * @return string me-return string class
     */
    public function getFieldColClass() {
        return "col-sm-" . $this->fieldWidth;
    }

    public function includeEditorJS() {
        return ['list-view-editor.js'];
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut checkboxlist dari hasil render
     */
    public function render() {
        $this->addClass('form-group form-group-sm flat', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->fieldOptions['ui-tree-node'] = '';
        $this->fieldOptions['ng-repeat']    = 'item in value';
        $this->fieldOptions['ng-init']      = 'initItem(value, $index)';
        $this->addClass('list-view-item', 'fieldOptions');

        Yii::import(FormBuilder::classPath($this->templateForm));
        $class = Helper::explodeLast(".", $this->templateForm);

        if (($this->fieldTemplate == 'form' || $this->fieldTemplate == 'datasource') && class_exists($class)) {
            $fb    = FormBuilder::load($class);
            $model = new $class();

            if ($this->value == "") {
                $this->value = [];
            }

            $this->templateAttributes = $model->attributes;
            $fb->model                = $model;
            $this->renderTemplateForm = $fb->render($model, ['wrapForm' => false]);
            
            
            ## find SubRelation in Child ListView
            $fields = $model->getFields();
            $dss = [];
            $this->subRels = [];
            foreach ($fields as $k=>$f) {
                if ($f['type'] == 'DataSource') {
                    $dss[$f['name']] = $f;
                }
            }
            foreach ($fields as $k=>$f) {
                if ($f['type'] == 'ListView') {
                    if (!isset($f['fieldTemplate']) || $f['fieldTemplate'] == 'datasource') {
                        if (@$f['datasource'] != '') {
                            $ds = @$dss[@$f['datasource']];
                            if (@$ds['relationTo'] != '' && @$ds['options']['watchModel'] == 'true') {
                                $this->subRels[$ds['relationTo']] = [
                                    'listview' => $f['name'],
                                    'datasource' => $ds['name']
                                ];
                            }
                        }
                    }
                }
            }
        } else if ($this->fieldTemplate == 'default') {
            $field                 = new $this->singleView;
            $field->attributes     = $this->singleViewOption;
            $field->renderID       = $this->name . rand(0, 10000);
            $field->builder        = $this->builder;
            $field->formProperties = $this->formProperties;

            $this->templateAttributes = ['val' => ''];
            $this->renderTemplateForm = $field->render();
        }

        $this->setDefaultOption('ng-model', "model['{$this->originalName}']", $this->options);

        $jspath = explode(".", FormBuilder::classPath($this->templateForm));
        array_pop($jspath);
        $jspath = implode(".", $jspath);

        $inlineJS = str_replace("/", DIRECTORY_SEPARATOR, trim($this->inlineJS, "/"));
        $inlineJS = Yii::getPathOfAlias($jspath) . DIRECTORY_SEPARATOR . $inlineJS;

        if (is_file($inlineJS)) {
            $inlineJS = file_get_contents($inlineJS);
        } else {
            $inlineJS = '';
        }
        
        return $this->renderInternal('template_render.php', ['inlineJS' => $inlineJS]);
    }

}