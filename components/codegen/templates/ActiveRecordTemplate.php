<?php

class ActiveRecordTemplate extends CComponent {

    public static function generateFields($model) {
        $array         = $model->modelFieldList;
        $classPart     = explode("-", Helper::camelToSnake(get_class($model)));
        $lastPartIndex = count($classPart) - 1;
        $params        = [
            'array' => $array,
            'columns' => $model->tableSchema->columns,
            'primaryKey' => $model->tableSchema->primaryKey,
            'classPart' => $classPart,
            'length' => count($array),
            'model' => $model,
        ];
        if (!empty($classPart) && (in_array($classPart[$lastPartIndex], ["index", "form", "master", "relform"]))) {
            $params['type'] = array_pop($classPart);
        } else {
            $params['type'] = "";
        }

        $params['module']     = array_shift($classPart);
        $params['basicTitle'] = Helper::camelToSpacedCamel(implode("", array_map('ucfirst', $classPart)));
        $params['basic']      = lcfirst(implode("", array_map('ucfirst', $classPart)));

        $type   = $params['type'];
        $return = [];

        switch ($type) {
            case "index":
                self::generateIndex($return, $params);
                break;
            case "relform":
                if (isset($_SESSION['CrudGenerator'][get_class($model)]['relation']['type'])) {
                    self::generateRelform($return, $params);
                }
                break;
            case "form":
                self::generateForm($return, $params);
                break;
            case "master":
                self::generateMaster($return, $params);
                break;
        }

        return $return;
    }

    private static function generateIndex(&$return, $params) {
        $array      = [];
        $columns    = [];
        $model      = null;
        $basicTitle = "";
        $module     = '';
        $basic      = '';
        $primaryKey = '';
        extract($params);

        $generatorParams = [];
        if (isset($_SESSION['CrudGenerator']) && isset($_SESSION['CrudGenerator'][get_class($model)])) {
            $generatorParams = $_SESSION['CrudGenerator'][get_class($model)];
            unset($_SESSION['CrudGenerator'][get_class($model)]);
        }

        $prefixUrl = "{$basic}";
        if ($module != '' && $module != 'app' && $module != 'application') {
            $prefixUrl = "{$module}/" . $prefixUrl;
        }

        $linkBar = [
            [
                'label' => 'Tambah ' . $basicTitle,
                'buttonType' => 'success',
                'icon' => 'plus',
                'options' => [
                    'href' => "url:/{$prefixUrl}/new",
                ],
                'type' => 'LinkButton',
            ],
        ];

        if (@$generatorParams['bulkCheckbox'] == 'Yes') {
            array_unshift($linkBar,
                [
                    'label' => 'Delete Item(s)',
                    'buttonType' => 'danger',
                    'icon' => 'trash',
                    'options' => array(
                        'href' => 'url:' . $prefixUrl . '/delete&id={ gridView1.checkboxValues(\'chk\',\'' . $primaryKey . '\') }',
                        'ng-if' => 'gridView1.checkbox.chk.length > 0',
                    ),
                    'type' => 'LinkButton',
                ]);
        }

        $return[] = [
            'linkBar' => $linkBar,
            'title' => 'Daftar ' . $basicTitle,
            'showSectionTab' => 'No',
            'type' => 'ActionBar',
        ];

        $gv = new GridView;
        ## generate Filters & Columns
        foreach ($array as $k => $i) {
            if ($array[$k]['name'] == $primaryKey) {
                continue;
            }
            $i['label'] = implode(" ", array_map('ucfirst', explode("_", $i['label'])));
            $filter     = [
                'filterType' => "string",
                'name' => $i['name'],
                'label' => $i['label']
            ];
            $column     = [
                'columnType' => "string",
                'options' => [],
                'name' => $i['name'],
                'label' => $i['label']
            ];

            $filters[]     = $filter;
            $cols[]        = $column;
            $gv->columns[] = $column;
        }

        $editUrl               = "{$prefixUrl}/update";
        $editButtonCol         = [
            'name' => '',
            'label' => '',
            'columnType' => "string",
            'cellMode' => 'custom',
            'options' => [
                'mode' => 'edit-button',
                'editUrl' => "{$editUrl}&id={{row.{$primaryKey}}}"
            ]
        ];
        $gv->columns[]         = $editButtonCol;
        $editButtonCol['html'] = $gv->getRowTemplate($editButtonCol, count($cols));
        array_push($cols, $editButtonCol);

        if (@$generatorParams['bulkCheckbox'] == 'Yes') {
            $checkboxCol   = [
                'name' => 'chk',
                'label' => '',
                'options' => array(
                    'modifyDataSource' => 'false',
                ),
                'mergeSameRow' => '',
                'mergeSameRowWith' => '',
                'html' => '',
                'columnType' => 'checkbox',
                'show' => false,
                'checkedValue' => 'checked',
            ];
            $gv->columns[] = $checkboxCol;
            array_push($cols, $checkboxCol);
        } else {
            $delUrl               = "{$prefixUrl}/delete";
            $delButtonCol         = [
                'name' => '',
                'label' => '',
                'columnType' => "string",
                'cellMode' => 'custom',
                'options' => [
                    'mode' => 'del-url-button',
                    'editUrl' => "{$delUrl}&id={{row.{$primaryKey}}}"
                ]
            ];
            $gv->columns[]        = $delButtonCol;
            $delButtonCol['html'] = $gv->getRowTemplate($delButtonCol, count($cols));
            array_push($cols, $delButtonCol);
        }

        $return[] = [
            'name' => 'dataFilter1',
            'datasource' => 'dataSource1',
            'type' => 'DataFilter',
            'filters' => $filters
        ];

        if (!!$model->_softDelete) {
            $condition = $model->_softDelete['column'] . ' <> \'' . $model->_softDelete['value'] . '\' {AND [where]}';
        } else {
            $condition = '{[where]}';
        }
        $return[] = [
            'name' => 'dataSource1',
            'params' => [
                'where' => 'dataFilter1',
                'paging' => 'gridView1',
                'order' => 'gridView1',
            ],
            'relationTo' => 'currentModel',
            'relationCriteria' => array(
                'select' => '',
                'distinct' => 'false',
                'alias' => 't',
                'condition' => $condition,
                'order' => '{[order]}',
                'paging' => '{[paging]}',
                'group' => '',
                'having' => '',
                'join' => '',
            ),
            'type' => 'DataSource',
        ];
        $return[] = [
            'name' => 'gridView1',
            'datasource' => 'dataSource1',
            'type' => 'GridView',
            'columns' => $cols
        ];
        return $return;
    }

