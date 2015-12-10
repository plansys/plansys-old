<?php
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

class ImportCommand extends Service {
    const MAX_ERRORS = 20;
    
    public function finished($msg) {
        $this->setView('finished', $msg. "\n");
        echo $msg;
    }
    public function failed($msg) {
        $this->setView('failed', $msg. "\n");
        echo $msg;
    }
    
    public function msg($msg) {
        $this->setView('body', $msg . "\n");
        echo $msg;
    }
    
    public function formatErrors($errors) {
        if (!empty($errors)) {
            $html = <<<EOF
<table class='table table-condensed table-bordered' style="margin:0px;">
        <tr>
            <th>Row Num.</th>
            <th>Error(s)</th>
        </tr>
        [data]
    </table>
</pre>
EOF;
            $data = [];
            $toomany = false;
            foreach ($errors as $r=>$e) {
                $msg = json_encode($e, JSON_PRETTY_PRINT);
                $err = "<table class='table table-condensed table-bordered' style='margin:0px;'>";
                foreach ($e as $k=>$v) {
                    $err .= "<tr>";
                    $err .=  "<td>" . $k ."</td>";
                    $err .= "<td>" . implode("<br/>",$v) ."</td>";
                    $err .= "</tr>";
                }
                $err = $err . "</table>";
                $data[] = "
        <tr>
            <th class='text-center' style='vertical-align:middle'>{$r}</th>
            <th>{$err}</th>
        </tr>
";              
                if ($r >= ImportCommand::MAX_ERRORS) {
                    $toomany = true;
                }
            }
            
            $html = str_replace("[data]", implode("\n", $data), $html);
            if ($toomany) {
                $html = $html . "<b style='color:red'> Too many errors... </b>";
            }
            
            return $html;
        }
    }
    
    public function getConfig($modelClass, $name = "") {
        $configPath = Yii::getPathOfAlias('app.config.etl');
        if (!is_dir($configPath)) {
            mkdir($configPath, 0777, true);
        }
        $fileName = $modelClass . "." . ($name == "" ? "" : "." . $name) . ".json"; 
        $filePath = $configPath . DIRECTORY_SEPARATOR . $fileName;
        $config = null;
        if (is_file($filePath)) {
            $config = json_decode(file_get_contents($filePath), true);
        } 
        
        if (is_null($config)) {
            $model = new $modelClass;
            $columns = [];
            foreach ($model->attributes as $key => $val) {
                if (!is_array($val)) {
                    $columns[$key] = "default";
                }
            } 
            
            return [
                'columns' => $columns
            ];
        }
        return $config;
    }
    
    public function actionIndex() {
        if (is_null($this->params) || !isset($this->params['file'])) {
            $this->failed('You should run this from `Import Controller`');
            return;
        }
        if (!class_exists($this->params['model'])) {
            $this->failed('Model ' . $this->params['model'] . ' does not exist!');
            return;
        }
        $file = $this->params['file'];
        $modelClass = $this->params['model'];
        
        $errors = [];
        $excelColumns = [];
        
        $config = $this->getConfig($modelClass);
        $modelColumns = $config['columns'];

        $this->setView('title', '<h3 style="margin-top:5px;">Importing ' . $modelClass . '</h3>');
        $this->msg('Opening Excel File...');

        $reader = ReaderFactory::create(Type::XLSX);
        $reader->open($file);
        $transaction = Yii::app()->db->beginTransaction();
        
        ## get first sheet
        $import = new Import($this->params['model']);
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
                
                ## do import 
                $rowImport = [];
                foreach ($import->columns as $c=>$v) {
                    $col = is_string($v) ? ['type' => $v] : $v;
                    if ($col['type'] == 'function') {
                        continue;
                    }
                    $rowImport[$c] = $row[$excelColumns[$c]];
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
                
                $this->msg('Importing ' . ($r - 1) . ' Row...<br/><br/>' . $this->formatErrors($errors));
            }
            break;
        }
    
        if (empty($errors)) {
            $this->msg('<a style="margin-top:10px;" href="'.$import->saveExcel().'" class="btn btn-success btn-sm">
                            <i class="fa fa-download"></i> Download Excel
                        </a>');
            $transaction->commit(); 
            $this->finished("Done");
        } else {
            $this->failed('Import Failed');
            $transaction->rollback();
        }
        
        
        $reader->close();
    }
}