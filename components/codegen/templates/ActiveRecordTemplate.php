<?php

class ActiveRecordTemplate extends CComponent {

    public static function generateFields($model) {
        $array     = $model->modelFieldList;
        $classPart = explode("-", Helper::camelToSnake(get_class($model)));
        $lastPart  = count($classPart) - 1;
        $params    = [
            'array' => $array,
            'columns' => $model->tableSchema->columns,
            'primaryKey' => $model->tableSchema->primaryKey,
            'classPart' => $classPart,
            'length' => count($array),
            'basic' => lcfirst(implode("", array_map('ucfirst', $classPart))),
            'basicTitle' => Helper::camelToSpacedCamel(implode("", array_map('ucfirst', $classPart)))
        ];
        if (!empty($classPart) && ($classPart[$lastPart] == "form" || $classPart[$lastPart] == "index")) {
            $params['type'] = array_pop($classPart);
        } else {
            $params['type'] = "";
        }
        $params['module'] = array_shift($classPart);
        $type             = $params['type'];
        $return           = [];

        if ($type == "index") {
            self::generateIndex($return, $params);
        } else {
            self::generateForm($return, $params);
        }

        return $return;
    }

    private static function generateForm(&$return, $params) {
        extract($params);
        $column1  = [];
        $column2  = [];
        $array_id = null;
        foreach ($array as $k => $i) {
            if ($array[$k]['name'] == $primaryKey) {
                $array_id = $array[$k];
                continue;
            }
            switch (true) {
                case $columns[$array[$k]['name']]->dbType == "date":
                    $array[$k]['type']      = "DateTimePicker";
                    $array[$k]['fieldType'] = "date";
                    break;
                case $columns[$array[$k]['name']]->dbType == "datetime":
                    $array[$k]['type']      = "DateTimePicker";
                    $array[$k]['fieldType'] = "datetime";
                    break;
                case $columns[$array[$k]['name']]->dbType == "timestamp":
                    $array[$k]['type']      = "DateTimePicker";
                    $array[$k]['fieldType'] = "datetime";
                    break;
                case $columns[$array[$k]['name']]->dbType == "time":
                    $array[$k]['type']      = "DateTimePicker";
                    $array[$k]['fieldType'] = "time";
                    break;
                case substr($i['name'], -3) == "_id":
                    ## get class name
                    $relName            = substr($i['name'], 0, strlen($i['name']) - 3);
                    $array[$k]['label'] = $relName;

                    $relName = implode("", array_map('ucfirst', explode("_", $relName)));

                    if (@is_subclass_of($relName, 'ActiveRecord')) {

                        ## get class alias
                        if (is_file(Yii::getPathOfAlias('app.models.' . $relName) . ".php")) {

                            $classAlias = "app.models." . $relName;
                        } else if (is_file(Yii::getPathOfAlias('application.models.' . $relName) . ".php")) {
                            $classAlias = "application.models." . $relName;
                        } else {
                            $classAlias = '';
                        }

                        if ($classAlias != '') {
                            ## fill attribute
                            $array[$k]['type']       = "RelationField";
                            $array[$k]['modelClass'] = $classAlias;
                            $array[$k]['idField']    = 'id';
                            $attr                    = $relName::model()->attributes;

                            ## fill label field
                            if (array_key_exists('name', $attr)) {
                                $array[$k]['labelField'] = 'name';
                            } else if (array_key_exists('nama', $attr)) {
                                $array[$k]['labelField'] = 'nama';
                            } else {
                                foreach ($attr as $y => $z) {
                                    if ($y == 'id')
                                        continue;
                                    if (substr($y, -3) == "_id")
                                        continue;

                                    $array[$k]['labelField'] = $y;
                                    break;
                                }
                            }
                        }
                    }

                    break;
            }

            $array[$k]['label'] = implode(" ", array_map('ucfirst', explode("_", $array[$k]['label'])));

            if ($k < $length / 2) {
                $column1[] = $array[$k];
            } else {
                $column2[] = $array[$k];
            }
        }

        $column1[] = '<column-placeholder></column-placeholder>';
        $column2[] = '<column-placeholder></column-placeholder>';
        $return[]  = [
            'linkBar' => [
                [
                    'label' => 'Kembali',
                    'buttonType' => 'default',
                    'icon' => '',
                    'options' => [
                        'href' => "url:/{$module}/{$basic}/index",
                    ],
                    'type' => 'LinkButton',
                ],
                [
                    'label' => 'Simpan',
                    'buttonType' => 'success',
                    'icon' => '',
                    'options' => [
                        'ng-click' => "form.submit(this)",
                    ],
                    'type' => 'LinkButton',
                ],
                [
                    'type' => 'Text',
                    'value' => '<div ng-if="!isNewRecord" class="separator"></div>',
                    'renderInEditor' => 'Yes',
                ],
                [
                    'label' => 'Hapus',
                    'buttonType' => 'danger',
                    'icon' => '',
                    'options' => [
                        'ng-if' => '!isNewRecord',
                        'href' => "url:/{$module}/{$basic}/delete?id={model.id}",
                        'confirm' => 'Apakah Anda Yakin ?'
                    ],
                    'type' => 'LinkButton',
                ],
            ],
            'title' => '{{ isNewRecord ? \'Tambah ' . $basicTitle . '\' : \'Update ' . $basicTitle . '\'}}',
            'showSectionTab' => 'Yes',
            'type' => 'ActionBar',
        ];

        if (!is_null($array_id)) {
            $return[] = $array_id;
        }

        $return[] = [
            'type' => 'ColumnField',
            'column1' => $column1,
            'column2' => $column2
        ];
        return $return;
    }

