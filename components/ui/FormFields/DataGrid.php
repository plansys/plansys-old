<?php

/**
 * Class DataGrid
 * @author rizky
 */
class DataGrid extends FormField {

    public static $toolbarName = "Data Grid";
    public static $category    = "Data & Tables";
    public static $toolbarIcon = "fa fa-table fa-nm";
    public static $deprecated  = true;
    public        $name;
    public        $datasource;
    public        $columns     = [];
    public        $gridOptions = [];    

    public static function generateParams($paramName, $params, $template, $paramOptions = []) {
        switch ($paramName) {
            case "order":
            case "!order":
                return DataGrid::generateOrderParams($params, $template, $paramOptions);
                break;
            case "paging":
                return DataGrid::generatePagingParams($params, $paramOptions);
                break;
        }
    }

    private static function generateOrderParams($params, $template, $paramOptions = []) {
        $sqlparams = [];
        $sql       = [];

        if (!is_array($params)) {
            $params = ['order_by' => []];
        }

        $tmpl = preg_replace("/order\s+by/i", "", $template);

        if (strpos($tmpl, "[order]") !== false) {
            $syntax   = "order";
            $rawOrder = explode(",", trim(str_replace("[order]", "", $tmpl)));
        } else if (strpos($tmpl, "[!order]") !== false) {
            $syntax   = "!order";
            $rawOrder = explode(",", trim(str_replace("[!order]", "", $tmpl)));
        }

//        foreach ($rawOrder as $o) {
//            $o = trim(preg_replace('!\s+!', ' ', $o));
//            $o = explode(" ", $o);
//            if (count($o) == 2) {
//                $index = -1;
//                foreach ($params['order_by'] as $k => $p) {
//                    if (@$p['fields'] == $o[0]) {
//                        $index = $k;
//                    }
//                }
//
//                if ($index < 0) {
//                    array_unshift($params['order_by'], [
//                        'field' => $o[0],
//                        'direction' => $o[1]
//                    ]);
//                } else {
//                    $params['order_by'][$index] = [
//                        'field' => $o[0],
//                        'direction' => $o[1]
//                    ];
//                }
//            }
//        }

        if (isset($params['order_by']) && count($params['order_by']) > 0) {
            foreach ($params['order_by'] as $k => $o) {
                $direction = $o['direction'] == 'asc' ? 'asc' : 'desc';
                $field     = "|" . preg_replace("[^a-zA-Z0-9]", "", $o['field']) . "|";

                ## quote field if it is containing illegal char
                // if (!preg_match("/^[a-zA-Z_][a-zA-Z0-9_]*$/", str_replace(".", "", $field))) {
                //     $field = "`{$field}`";
                // }
                $field = ActiveRecord::formatCriteria($field);
                $sql[] = "{$field} {$direction}";
            }
        }

        $query  = '';
        $addSql = ($syntax == "!order" ? "" : "order by ");

        if (count($sql) > 0 || $tmpl != '') {
            if ($template == '' || trim($template) == $syntax) {
                $query = $addSql . implode(" , ", $sql);
            } else {
                $query = str_replace("[" . $syntax . "]", implode(" , ", $sql), $tmpl);
                $query = trim(trim($query), ",");

                if (strpos($query, "order by") === false && $query != "") {
                    $query = $addSql . $query;
                }
            }
        } else if (count(@$paramOptions) > 0) {
            $query = $addSql . @$paramOptions[0];
        }

        return [
            'sql' => $query,
            'params' => [],
            'render' => true,
            'generateTemplate' => true
        ];
    }

    public static function generatePagingParams($params, $paramOptions = []) {
        if (!isset($params['currentPage']) || count($params['currentPage']) == 0) {
            $defaultPageSize = "25";

            $template = [
                'sql' => "limit {$defaultPageSize}",
                'params' => [],
                'render' => true
            ];
        } else {
            $currentPage = $params['currentPage'];
            $pageSize    = $params['pageSize'];
//            $totalItems = $params['totalServerItems'];

            $from = ($currentPage - 1) * $pageSize;
            $from = $from < 0 ? 0 : $from;

            $to = $currentPage * $pageSize;

            $template = [
                'sql' => "limit {$from},{$pageSize}",
                'params' => [
                    //                    'limit' => $pageSize,
                    //                    'offset' => $from,
                    //                    'page' => $currentPage,
                    //                    'pageSize' => $pageSize
                ],
                'render' => true
            ];
        }
        return $template;
    }