    private static function generateRelform(&$return, $params) {
        $array      = [];
        $primaryKey = '';
        $columns    = [];
        $length     = 0;
        $model      = null;
        $basicTitle = "";
        $module     = '';
        $basic      = '';
        extract($params);

        $generatorParams = [];
        if (isset($_SESSION['CrudGenerator']) && isset($_SESSION['CrudGenerator'][get_class($model)])) {
            $generatorParams = $_SESSION['CrudGenerator'][get_class($model)];
            unset($_SESSION['CrudGenerator'][get_class($model)]);
        }

        ## get basic model name
        $basic     = substr($basic, 0, strlen($generatorParams['relation']['name']) * -1);
        $prefixUrl = "{$basic}";
        if ($module != '' && $module != 'app' && $module != 'application') {
            $prefixUrl = "{$module}/" . $prefixUrl;
        }

        $column1  = [];
        $column2  = [];
        $array_id = null;
        foreach ($array as $k => $i) {
            if (isset($array[$k]['name'])) {
                if ($array[$k]['name'] == $primaryKey) {
                    $array_id = $array[$k];
                    continue;
                }
                if (isset($array[$k]['label'])) {
                    $array[$k]['label'] = ucfirst(implode(" ", array_map('ucfirst', explode("_", $array[$k]['label']))));
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
                        ## generate relation field
                        ## get class name
                        $relClassName       = substr($i['name'], 0, strlen($i['name']) - 3);
                        $array[$k]['label'] = str_replace("_", " ", ucfirst($relClassName));
                        $relClassName       = implode("", array_map('ucfirst', explode("_", $relClassName)));

                        if (@is_subclass_of($relClassName, 'ActiveRecord')) {
                            ## get class alias
                            if (is_file(Yii::getPathOfAlias('app.models.' . $relClassName) . ".php")) {
                                $classAlias = "app.models." . $relClassName;
                            } else if (is_file(Yii::getPathOfAlias('application.models.' . $relClassName) . ".php")) {
                                $classAlias = "application.models." . $relClassName;
                            } else {
                                $classAlias = '';
                            }

                            if ($classAlias != '') {
                                ## fill attribute
                                $array[$k]['type']       = "RelationField";
                                $array[$k]['modelClass'] = $classAlias;
                                $array[$k]['idField']    = $relClassName::model()->tableSchema->primaryKey;
                                $attr                    = $relClassName::model()->attributes;

                                ## fill label field
                                if (array_key_exists('name', $attr)) {
                                    $array[$k]['labelField'] = 'name';
                                } else if (array_key_exists('nama', $attr)) {
                                    $array[$k]['labelField'] = 'nama';
                                } else {
                                    foreach ($attr as $y => $z) {
                                        if ($y == $array[$k]['idField'])
                                            continue;
                                        if ($y == '_softDelete')
                                            continue;
                                        if (substr($y, -3) == "_id")
                                            continue;

                                        $array[$k]['labelField'] = $y;
                                        break;
                                    }
                                }
                            }
                        } else if (!empty($generatorParams)) {
                            self::appendGenMsg('<br/>&bull; Failed to create RelationField <b>' . $i['name'] . '</b>,
                                      <br/> &nbsp; Model Class <b>' . $relClassName . '</b> is not available', $model);
                        }
                        break;
                }
            }

            if ($k < $length / 2) {
                $column1[] = $array[$k];
            } else {
                $column2[] = $array[$k];
            }
        }

        $column1[] = '<column-placeholder></column-placeholder>';
        $column2[] = '<column-placeholder></column-placeholder>';
        $return[]  = [
            'type' => 'Text',
            'value' => '<div ng-show="!params.posted">',
        ];

        $linkBar = [
            [
                'label' => 'Simpan',
                'buttonType' => 'success',
                'icon' => 'check',
                'options' => [
                    'ng-click' => "form.submit(this)",
                ],
                'type' => 'LinkButton',
            ],
        ];

        if ($generatorParams['relation']['deleteable'] == 'Yes') {
            $linkBar[] = [
                'type' => 'Text',
                'value' => '<div ng-if="!isNewRecord" class="separator"></div>',
                'renderInEditor' => 'Yes',
            ];
            $linkBar[] = [
                'label' => 'Hapus',
                'buttonType' => 'danger',
                'icon' => 'trash',
                'options' => [
                    'ng-if' => '!isNewRecord',
                    'href' => 'url:/' . $prefixUrl . '/delete' . ucfirst($generatorParams['relation']['name']) . '&id={params.id}',
                    'confirm' => 'Apakah Anda Yakin ?'
                ],
                'type' => 'LinkButton',
            ];
        }

        $return[] = [
            'linkBar' => $linkBar,
            'title' => '{{ isNewRecord ? \'Tambah ' . $basicTitle . '\' : \'Update ' . $basicTitle . '\'}}',
            'showSectionTab' => 'No',
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
        $return[] = [
            'type' => 'Text',
            'value' => '</div>
<div ng-if="!!params.posted">
    <br>
    <br>
    <br>
    <div ng-if=\'!!params.inserted\'>
        <div ng-init=\'
        parentWindow.' . $generatorParams['relation']['foreignKey'] . '.updateInternal(model.' . $primaryKey . ');
        \'></div>
    </div>
    <div ng-if=\'!!params.deleted\'>
        <div ng-init=\'
        parentWindow.' . $generatorParams['relation']['foreignKey'] . '.unselect();
        \'></div>
    </div>
    <div style=\'text-align:center;\' ng-init=\'
    closeWindow();
    parentWindow.' . $generatorParams['relation']['foreignKey'] . '.reload();
    \'>Loading ...</div>
</div>'
        ];
        return $return;
    }


    private static function generateMaster(&$return, $params) {
        $array      = [];
        $primaryKey = '';
        $cols       = [];
        $length     = 0;
        $basicTitle = "";
        $model      = null;
        $module     = '';
        $basic      = '';
        extract($params);

        $return[] = [
            'linkBar' => [
                [
                    'label' => 'Simpan ' . $basicTitle,
                    'buttonType' => 'success',
                    'icon' => 'check',
                    'options' => [
                        'ng-click' => "form.submit(this)",
                    ],
                    'type' => 'LinkButton',
                ],
            ],
            'title' => '{{ form.title }}',
            'showSectionTab' => 'No',
            'type' => 'ActionBar',
        ];

        $gv = new GridView;

        ## generate Filters & Columns
        $pkCol = null;
        foreach ($array as $k => $i) {
            if ($i['name'] == $primaryKey) {
                $i['label'] = $i['name'];
            }

            ## setup label
            $i['label'] = implode(" ", array_map('ucfirst', explode("_", $i['label'])));

            ## setup filters
            $filter          = [
                'filterType' => "string"
            ];
            $filter['name']  = $i['name'];
            $filter['label'] = $i['label'];
            $filters[]       = $filter;

            ## setup columns
            $column          = [
                'columnType' => "string",
                'options' => []
            ];
            $column['name']  = $i['name'];
            $column['label'] = $i['label'];

            if ($i['name'] != $primaryKey) {
                $gv->columns[]             = $column;
                $column['cellMode']        = 'custom';
                $column['options']['mode'] = 'editable';
                $column['html']            = $gv->getRowTemplate($column, $k);
                $cols[]                    = $column;
            } else {
                $pkCol = $column;
            }
        }
        array_unshift($cols, $pkCol);

        $delButtonCol         = [
            'name' => '',
            'label' => '',
            'columnType' => "string",
            'cellMode' => 'custom',
            'options' => [
                'mode' => 'del-button'
            ]
        ];
        $gv->columns[]        = $delButtonCol;
        $delButtonCol['html'] = $gv->getRowTemplate($delButtonCol, count($cols));
        array_push($cols, $delButtonCol);

        $return[] = [
            'name' => 'dataFilter1',
            'datasource' => 'dataSource1',
            'type' => 'DataFilter',
            'filters' => $filters
        ];

        if (!!$model->_softDelete) {
            $condition = $model->_softDelete['column'] . ' <> \'' . $model->_softDelete['value'] . '\' {AND [where]}';
        } else {
            $condition = '{[where]}';
        }

        $return[] = [
            'name' => 'dataSource1',
            'params' => [
                'where' => 'dataFilter1',
                'paging' => 'gridView1',
                'order' => 'gridView1',
            ],
            'relationTo' => 'currentModel',
            'relationCriteria' => array(
                'select' => '',
                'distinct' => 'false',
                'alias' => 't',
                'condition' => $condition,
                'order' => '{[order]}',
                'paging' => '{[paging]}',
                'group' => '',
                'having' => '',
                'join' => '',
            ),
            'type' => 'DataSource',
        ];
        $return[] = [
            'name' => 'gridView1',
            'datasource' => 'dataSource1',
            'type' => 'GridView',
            'columns' => $cols
        ];
        $return[] = [
            'renderInEditor' => 'Yes',
            'type' => 'Text',
            'value' => '<div style="margin-top:5px;text-align:center;\">
    <div ng-click="gridView1.addRow()"
    class="btn btn-sm btn-success"><i class="fa fa-plus"></i> New Record</div>
</div>',
        ];
        return $return;
    }

    private static function generateForm(&$return, $params) {
        $array      = [];
        $primaryKey = '';
        $columns    = [];
        $length     = 0;
        $model      = null;
        $basicTitle = "";
        $module     = '';
        $basic      = '';
        extract($params);
        $modelClassName = get_class($model);

        $generatorParams = [];
        if (isset($_SESSION['CrudGenerator']) && isset($_SESSION['CrudGenerator'][$modelClassName])) {
            $generatorParams = $_SESSION['CrudGenerator'][$modelClassName];
            unset($_SESSION['CrudGenerator'][$modelClassName]);
        }

        $generatorParams = json_decode('{"name":"ErisAddressForm.php","className":"ErisAddressForm","extendsName":"Address","type":"form","relations":[{"name":"customers","tableName":"customer","type":"CHasManyRelation","foreignKey":"address_id","className":"Customer","formType":"Table","editable":"No","insertable":"No"}],"status":"processing","overwrite":true,"path":"app.modules.eris.forms.address"}', true);

        $prefixUrl = "{$basic}";
        if ($module != '' && $module != 'app' && $module != 'application') {
            $prefixUrl = "{$module}/" . $prefixUrl;
        }

        $column1  = [];
        $column2  = [];
        $array_id = null;

        $genRel = [];

        foreach ($array as $k => $i) {
            if (isset($array[$k]['name']) && isset($columns[$array[$k]['name']])) {
                if ($array[$k]['name'] == $primaryKey) {
                    $array_id = $array[$k];
                    continue;
                }
                if (isset($array[$k]['label'])) {
                    $array[$k]['label'] = implode(" ", array_map('ucfirst', explode("_", $array[$k]['label'])));
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
                        ## generate relation field
                        ## get class name
                        $relClassName       = substr($i['name'], 0, strlen($i['name']) - 3);
                        $array[$k]['label'] = str_replace("_", " ", ucfirst($relClassName));
                        $relClassName       = implode("", array_map('ucfirst', explode("_", $relClassName)));

                        if (!!@$generatorParams['relations']) {
                            foreach ($generatorParams['relations'] as $rel) {
                                $relName = ucfirst($rel['name']);
                                if (@$rel['type'] == 'CBelongsToRelation' && $relClassName == $relName) {
                                    $relForm      = substr($modelClassName, 0, -4) . $relName . 'Relform';
                                    $relClassName = $rel['className'];
                                    $buttons      = [];

                                    if ($rel['insertable'] == "Yes") {
                                        $label     = strlen($array[$k]['label']) > 10 ? '' : $array[$k]['label'];
                                        $buttons[] = [
                                            'renderInEditor' => 'Yes',
                                            'type' => 'Text',
                                            'value' => '<div
ng-click="Rel' . $relName . 'InsertPopup.open()"
style="margin:0px 0px 10px 5px;" class="btn btn-xs btn-default pull-right"><i class="fa fa-plus"></i> New ' . $label . '</div>',
                                        ];
                                    }

                                    $buttons[] = [
                                        'renderInEditor' => 'Yes',
                                        'type' => 'Text',
                                        'value' => '<div
ng-click="Rel' . $relName . 'UpdatePopup.open()" ng-if="!!model.' . $i['name'] . ' && !!' . $i['name'] . '.text"
style="margin-bottom:10px;" class="btn btn-xs btn-default pull-right"><i class="fa fa-pencil"></i> Edit ' . $array[$k]['label'] . '</div>
<div class="clearfix"></div>',
                                    ];

                                    if ($rel['insertable'] == "Yes") {
                                        $buttons[] = [
                                            'type' => 'PopupWindow',
                                            'name' => 'Rel' . $relName . 'InsertPopup',
                                            'options' => array(
                                                'height' => '500',
                                                'width' => '700',
                                            ),
                                            'mode' => 'url',
                                            'url' => $prefixUrl . '/insert' . $relName . '&id={{model.' . $i['name'] . '}}',
                                        ];
                                    }

                                    $buttons[] = [
                                        'type' => 'PopupWindow',
                                        'name' => 'Rel' . $relName . 'UpdatePopup',
                                        'options' => array(
                                            'height' => '500',
                                            'width' => '700',
                                        ),
                                        'mode' => 'url',
                                        'url' => $prefixUrl . '/update' . $relName . '&id={{model.' . $i['name'] . '}}',
                                    ];

                                    $genRel[] = [
                                        'index' => $k,
                                        'data' => $buttons
                                    ];
                                    break;
                                }
                            }
                        }

                        if (@is_subclass_of($relClassName, 'ActiveRecord')) {
                            ## get class alias
                            if (is_file(Yii::getPathOfAlias('app.models.' . $relClassName) . ".php")) {
                                $classAlias = "app.models." . $relClassName;
                            } else if (is_file(Yii::getPathOfAlias('application.models.' . $relClassName) . ".php")) {
                                $classAlias = "application.models." . $relClassName;
                            } else {
                                $classAlias = '';
                            }

                            if ($classAlias != '') {
                                ## fill attribute
                                $array[$k]['type']       = "RelationField";
                                $array[$k]['modelClass'] = $classAlias;
                                $array[$k]['idField']    = $relClassName::model()->tableSchema->primaryKey;
                                $attr                    = $relClassName::model()->attributes;

                                ## fill label field
                                if (array_key_exists('name', $attr)) {
                                    $array[$k]['labelField'] = 'name';
                                } else if (array_key_exists('nama', $attr)) {
                                    $array[$k]['labelField'] = 'nama';
                                } else {
                                    foreach ($attr as $y => $z) {
                                        if ($y == $array[$k]['idField'])
                                            continue;
                                        if ($y == '_softDelete')
                                            continue;
                                        if (substr($y, -3) == "_id")
                                            continue;

                                        $array[$k]['labelField'] = $y;
                                        break;
                                    }
                                }
                            }
                        } else if (!empty($generatorParams)) {
                            self::appendGenMsg('<br/>&bull; Failed to create RelationField <b>' . $i['name'] . '</b>,
                                      <br/> &nbsp; Model Class <b>' . $relClassName . '</b> is not available', $model);
                        }
                        break;
                }
            }

            if ($k < $length / 2) {
                $column1[] = $array[$k];
            } else {
                $column2[] = $array[$k];
            }
        }

        foreach ($genRel as $i => $gen) {
            $colNum = $gen['index'] >= floor(count($array) / 2) ? 2 : 1;
            $idx    = $gen['index'] >= floor(count($array) / 2) ? $gen['index'] - ceil(count($array) / 2) : $gen['index'];
            $offset = ($i * count($gen['data'])) + 1;
            array_splice(${'column' . $colNum}, $idx + $offset, 0, $gen['data']);
        }

        $column1[] = '<column-placeholder></column-placeholder>';
        $column2[] = '<column-placeholder></column-placeholder>';

        $return[] = [
            'linkBar' => [
                [
                    'label' => 'Kembali',
                    'buttonType' => 'default',
                    'icon' => 'chevron-left',
                    'options' => [
                        'href' => "url:/{$prefixUrl}/index",
                    ],
                    'type' => 'LinkButton',
                ],
                [
                    'label' => 'Simpan',
                    'buttonType' => 'success',
                    'icon' => 'check',
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
                    'icon' => 'trash',
                    'options' => [
                        'ng-if' => '!isNewRecord',
                        'href' => "url:/{$prefixUrl}/delete?id={model.{$primaryKey}}",
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

        if (!!@$generatorParams['relations']) {
            foreach ($generatorParams['relations'] as $rel) {
                if ($rel['type'] == 'CHasManyRelation' || $rel['type'] == 'CHasManyRelation') {
                    $return[] = [
                        'title' => ucfirst($rel['name']),
                        'type' => 'SectionHeader',
                    ];

                    if (!@is_subclass_of($rel['className'], 'ActiveRecord')) {
                        self::appendGenMsg('<br/>&bull; Failed to create Relation <b>' . $rel['name'] . '</b>,
                                      <br/> &nbsp; Model Class <b>' . $rel['className'] . '</b> is not available', $model);
                        continue;
                    }

                    switch ($rel['formType']) {
                        case "Table":
                            $return[] = self::insertRelTable($rel, $model);
                            break;
                        case "SubForm":
                            $return[] = self::insertRelSubForm($generatorParams, $rel);
                            break;
                    }
                }
            }
        }

        return $return;
    }

    private static function insertRelTable($params, $parentModel) {
        $gv         = new GridView;
        $model      = $params['className']::model();
        $array      = $model->modelFieldList;
        $primaryKey = $model->tableSchema->primaryKey;

        var_dump($array);
        die();

        foreach ($array as $k => $i) {
            if ($array[$k]['name'] == $primaryKey) {
                continue;
            }
            $i['label'] = implode(" ", array_map('ucfirst', explode("_", $i['label'])));
            $filter     = [
                'filterType' => "string",
                'name' => $i['name'],
                'label' => $i['label']
            ];
            $column     = [
                'columnType' => "string",
                'options' => [],
                'name' => $i['name'],
                'label' => $i['label']
            ];

            $filters[]     = $filter;
            $cols[]        = $column;
            $gv->columns[] = $column;
        }
    }

    private static function appendGenMsg($message, $model) {
        if (!isset($_SESSION['CrudGenerator'][get_class($model)])) {
            $_SESSION['CrudGenerator'][get_class($model)] = [];
        }

        if (!isset($_SESSION['CrudGenerator'][get_class($model)]['msg'])) {
            $_SESSION['CrudGenerator'][get_class($model)] = [
                'msg' => '<b>WARNING: </b>'
            ];
        }

        $msg = $_SESSION['CrudGenerator'][get_class($model)]['msg'];
        $msg .= $message;
        $_SESSION['CrudGenerator'][get_class($model)] = ['msg' => $msg];
    }
}
