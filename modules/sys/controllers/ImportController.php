<?php
        
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

class ImportController extends Controller {
    
    public $enableDebug = false;
    
    public function actionIndex() {
        if (isset($_GET['m'])) {
            
            $m = explode(".", $_GET['m']);
            $model = @$m[0];
            $submodel = @$m[1];
            
            if (!class_exists($model) || !is_subclass_of($model, 'ActiveRecord')) { 
                throw new CHttpException(404);
            }
        } else {
            throw new CHttpException(404);
        }
        
        $model = new SysImportData;
        $submodel = '';
        if (isset($_POST["SysImportData"])) {
            if (@$_GET['mode'] == 'test') {
                $this->redirect(['test', 
                    'm' => $_GET['m'],
                    'f' => $_POST['SysImportData']['file']
                ]);
            } else {
                $_POST['SysImportData']['model'] = $_GET['m'];
                $pid = ServiceManager::start('ImportData', $_POST["SysImportData"]);
                $this->redirect(['/sys/service/view&name=ImportData&id=' . $pid]);
            }
        }
        
        $options = [];
        
        if (@$_GET['mode'] == "blank") {
            $options['layout'] = "//layouts/blank";
        }
        
        $this->renderForm("SysImportData", $model, [
            's' => $submodel
        ], $options);
    }
    
    public function actionTest($m,$f) {
        Yii::import('application.commands.ImportCommand');
        $file = $f;
        //$file = '/var/www/eauction/assets/tmp/Yawyaw_6.xlsx';
        $model = $m;
        $errors = [];
        $excelColumns = [];
        
        $reader = ReaderFactory::create(Type::XLSX);
        $a = $reader->open($file);
        $transaction = Yii::app()->db->beginTransaction();
        
        ## get first sheet
        $import = new Import($model);
        foreach ($reader->getSheetIterator() as $sheet) {
            
            ## loop each row in first sheet
            $rowCount = count($sheet->getRowIterator());
            
            foreach ($sheet->getRowIterator() as $r=>$row) {
                ## first row is always column name, assign it then skip it
                if ($r == 1) { 
                    foreach ($row as $k=>$v) {
                        $excelColumns[$v] = $k;
                    }
                    continue;
                }
                   
                foreach ($row as $k=>$item) {
                    if(
                        ( !is_array( $item ) ) &&
                        ( ( !is_object( $item ) && settype( $item, 'string' ) !== false ) ||
                        ( is_object( $item ) && method_exists( $item, '__toString' ) ) )
                    ) {
                        $row[$k] = (string)$item;
                    } else {
                        $row[$k] = "";
                    }
                }
                
                ## do import 
                $rowImport = [];
                $emptyCount = 0;
                foreach ($import->columns as $c=>$v) {
                    $col = is_string($v) ? ['type' => $v] : $v;
                    if ($col['type'] == 'function') {
                        continue;
                    }
                    $rowImport[$c] = @$row[$excelColumns[$c]];
                    
                    if (@$row[$excelColumns[$c]] == "") {
                        $emptyCount++;
                    }
                }
                
                if ($emptyCount + 1 >= count($import->columns)) {
                    continue;
                }
                
                try {
                    $res = $import->importRow($rowImport);
                } catch (Exception $e) {
                    $res = [['error' => $e->getMessage()]];
                }
                
                ## mark errors
                if ($res !== true) {
                    if (count($errors) <= ImportCommand::MAX_ERRORS) {
                        $errors[$r - 1] = $res;
                    } else {
                        $this->failed('Import Failed');
                        break;
                    }
                }
                
            }
            break;
        }
        
        echo "ETL CONFIG: OK";
    
    }
    
    public function actionDownloadTemplate() {
        Export::downloadSample($_GET['m']);
    }
}