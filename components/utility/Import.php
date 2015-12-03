<?php
class Import extends CComponent {
    
    const ETL_PATH = 'app.config.etl';
    public $config = [];
    public $mode = 'default';
    public $columns = [];
    public $model = '';
    
    public function loadConfig($model, $configName = '') {
        $dir = Yii::getPathOfAlias(Import::ETL_PATH);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        $configSuffix = ($configName != '' ? ".{$configName}" : "");
        $filePath = $dir . DIRECTORY_SEPARATOR . $model . $configSuffix . ".php";
        if (is_file($filePath)) {
            $config = include($filePath);
            if (is_null($config)) {
                throw new CException('Failed to read config file `' . $filePath . '`. Please fix your config!');
            }
            
            if (!class_exists($model)) {
                throw new CException('Failed to load `' . $model . '` Class. Class does not exist!');
            }
            
            if (!is_subclass_of($model, 'ActiveRecord')) {
                throw new CException('Failed to load `' . $model . '`. Model must extends from ActiveRecord Class!');
            }
            
            $this->model = new $model;
            $this->mode = isset($config['mode']) ? $config['mode'] : 'default';
            
            switch ($this->mode) {
                case 'default':
                    if (isset($config['columns'])) {
                        
                    }
                    break;
            }
            
            $this->config = $config;
            $this->loaded = true;
            return true;
        } else {
            throw new CException('File `' . $filePath . '` does not exist!');
        }
    }
    
    public function importRow($row, $rowIndex = false) {
        if (!$this->loaded) {
            throw new CException('Import configuration must be loaded before importing!');
        }
        
        $modelClass = $this->model;
        $attrs = [];
        $pks = [];
        
        ## loop each column, and determine how to fill it's value
        foreach ($this->columns as $key => $col) {
            if (isset($row[$key])) {
                $conf = is_string($col) ? [
                    'type' => $col
                ] : $col;
                
                switch ($conf['type']) {
                    case 'pk':
                        $pks[$key] = $row[$key];
                        $attrs[$key] = $row[$key];
                        break;
                    case 'default':
                        $attrs[$key] = $row[$key];
                        break;
                    case 'function':
                        if (isset($row[$key]['function'])) {
                            $attrs[$key] = Helper::evaluate($row[$key]['function'], ['row'=>$row]);
                        }
                        break;
                    case 'lookup':
                        
                        break;
                }
            }
        }
        
        ## load model class when available, insert it when not exist
        $model = $modelClass::model()->findByAttributes($pks);
        if (is_null($model)) {
            $model = new $modelClass;
        }
        
        ## assign row vars, then save it
        $model->attributes = $attrs;
        if ($model->save()) {
            return true;
        } else {
            return $model->errors;
        }
    }
    
    public function __construct($model, $configName = '') {
        $this->loadConfig($model, $configName);
    }
    
}
    