    /**
     * @return array me-return array property DataGrid.
     */
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Data Grid Name',
                'name' => 'name',
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Data Source Name',
                'name' => 'datasource',
                'options' => array (
                    'ng-model' => 'active.datasource',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                    'ps-list' => 'dataSourceList',
                ),
                'labelWidth' => '5',
                'fieldWidth' => '7',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Generate Columns',
                'buttonType' => 'success',
                'icon' => 'magic',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin:0px 0px 5px 0px',
                    'ng-show' => 'active.datasource != \'\'',
                    'ng-click' => 'generateColumns()',
                ),
                'type' => 'LinkButton',
            ),
            array (
                'type' => 'Text',
                'value' => '<div class=\'clearfix\'></div>',
            ),
            array (
                'label' => 'Grid Options',
                'name' => 'gridOptions',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
            array (
                'title' => 'Columns',
                'type' => 'SectionHeader',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\'margin-top:5px\'></div>',
            ),
            array (
                'name' => 'columns',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.components.ui.FormFields.DataGridListForm',
                'options' => array (
                    'ng-model' => 'active.columns',
                    'ng-change' => 'save()',
                ),
                'singleViewOption' => array (
                    'name' => 'val',
                    'fieldType' => 'text',
                    'labelWidth' => 0,
                    'fieldWidth' => 12,
                    'fieldOptions' => array (
                        'ng-delay' => 500,
                    ),
                ),
                'type' => 'ListView',
            ),
        );
    }

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['js'];
    }

    public function actionExportExcel() {
        $data = json_decode($_POST['data'], true);
        $file = $_POST['file'];

        ## add header
        if (count($data) > 0) {
            array_unshift($data, $data[0]);
            foreach ($data[0] as $k => $i) {
                $data[0][$k] = $k;
            }
        }
        ## generate excel
        Yii::import('ext.phpexcel.XPHPExcel');
        $phpExcelObject = XPHPExcel::createPHPExcel();
        $phpExcelObject->getActiveSheet()->fromArray($data, null, 'A1');
        foreach (range('A', $phpExcelObject->getActiveSheet()->getHighestDataColumn()) as $col) {
            $phpExcelObject->getActiveSheet()
                ->getColumnDimension($col)
                ->setAutoSize(true);
        }

        $this->generateExcel($phpExcelObject, $file);
    }

    public function generateExcel($phpExcelObject, $filename) {
        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($phpExcelObject, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function actionGenerateExcelTemplate() {
        $cols = json_decode($_GET['columns'], true);
        Yii::import('ext.phpexcel.XPHPExcel');
        $phpExcelObject = XPHPExcel::createPHPExcel();
        $phpExcelObject->getActiveSheet()->fromArray($cols, null, 'A1');
        foreach (range('A', $phpExcelObject->getActiveSheet()->getHighestDataColumn()) as $col) {
            $phpExcelObject->getActiveSheet()
                ->getColumnDimension($col)
                ->setAutoSize(true);
        }

        $this->generateExcel($phpExcelObject, 'contoh-template');
    }

    public function actionUpload($path = null) {
        if (!isset($_FILES['file'])) {
            echo json_encode(["success" => "No"]);
            die();
        }
        $file = $_FILES["file"];
        $name = $file['name'];

        ## create temporary directory
        $tmpdir = Yii::getPathOfAlias('webroot.assets.tmp_exim');
        if (!is_dir($tmpdir)) {
            mkdir($tmpdir, 0755, true);
            chmod($tmpdir, 0755);
        }

        ## make sure there is no duplicate file name
        $i          = 1;
        $actualName = pathinfo($name, PATHINFO_FILENAME);
        $originName = $actualName;
        $extension  = pathinfo($name, PATHINFO_EXTENSION);
        while (file_exists($tmpdir . DIRECTORY_SEPARATOR . $actualName . '.' . $extension)) {
            $actualName = (string)$originName . '_' . $i;
            $name       = $actualName . '.' . $extension;
            $i++;
        }

        $tmppath = $tmpdir . DIRECTORY_SEPARATOR . $name;
        move_uploaded_file($file["tmp_name"], $tmppath);

        switch (@$_GET['a']) {
            case "excel":
                $reader = new ExcelImport();
                $reader->read($tmppath);
                $se     = $reader->sheets[0]['cells'];
                $arr    = array_shift($se);
                $result = array();

                foreach ($se as $row) {
                    $temp = [];
                    foreach ($row as $k => $val) {
                        if (isset($arr[$k])) {
                            $temp[$arr[$k]] = $val;
                        }
                    }
                    $result[] = $temp;
                }
                echo json_encode($result);
                break;
            default:
                echo json_encode([
                    'success' => 'Yes',
                    'path' => $tmppath,
                    'downloadPath' => base64_encode($tmppath),
                    'name' => $name
                ]);
                break;
        }
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut datafilter dari hasil render
     */
    public function render() {

        foreach ($this->columns as $k => $c) {


            switch ($c['columnType']) {
                case 'dropdown':
                    $list = '[]';
                    if (@$c['listType'] == 'php') {
                        $list = $this->evaluate(@$c['listExpr'], true);
                        $list = json_encode($list);
                    }
                    $this->columns[$k]['listItem'] = $list;
                    break;
                case 'string':
                    if (count(@$this->columns[$k]['stringAlias']) > 0) {
                        foreach ($this->columns[$k]['stringAlias'] as $x => $y) {
                            $this->columns[$k]['stringAlias'][$x] = htmlentities($y);
                        }
                    }
                    break;
            }
        }

        return $this->renderInternal('template_render.php');
    }

}