    private static function generateIndex(&$return, $params) {
        extract($params);
        $return[] = [
            'linkBar' => [
                [
                    'label' => 'Tambah ' . $basicTitle,
                    'buttonType' => 'success',
                    'icon' => 'plus',
                    'options' => [
                        'href' => "url:/{$module}/{$basic}/new",
                    ],
                    'type' => 'LinkButton',
                ],
            ],
            'title' => 'Daftar ' . $basicTitle,
            'showSectionTab' => 'No',
            'type' => 'ActionBar',
        ];

        ## generate Filters & Columns

        foreach ($array as $k => $i) {
            $filter = [
                'filterType' => "string"
            ];
            $column = [
                'columnType' => "string",
                'options' => [],
            ];
            switch (true) {
                case $columns[$array[$k]['name']]->dbType == "date" :
                    $filter['filterType'] = "date";
                    $column['inputMask']  = "99/99/9999";
                    break;
                case $columns[$array[$k]['name']]->dbType == "datetime" :
                    $column['inputMask'] = "99/99/9999 99:99";
                    break;
                case $columns[$array[$k]['name']]->dbType == "time" :
                    $column['inputMask'] = "99:99";
                    break;
                case substr($i['name'], -3) == "_id":
                    ## get class name
                    $relName    = substr($i['name'], 0, strlen($i['name']) - 3);
                    $i['label'] = $relName;

                    $relName = implode("", array_map('ucfirst', explode("_", $relName)));


                    if (@is_subclass_of($relName, 'ActiveRecord')) {

                        ## get class alias
                        if (is_file(Yii::getPathOfAlias('app.models.' . $relName) . ".php")) {

                            $classAlias = "app.models." . $relName;
                        } else if (is_file(Yii::getPathOfAlias('application.models.' . $relName) . ".php")) {
                            $classAlias = "application.models." . $relName;
                        } else {
                            $classAlias = '';
                        }

                        if ($classAlias != '') {
                            ## fill attribute
                            $filter['filterType']    = "relation";
                            $filter['relModelClass'] = $classAlias;
                            $filter['relIdField']    = 'id';
                            $filter['relParams']     = [];
                            $filter['relCriteria']   = array(
                                'select' => '',
                                'distinct' => 'false',
                                'alias' => 't',
                                'condition' => '{[search]}',
                                'order' => '',
                                'group' => '',
                                'having' => '',
                                'join' => '',
                            );
                            $attr                    = $relName::model()->attributes;

                            ## fill label field
                            if (array_key_exists('name', $attr)) {
                                $filter['relLabelField'] = 'name';
                            } else if (array_key_exists('nama', $attr)) {
                                $filter['relLabelField'] = 'nama';
                            } else {
                                foreach ($attr as $y => $z) {
                                    if ($y == 'id')
                                        continue;
                                    if (substr($y, -3) == "_id")
                                        continue;

                                    $filter['relLabelField'] = $y;
                                    break;
                                }
                            }
                            $column               = $filter;
                            $column['columnType'] = "relation";
                            unset($column['filterType']);
                        }
                    }
                    break;
            }

            $i['label'] = implode(" ", array_map('ucfirst', explode("_", $i['label'])));

            $filter['name']  = $i['name'];
            $filter['label'] = $i['label'];
            $column['name']  = $i['name'];
            $column['label'] = $i['label'];

            $filters[] = $filter;
            $cols[]    = $column;
        }

        $return[] = [
            'name' => 'dataFilter1',
            'datasource' => 'dataSource1',
            'type' => 'DataFilter',
            'filters' => $filters
        ];
        $return[] = [
            'name' => 'dataSource1',
            'params' => [
                'where' => 'dataFilter1',
                'paging' => 'dataGrid1',
                'order' => 'dataGrid1',
            ],
            'relationTo' => 'currentModel',
            'type' => 'DataSource',
        ];
        $return[] = [
            'name' => 'dataGrid1',
            'datasource' => 'dataSource1',
            'type' => 'GridView',
            'columns' => $cols
        ];
        return $return;
    }

}
