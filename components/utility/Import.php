<?php
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;

class Import extends CComponent {
    
    const ETL_PATH = 'app.config.etl';
    public $config = [];
    public $columns = [];
    public $relations = [];
    public $model = null;
    public $modelClass = null;
    public $resultFile = '';
    public $resultUrl = '';
    
    public $originalRow = [];
    public $currentRow = [];
    public $lastRow = [];
    public $lastFilledRow = [];
    public $rowHistory = [];
    public $skip = [];
    public $rowIndex = 0;
    
    private $root = null;
    private $parent = null;
    private $loaded = false;
    private $lookup = [];
    private $ignoreCols = [];
    
    public function loadConfig($model, $defaultConfig = [], &$root = null, &$parent = null) {
        $dir = Yii::getPathOfAlias(Import::ETL_PATH);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            chmod($dir, 0755);
        }
        
        $configSuffix = "";
        if (strpos($model, ".") !== false) {
            $m = explode(".", $model);
            $model = $m[0];
            $configSuffix = $m[1];
            
            if(!empty($configSuffix)){
                $configSuffix = ".".$configSuffix;
            }
        }
        
        $filePath = $dir . DIRECTORY_SEPARATOR . $model . $configSuffix . ".php";
        
        if (!class_exists($model)) {
            throw new CException('Failed to load `' . $model . '` Class. Class does not exist!');
        }
        
        if (!is_subclass_of($model, 'ActiveRecord')) {
            throw new CException('Failed to load `' . $model . '`. Model must extends from ActiveRecord Class!');
        }
        
        if (!is_null($root)) {
            $this->root = $root;
        }
        
        if (!is_null($parent)) {
            $this->parent = $parent;
        }
        
        ## instantiate model class
        $this->modelClass = $model;
        $this->model = new $model;
        
        if (!empty($defaultConfig)) {
            $config = $defaultConfig;
        } else {
            if (is_file($filePath)) {
                $config = include($filePath);
                if (is_null($config)) {
                    throw new CException('Failed to read config file `' . $filePath . '`. Please fix your config!');
                }
            } else {
                $modelColumns = [];
                foreach ($this->model->tableSchema->columns as $key=>$col) {
                    if ($col->isPrimaryKey) {
                        $modelColumns[$key] = 'pk';
                    } else {
                        $modelColumns[$key] = 'default';
                    }
                }
                
                $config = [
                    'columns' => $modelColumns
                ];
            }
        }
        
        ## parse columns definition
        $cols = $this->model->tableSchema->columns;
        if (is_array($config['columns']) && !empty($config['columns'])) {
            ## load columns definition
            $this->columns = $config['columns'];
        } else {
            ## columns definition is not available, generate it from db
            foreach ($cols as $key=>$col) {
                if ($col->isPrimaryKey) {
                    $this->columns[$key] = "pk";
                } else {
                    $this->columns[$key] = "default";
                }
            }
        }
        
        if (isset($config['skipParentIf'])) {
            $this->skip['parentIf'] = $config['skipParentIf'];
        }
        
        if (isset($config['skipIf'])) {
            $this->skip['if'] = $config['skipIf'];
        }
        
        if (isset($config['skipChildIf'])) {
            $this->skip['childIf'] = $config['skipChildIf'];
        }
        
        ## ignore columns that is unavailable in model
        foreach ($this->columns as $k=>$c) {
            if (!isset($cols[$k])) {
                $this->ignoreCols[$k] = $c;
            }
        }
        
