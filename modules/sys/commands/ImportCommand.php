<?php
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

class ImportCommand extends Service {
    const MAX_ERRORS = 20;
    
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
            <th>Row #</th>
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
                $err = "<table class='table table-condensed table-bordered' style='margin:0px;'>
                    <tr>";
                foreach ($e as $k=>$v) {
                    $err = $err . "<td>" . $k ."</td>";
                    $err = $err . "<td>" . implode("<br/>",$v) ."</td>";
                }
                $err = $err . "</tr></table>";
                $data[] = "
        <tr>
            <th class='text-center' style='vertical-align:middle'><div class='label label-danger'>{$r}</div></th>
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
                
                ## assign value to model attribute
                $model = new $modelClass;
                foreach ($modelColumns as $c=>$v) {
                    $model->$c = $row[$excelColumns[$c]];
                }
                
                ## before lookup
                foreach ($modelColumns as $c=>$v) {
                    if (is_array($config['columns'][$c]) && 
                        $config['columns'][$c]['type'] == 'function' && 
                        is_string($config['columns'][$c]['value'])) {
                            
                        $model->$c = Helper::evaluate($config['columns'][$c]['value'], [
                            'row' => $model->attributes
                        ]);
                    } 
                }
                
                ## process lookup
                
                ## after lookup
                foreach ($modelColumns as $c=>$v) {
                    if (is_array($config['columns'][$c]) && 
                        $config['columns'][$c]['type'] == 'function' && 
                        is_string($config['columns'][$c]['value'])) {
                            
                        $model->$c = Helper::evaluate($config['columns'][$c]['value'], [
                            'row' => $model->attributes
                        ]);
                    }
                }
                
                ## save model
                $model->save();
                if (count($model->errors) > 0) {
                    if (count($errors) <= ImportCommand::MAX_ERRORS) {
                        $errors[$r] = $model->errors;
                    } else {
                        $this->failed('Too Many Errors...');
                        break;
                    }
                }
                
                
                $this->msg('Importing ' . $r . ' Row...<br/><br/>' . $this->formatErrors($errors));
                
            }
            break;
        }
    
        if (empty($errors)) {
            $transaction->commit();
        } else {
            $transaction->rollback();
        }
        
        $reader->close();
    }
}