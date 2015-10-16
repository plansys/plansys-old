<?php

class DevCrudRelForm extends Form {

    public function getForm() {
        return array (
            'title' => 'Detail Crud Rel ',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => '',
        );
    }

    public function getFields() {
        return array (
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'label' => 'Relation Name',
                        'name' => 'name',
                        'options' => array (
                            'ps-list' => '$parent.relNameList',
                            'ng-model' => 'item.name',
                            'ng-change' => 'changeRelation(item, relationList[model.name])',
                        ),
                        'type' => 'DropDownList',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<div ng-if=\"!!model.name\">
    <pre ng-if=\"!!relationList[model.name].tableName\" style=\"
    padding:5px;
    margin:5px 1px 5px 5px;
    font-size:12px;
    user-select:text;
    -webkit-user-select:text;
    -moz-user-select:text;
    \">
TYPE  : {{ relationList[model.name].type }}
TABLE : {{ relationList[model.name].tableName }}
FK    : {{ relationList[model.name].foreignKey }}</pre>
    <pre class=\"alert alert-danger\" 
    ng-if=\"!relationList[model.name].tableName\" style=\"
    padding:5px;
    margin:5px 1px 5px 5px;
    text-align:center;
    font-size:12px;
    \"><i>{{ relationList[model.name].className }}</i> Model Not Found</pre>
</div>',
                    ),
                ),
                'column2' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'name' => 'BelongsTo',
                        'subForm' => 'application.modules.dev.forms.formbuilder.crud.DevCrudRelBelongsTo',
                        'options' => array (
                            'ng-if' => 'relationList[model.name].type == \'CBelongsToRelation\'',
                        ),
                        'type' => 'SubForm',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<div ng-if=\"false\">
    <pre>{{ model | json }}</pre>
    <pre>{{  relationList[model.name] | json }}</pre>
</div>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'options' => array (
                    'style' => 'margin-top:-5px;',
                ),
                'perColumnOptions' => array (
                    'style' => 'padding:5px 10px 0px 0px;',
                ),
                'type' => 'ColumnField',
            ),
        );
    }

}