        if (isset($config['relations'])) {
            if (is_array($config['relations'])) {
                $this->relations = $config['relations'];
                
                foreach ($this->relations as $rname => $rel) {
                    if (!isset($rel['model'])) {
                        throw new CException('Invalid columns configuration  in `' . $filePath . '`.
                        Missing `model` key in `'.$rname .'` relation!');
                    }
                    $relModel = $rel['model'];
                    unset($rel['model']);
                    
                    if (!is_null($this->root)) {
                        $this->relations[$rname]['import'] = new Import($relModel, $rel, $this->root, $this);
                    } else {
                        $this->relations[$rname]['import'] = new Import($relModel, $rel, $this, $this);
                    }
                }
            }
        }
        
        foreach ($this->columns as $key=>$col) {
            if (is_string($col)) {
                if ($col == 'pk' || $col == 'default') {
                    $col = ['type' => $col];
                } else {
                    $col =  [
                        'type' => 'function',
                        'value' => $col
                    ];
                }
                $this->columns[$key] = $col;
            }
            
            if (!isset($col['type'])) {
                echo "ERROR!<br/>Key: {$key} does not have type";
                die();
            }
            
            switch ($col['type']) {
                case 'function':
                    if (!isset($col['value'])) {
                        throw new CException('Invalid columns configuration  in `' . $filePath . '`.
                        Missing `value` key, please provide function name to execute in `value` key!');
                    }

                    break;
                case 'lookup':
                    if (!isset($col['from'])) {
                        throw new CException('Invalid columns configuration  in `' . $filePath . '`.
                        ['.$key.'] Missing `from` key, please provide table name to refer to!');
                    }
                    if (!isset($col['return'])) {
                        throw new CException('Invalid columns configuration  in `' . $filePath . '`.
                        ['.$key.'] Missing `return` key, please provide return column condition !');
                    }
                    if (!isset($col['condition'])) {
                        throw new CException('Invalid columns configuration  in `' . $filePath . '`.
                        ['.$key.'] Missing `condition` key, please provide lookup condition !');
                    }
                    
                    $table = Yii::app()->db->schema->getTable($col['from']);
                    if (isset($table)) {
                        if (!isset($this->lookup[$col['from']])) {
                            $this->lookup[$col['from']] = [
                                'schema' => Yii::app()->db->schema->getTable($col['from']),
                                'hash' => [
                                    $key => []
                                ]
                            ];
                        } else {
                            $this->lookup[$col['from']]['hash'][$key] = [];
                        }
                    } else {
                        throw new CException('Invalid columns configuration  in `' . $filePath . '`.
                        ['.$key.'] table `'.$col['from'].'` is not found!');
                    }
                    break;
            }
        }
        
        $this->config = $config;
        $this->loaded = true;
        
        return true;
    }
    
    private function lookup(&$attrs, $col, $key, $row, &$returnRows = null) {
        $errors = [];
        $hashKey = [];
        
        $rowParams = $this->rowParams($row);
        
        ## replace condition
        $condition = preg_replace_callback( "/{([^.}]*)\.?([^}]*)}/", function($var) use($key, $rowParams, &$errors, &$hashKey) {
            $ref = $rowParams[$var[1]];
            
            if (!isset($ref[$var[2]])) {
                $errors[$var[2]] = "Error in column `{$key}`. key '{$var[0]}' untuk kondisi lookup tidak ditemukan!";
            } else {
                $hashKey[$var[2]] = $ref[$var[2]];
            }
            
            return @$ref[$var[2]];
        }, $col['condition']);
        
        ## if one of column condition variables is not found, then skip this row
        if (!empty($errors)) {
            return $errors;
        }
        
        $lrow = null;
        $hashKey = json_encode($hashKey);
        if (isset($this->lookup[$col['from']]['hash'][$hashKey])) {
            ## lookup from hash table if row is already queried
            $lrow = $this->lookup[$col['from']]['hash'][$hashKey];
        } else {
            ## if hashtable is not found then execute query
            $sql = " SELECT * FROM {$col['from']} WHERE {$condition}";
            $lrow = Yii::app()->db->createCommand($sql)->queryRow();
        }
        
        ## if lookup row are found, then
        $into = isset($col['into']) ? $col['into'] : $key;
        
        if ($lrow !== false && !empty($lrow)) {
            ## assign it into attributes
            if (is_string($col['return'])) {
                if (isset($lrow[$col['return']])) {
                    $attrs[$into] = $lrow[$col['return']];
                } else {
                    throw new CException("Key `return` tidak bisa diambil dari kolom " . $col['return'] . " (tidak ditemukan)");
                }
            } else if (is_array($col['return'])) {
                foreach ($col['return'] as $k=>$r) {
                    $attrs[$k] = $lrow[$r];
                }
            }
            
            if (!is_null($returnRows)) {
                $returnRows = $lrow;
            }
            
            $this->lookup[$col['from']]['hash'][$hashKey] = $lrow;
        } else {
            if (!isset($col['notfound'])) {
                throw new CException($this->lookupError($row, $col, $key));
            }
            
            ## lookup is NOT found, if there is notfound stetement then execute it
            switch (@$col['notfound']['action']) {
                case 'return':
                    $attrs[$into] = $row[$key];
                    $returnRows[$into] = $attrs[$into];
                    break;
                case 'insert':
                    $from = $col['from'];
                    if (isset($col['notfound']['to'])) {
                        $from = $col['notfound']['to'];
                    }
                    
                    if (!isset($this->lookup[$from])) {
                        $this->lookup[$from] = [
                            'schema' => Yii::app()->db->schema->tables[$from],
                            'hash' => [
                                $key => []
                            ]
                        ];
                    }
                    $pk = $this->lookup[$from]['schema']->primaryKey;
                    if (!is_string($pk)) {
                        return 'Import does not support inserting multiple primary key in lookup!';
                    }
                    
                    ## insert data
                    $insert = $col['notfound']['data'];
                    foreach ($insert as $k=>$i) {
                        if (is_array($i) && $i['type'] == 'function' && isset($i['value'])) {
                            $i = $i['value'];
                        }
                        
                        if (is_array($i)) {
                            switch ($i['type']) {
                                case 'lookup':
                                    $this->lookup($insert, $i, $k, array_merge($row, $attrs));
                                    break;
                            }
                        } else if (is_string($i)) {
                            $rowParams = $this->rowParams(array_merge($row, $attrs));
                            try {
                                $insert[$k] = $this->evalExpr($i, $rowParams);
                                
                            } catch (Exception $e) {
                                throw new CException("
Kolom " . $key . " tidak ditemukan, <br/>
akan menambahkan kolom " . $key . " dengan data sebagai berikut:<br/>
<br/>
<pre>" . json_encode($insert, JSON_PRETTY_PRINT) . "</pre>
<br/>
penambahan gagal dikarenakan data " . $k . " tidak dapat ditemukan ($i)");
                            }
                        }
                    }
                    
                    Yii::app()->db->createCommand()->insert($from, $insert);
                    $insert[$pk] = Yii::app()->db->getLastInsertID(); 
                    $this->lookup[$from]['hash'][$hashKey] = $insert;
                    
                    ## assign inserted id into attrs
                    if (!isset($col['notfound']['return'])) {
                        $attrs[$into] = $insert[$col['return']];
                        $returnRows[$into] = $attrs[$into];
                    } else {
                        if (is_string($col['notfound']['return'])) {
                            $attrs[$into] = $insert[$col['notfound']['return']];
                            $returnRows[$into] = $attrs[$into];
                        } else if (is_array($col['notfound']['return'])) {
                            foreach ($col['notfound']['return'] as $nk => $nr) {
                                $attrs[$nk] = $insert[$nr];
                                $returnRows[$nk] = $attrs[$nk];
                            }
                        }
                    }
                break;
                case 'lookup':
                    $coldef= $col['notfound'];
                    unset($coldef['action']);
                    return $this->lookup($attrs, $coldef, $key, array_merge($row, $attrs));
                    break;
                case 'function': 
                    ## evaluate function, using parameters
                    if (is_array($col['notfound']['value'])) {
                        foreach ($col['notfound']['value'] as $k=>$v) {
                            $attrs[$k] = $this->evalExpr($v, $this->rowParams($row));
                            $returnRows[$k] = $attrs[$k];
                        }
                        
                    } else {
                        $attrs[$into] = $this->evalExpr($col['notfound']['value'], $this->rowParams($row));
                        $returnRows[$into] = $attrs[$into];
                    }
                    break;
                case 'error':
                    return $this->lookupError($row, $col, $key);
                    break;
            }
        }
        
        return true;
    }
    
    private function lookupError($row, $col, $key) {
        $msg = @$col['notfound']['msg'];
        
        if ($msg == "") {
            $msg = "'<br/> tidak dapat menemukan {$key} dengan data `{row.$key}`'";
        }
        $rowParams = $this->rowParams($row);
        return $this->evalExpr($msg, $rowParams);
    }
    
    private function rowParams($row) {
        $parent = null;
        if (!is_null($this->parent)) {
            $parent = $this->parent->currentRow;
            $pk = $this->parent->model->tableSchema->primaryKey;
            
            if (!isset($parent[$pk]) && isset($this->parent->lastRow[$pk])) {
                $parent[$pk] = $this->parent->lastRow[$pk]; 
            }
        }
        
        $lastRow = $this->lastRow;
        $lastFilledRow = $this->lastFilledRow;
        
        $root = null;
        if (!is_null($this->root)) {
            $root = $this->root->currentRow;
            $lastRow = $this->root->lastRow;
        }
        
        return [
            'row' => $row,
            'lastRow' => $lastRow,
            'lastFilledRow' => $lastFilledRow,
            'parent' => $parent,
            'root' => $root
        ];
    }
    
    private function evalExpr($expr, $rowParams, $addQuote = true) {
        
        $markNull = false;
        $eval = preg_replace_callback( "/{([^.}]*)\.?([^}]*)}/", 
                function($var) use($expr, $rowParams, &$markNull) {
                    $nullable = false;
                    if ($var[1][0] == '*') {
                        $var[1] = trim($var[1], '*');
                        $nullable = true;
                    }
                    
                    $ref = $rowParams[$var[1]];
        
                    if (!isset($ref[$var[2]])) {
                        if ($var[1] == "lastFilledRow") {
                            $ref[$var[2]] = null;
                        } else {
                            throw new CException("Undefined index `{$var[2]}`");
                        }
                    }
                    
                    if (is_object($ref[$var[2]]) ) {
                        if ($ref[$var[2]] instanceof DateTime) {
                            $ref[$var[2]] = $ref[$var[2]]->format('Y-m-d H:i:s');
                        }
                    }
                    
                    if ($ref[$var[2]] == '' && $nullable) {
                        $ref[$var[2]] = null;
                        $markNull = true;
                    }
                    
                    if (is_null($ref[$var[2]]) && $var[1] != "lastFilledRow") {
                        $markNull = true;
                    }
                    
                    return $ref[$var[2]];
                }, $expr);
        
        
        if ($markNull) {
            return null;
        }
        
        if ($addQuote && $expr[0] == "{" && $expr[strlen($expr) -1] == "}" ) {
            return $eval;
        }
        
        return Helper::evaluate($eval, $rowParams);
    }
    
    public function importRow($row, $params = []) {
        if (!$this->loaded) {
            throw new CException('Import configuration must be loaded before importing!');
        }
        
        if (is_null($this->root)) {
            $this->parent = null;
            $this->lastRow = $this->currentRow;
            $this->currentRow = null;
            $this->rowIndex++;
            
            if (!is_null($this->lastRow)) {
                foreach ($this->lastRow as $k => $r) {
                    if (function_exists('iconv')) {
                        $r = trim(iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $r));
                    } else {
                        $r = trim($r);
                    }
                    
                    if ($r != "" && !is_null($r)) {
                        $this->lastFilledRow[$k] = $r;
                    }
                }
            }
            
        } else {
            $this->lastRow = $this->root->lastRow;
            $this->lastFilledRow = $this->root->lastFilledRow;
        }

        $modelClass = $this->modelClass;
        $attrs = [];
        $pks = [];
        $data = [];
        $resolveCol = [];

        ## skip data if needed
        $skipIf = false;
        $skipParentIf = false;
        $skipChildIf = false;
        if (isset($this->skip['if'])) $skipIf = $this->skip['if'];
        if (isset($this->skip['parentIf'])) $skipParentIf = $this->skip['parentIf'];
        if (isset($this->skip['childIf'])) $skipChildIf = $this->skip['childIf'];

        ## assign current row
        foreach ($row as $k=>$r) {
            if (is_string($r)) {
                if (function_exists('iconv')) {
                    $row[$k] = trim(iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $r));
                } else {
                    $row[$k] = trim($r);
                }
            }
        }
        
        $this->currentRow = $row;
        $this->originalRow = $row;
        if (is_null($this->root)) {
            foreach ($this->currentRow as $k => $r) {
                if ($this->currentRow[$k] != "" && !is_null($this->currentRow[$k])) {
                    $this->lastFilledRow[$k] = $this->currentRow[$k];
                }
            }
        }
        
        ## execute child if needed
        $executeChild = true;
        if ($skipIf === true) {
            $executeChild = false;
        } else if (is_string($skipParentIf)) {
            $rowParams = $this->rowParams($row);
            $skipParentIf = $this->evalExpr($skipParentIf, $rowParams + $params); 
        }
        
        ## execute function when beforeLookup
        foreach ($this->columns as $key => $col) {
            if (@$col['type'] == 'function') {
                if (@$col['when'] == 'beforeLookup') {
                    $rowParams = $this->rowParams($row);
                    $into = isset($col['into']) ? $col['into'] : $key;
                    $attrs[$into] = $this->evalExpr($col['value'], $rowParams + $params); 
                    
                    if (@$col['show'] === true) {
                        $data[$key] = $row[$key];
                        
                        if ($row[$key] != '' && !is_null($row[$key])) {
                            if (is_null($this->root)) {
                                $this->lastFilledRow[$key] = $row[$key];
                            } else {
                                $this->root->lastFilledRow[$key] = $row[$key];
                            }
                        }
                    }
                }
            }
        }
        $this->currentRow = $data;
        
        ## loop each column, and determine how to fill it's value
        foreach ($this->columns as $key => $col) {
            switch ($col['type']) {
                case 'pk':
                    if (@$row[$key] != '') {
                        $pks[$key] = $row[$key];
                        $attrs[$key] = $row[$key];
                        if (is_null($this->root)) {
                            $this->lastFilledRow[$key] = $row[$key];
                        } else {
                            $this->root->lastFilledRow[$key] = $row[$key];
                        }
                    } 
                    $resolveCol[] = $key;
                    $data[$key] = @$row[$key];
                    if (isset($col['skipIf'])) {
                        $skipIf = $col['skipIf'];
                    }
                    if (isset($col['skipParentIf'])) {
                        $skipParentIf = $col['skipParentIf'];
                    }
                    if (isset($col['skipChildIf'])) {
                        $skipChildIf = $col['skipChildIf'];
                    }
                    break;
                case 'default':
                    
                    if (!isset($row[$key]) && !is_null($row[$key])) {
                        return [[
                            $key => 'Field ' . $key . ' tidak ada!'
                        ]];
                    }
                    
                    $resolveCol[] = $key;
                    if (isset($this->model->tableSchema->columns[$key]) && ($row[$key] == '' && $this->model->tableSchema->columns[$key]->isForeignKey)) {
                        continue;
                    }
                    
                    $rowVal = $row[$key];
                    if (isset($col['format'])) {
                        switch ($col['format']) {
                            case "date":
                                $rowVal = date('Y-m-d', strtotime($rowVal));
                                if ($rowVal == '1970-01-01') {
                                    $rowVal = null;    
                                }
                            break;
                            case "datetime":
                                $test = date('Y-m-d', strtotime($rowVal));
                                if ($test == '1970-01-01') {
                                    $rowVal = null;    
                                } else {
                                    $rowVal = date('Y-m-d H:i:s', strtotime($rowVal));
                                }
                            break;
                        }
                    }
                    
                    $attrs[$key] = $rowVal;
                    $data[$key] = $rowVal;
                    
                    if ($rowVal != '' && $rowVal != null) {
                        if (is_null($this->root)) {
                            $this->lastFilledRow[$key] = $rowVal;
                        } else {
                            $this->root->lastFilledRow[$key] = $rowVal;
                        }
                    }
                    break;
                case 'lookup':
                    $retResult = [];
                    try {
                        $result = $this->lookup($attrs, $col, $key, array_merge($row, $attrs, $data), $retResult);
                    } catch (Exception $e) {
                        return [[
                            $key => "Error in column {$key}: " .  $e->getMessage()
                        ]];
                    }
                    
                    if (is_array($col['return'])) {
                        foreach($col['return'] as $k => $r) {
                            if (is_null($this->root)) {
                                $this->lastFilledRow[$k] = @$retResult[$r];
                            } else {
                                $this->root->lastFilledRow[$k] = @$retResult[$r];
                            }
                        }
                    }
                    
                    if (@$col['show'] !== false) {
                        $into = isset($col['into']) ? $col['into'] : $key;
                        if (isset($attrs[$into])) {
                            $data[$into] = $attrs[$into];
                            
                            if ($attrs[$into] != '' && $attrs[$into] != null) {
                                if (is_null($this->root)) {
                                    $this->lastFilledRow[$into] = $attrs[$into];
                                } else {
                                    $this->root->lastFilledRow[$into] = $attrs[$into];
                                }
                            }
                        } 
                        else if (isset($row[$into])) {
                            $data[$key] = $row[$into];
                            
                            if ($row[$into] != '' && $row[$into] != null) {
                                if (is_null($this->root)) {
                                    $this->lastFilledRow[$key] = $row[$into];
                                } else {
                                    $this->root->lastFilledRow[$key] = $row[$into];
                                }
                            }
                        } 
                        
                    }
                    
                    break;
            }
        }
        $this->currentRow = $data;

        ## execute function when afterLookup
        foreach ($this->columns as $key => $col) {
            if (@$col['type'] == 'function') {
                if (@$col['when'] == 'afterLookup' || !isset($col['when'])) {
                    $rowParams = $this->rowParams($data);
                    $into = isset($col['into']) ? $col['into'] : $key;
                    
                    $attrs[$into] = $this->evalExpr($col['value'], $rowParams + $params);
                    
                    if (@$col['show'] === true) {
                        $data[$key] = $row[$key];
                        if (is_null($this->root)) {
                            $this->lastFilledRow[$key] = $row[$key];
                        } else {
                            $this->root->lastFilledRow[$key] = $row[$key];
                        }
                    }
                }
            }
        }
        $this->currentRow = $data;
        
        ## load model class when available, insert it when not exist
        $model = null;
        if (!empty($pks)) {
            $model = $modelClass::model()->findByAttributes($pks);
        }
        if (is_null($model)) {
            $model = new $modelClass;
        }
        
        ## assign row vars, then save it
        foreach ($attrs as $k=>$a) {
            if (is_object(@$attrs[$k]) ) {
                if ($attrs[$k] instanceof DateTime) {
                    $attrs[$k] = $attrs[$k]->format('Y-m-d H:i:s');
                }
            }
        }
        if (is_string($skipIf)) {
            $rowParams = $this->rowParams($data);
            $skipIf = $this->evalExpr($skipIf, $rowParams + $params);
        }
        foreach ($attrs as $k =>$v) {
            $attrs[$k] = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $v);
        }
        $model->attributes = $attrs;
        
        
        if ($executeChild) {
            if ($skipParentIf !== true) {
                $model->save();
                
                if (is_string($this->model->tableSchema->primaryKey)) {
                    $data[$model->tableSchema->primaryKey] = $model->{$model->tableSchema->primaryKey};
                    if (is_null($this->root)) {
                        $this->lastFilledRow[$model->tableSchema->primaryKey] = $model->{$model->tableSchema->primaryKey};
                    } else {
                        $this->root->lastFilledRow['_' . $modelClass . '_id'] = $model->{$model->tableSchema->primaryKey}; 
                    }
                }
                
                if (is_null($this->root)) {
                    foreach ($this->currentRow as $k => $r) {
                        if ($this->currentRow[$k] != "" && !is_null($this->currentRow[$k])) {
                            $this->lastFilledRow[$k] = $this->currentRow[$k];
                        }
                    }
                }
            }
        }
        $this->currentRow =  array_merge($row, $attrs, $data);
        
        foreach ($resolveCol as $rc){
            if (isset($this->ignoreCols[$rc])) continue;
            $data[$rc] = $model->{$rc};
        }
        
        if (!$model->hasErrors() || $skipParentIf === true) {
            if ($executeChild) {
                foreach ($this->relations as $rname=>$rel) {
                    $initData =  array_merge($row, $attrs, $data);
                    if (isset($rel['condition'])) {
                        $expr = preg_replace_callback( "/{([^.}]*)\.?([^}]*)}/", 
                            function($var) use($initData) {
                                return @$initData[$var[2]];
                            }, $rel['condition']);
                        
                        if (!Helper::evaluate($expr)) {
                            continue;
                        }
                    }
                    
                    $relAttrs = $initData;
                    foreach ($rel['columns'] as $key=>$col) {
                        $rkey = $rname . '_' . $key;
                        
                        if (isset($row[$rkey])) {
                            $relAttrs[$key] = $row[$rkey];
                        }
                    }
                    
                    $rel['import']->originalRow = $this->originalRow;
                    $rel['import']->lastRow = $this->lastRow;
                    $rel['import']->lastFilledRow = $this->lastFilledRow;
                    $ro = $this->originalRow;
                    
                    
                    $res = $rel['import']->importRow($this->originalRow);
                    if ($res !== true) {
                        
                        $errors = [];
                        foreach ($res as $k=>$e) {
                            $errors[] = $k . " => " . @$e[0];
                        }
                        
                        return [
                            'relation ' . $rname => $errors
                        ];
                    }
                }
                
                ## execute function when afterSave
                if ($skipParentIf !== true) {
                    foreach ($this->columns as $key => $col) {
                        if (@$col['type'] == 'function') {
                            if (@$col['when'] == 'afterSave') {
                                $expr = preg_replace_callback( "/{([^.}]*)\.?([^}]*)}/", 
                                    function($var) use($key, $data, $col) {
                                        switch ($var[1]) {
                                            case 'row': $ref = $data; break;
                                            case 'lastRow': $ref = $this->lastRow; break;
                                        }
                                        
                                        if (is_object(@$ref[$var[2]]) ) {
                                            if ($ref[$var[2]] instanceof DateTime) {
                                                $ref[$var[2]] = $ref[$var[2]]->format('Y-m-d H:i:s');
                                            }
                                        }
                                
                                        return @$ref[$var[2]];
                                    }, $col['value']);
                                
                                $into = isset($col['into']) ? $col['into'] : $key;
                                
                                if ($col['value'][0] == "{" && $col['value'][strlen($col['value']) - 1] == "}") {
                                    $expr = '"' . $expr . '"';
                                }
                                
                                $attrs[$into] = Helper::evaluate($expr, [
                                    'row'=> $row,
                                    'lastRow' => $this->lastRow
                                ] + $params);
                                
                                if (@$col['show'] === true) {
                                    $data[$key] = $row[$key];
                                }
                            }
                        }
                    }
                }
            } 
        
            $this->rowHistory[] = $row;
            return true;
        } else {
            return $model->errors;
        }
    }
    
    public function saveExcel() {
        if (!empty($this->rowHistory)) {
            $data = $this->rowHistory;
            array_unshift($data, array_keys($data[0]));
            
            $path = Yii::getPathOfAlias('root.assets.import');
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
                chmod($path, 0755);
            }
            
            $filename = "import-". Helper::camelToSnake($this->modelClass) . '-' . date("Y-m-d~H.i.s"). '.xlsx';

            $this->resultFile = $path . DIRECTORY_SEPARATOR . $filename;
            $this->resultUrl = Yii::app()->baseUrl . '/assets/import/' . $filename;
            
            $writer = WriterFactory::create(Type::XLSX);
            $writer->openToFile($this->resultFile); 
            foreach ($data as $k=>$d) {
                foreach ($d as $dk => $dd) {
                    if (is_object(@$data[$k][$dk]) ) {
                        if ($data[$k][$dk] instanceof DateTime) {
                            $data[$k][$dk] = $data[$k][$dk]->format('Y-m-d H:i:s');
                        }
                    }
                }
            }
            $writer->addRows($data); 
            $writer->close();
            
            return $this->resultUrl;
        }
    }
    
    public function __construct($model, $defaultConfig = [], $root = null, $parent = null) {
        $this->loadConfig($model, $defaultConfig, $root, $parent);
    }
    
}
    