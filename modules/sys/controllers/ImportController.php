<?php
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
        
class ImportController extends Controller {
    public function beforeAction() {
        if (isset($_GET['m'])) {
            if (class_exists($_GET['m']) && is_subclass_of($_GET['m'], 'ActiveRecord')) { 
                return true;
            }
        }
        throw new CHttpException(404);
    }
    
    public function actionIndex() {
        $model = new SysImportData;
        if (isset($_POST["SysImportData"])) {
            $_POST['SysImportData']['model'] = $_GET['m'];
            ServiceManager::start('ImportData', $_POST["SysImportData"]);
            return;
        }
        
        $this->renderForm("SysImportData", $model);
    }
    
    public function actionDownloadTemplate() {
        $model = new $_GET['m'];
        $header = [];
        $sample = [];
        $pk = $model->tableSchema->primaryKey;
        $cols = $model->tableSchema->columns;
        foreach ($cols as $c) {
            $header[] = $c->name;
            if ($c->name == $pk) { 
                $sample[] = '';
                continue;
            }
            switch ($c->dbType) {
                case "date":
                    $sample[] = '2016-10-23';
                    break;
                case "datetime":
                    $sample[] = '2016-10-23 14:54:43';
                    break;
                case "time":
                    $sample[] = '14:54:32';
                    break;
                default:
                    $sample[] = 'text';
                default:
                    if ($c->type == "integer") {
                        $sample[] = 123;
                    } else {
                        $sample[] = 'text';
                    }
            }
        }
        
        if (isset($GLOBALS))
        
        $writer = WriterFactory::create(Type::XLSX);
        $writer->openToBrowser("import-". Helper::camelToSnake($_GET['m']) . '.xlsx'); // stream data directly to the browser
        $writer->addRows([$header, $sample]); // add multiple rows at a time
        $writer->close();
    }
}