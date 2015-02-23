<?php

class MigrationForm extends Form {

    public $idx;
    public $name;
    public $newsql = '';
    public $isNew = false;
    public $migrations = array();
    public $errors = array();
    public $done;
    

    public function getList() {
        $list = Setting::get('db.migration_list');
        $list = (is_null($list) ? array() : $list);
        return $list;
    }
    
    public function setList($v) {
        Setting::set("db.migration_list", $v);
    }
    
    public function addList($v) {
        $list = $this->list;
        if (array_search($v, $list) === false) {
            $list[] = $v;
        }
        $this->list = $list;
    }
    
    
    public function newMigration($post) {
        Setting::set('db.migration_name', $post['name']);

        $sql = $post['newsql'];
        $valid = $this->run($sql);
        
        if ($valid) {
            $valid = $this->run($sql);

            if ($valid) {
                $dir = Yii::getPathOfAlias('app.migrations');
                $migDir = glob($dir . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR);

                $newDir = $dir . DIRECTORY_SEPARATOR . (count($migDir) + 1);
                if (!is_dir($newDir)) {
                    mkdir($newDir);
                }
                $newFile = $newDir . DIRECTORY_SEPARATOR . $post['name'] . '.sql';
                file_put_contents($newFile, $sql);

                $this->addList((count($migDir) + 1) . "_" . $post['name'] . ".sql");
            }
        }
        return $valid;
    }

    public function runFile($id, $file) {
        $path = Yii::getPathOfAlias('app.migrations.' . $id) . DIRECTORY_SEPARATOR . $file;
        $sql = file_get_contents($path);
        $this->addList("{$id}_{$file}");
        return $this->run($sql);
    }

    public function run($sql) {
        $success = true;
        if (trim($sql) == "")
            return false;

        $transaction = Yii::app()->db->beginTransaction();
        try {
            Yii::app()->db->createCommand($sql)->execute();
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            $success = false;
            $message = str_replace("CDbCommand gagal menjalankan statement", "CDbCommand gagal menjalankan statement\n", $e->getMessage());
            $this->errors['newsql'][] = $message;
        }

        return $success;
    }

    public function init() {
        $dir = Yii::getPathOfAlias('app.migrations');

        if (!is_dir($dir)) {
            mkdir($dir);
            mkdir($dir . DIRECTORY_SEPARATOR . '1');
        }

        $migDir = glob($dir . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR);
        natsort($migDir);
        
        $mig = array();
        $migrations = array();
        foreach ($migDir as $d) {
            $sqls = glob($d . DIRECTORY_SEPARATOR . "*.sql");
            $idx = str_replace($dir . DIRECTORY_SEPARATOR, '', $d);
            if (!isset($mig[$idx])) {
                $mig = array($idx => array()) + $mig;
            }

            foreach ($sqls as $sql_file) {
                $data = file_get_contents($sql_file);
                $file = str_replace($d . DIRECTORY_SEPARATOR, '', $sql_file);
                $mig[$idx][$file] = $data;
            }
        }
        foreach ($mig as $id => $m) {
            $migrations[] = array(
                'id' => $id,
                'items' => $m
            );
        }
        
        
        $this->migrations = $migrations;
        $this->name = Setting::get('db.migration_name', '');
        $this->done = $this->list;
    }

    public function getForm() {
        return array(
            'title' => 'Database Migration',
            'layout' => array(
                'name' => 'full-width',
                'data' => array(
                    'col1' => array(
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'includeJS' => array(),
            'inlineJS' => 'MigrationForm.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Cancel',
                        'buttonType' => 'danger',
                        'options' => array (
                            'ng-click' => 'toggleMigration()',
                            'ng-if' => 'migration && !loading',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'label' => 'Submit',
                        'buttonType' => 'success',
                        'options' => array (
                            'ng-click' => 'form.submit(this)',
                            'ng-if' => 'migration && !loading',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'label' => 'New Migration',
                        'buttonType' => 'success',
                        'icon' => 'plus',
                        'options' => array (
                            'ng-click' => 'toggleMigration()',
                            'ng-if' => '!migration && !loading && isMigrated()',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'value' => '<div style=\\"width:700px;margin:0px auto;padding-top:20px;\\">',
                'type' => 'Text',
            ),
            array (
                'value' => '<div class=\"panel panel-default\" style=\"margin-bottom:5px;\">
    <div class=\"panel-heading\" 
    style=\"padding:10px;font-size:13px;border:0px;\">
        <div class=\"migration-all-container\">
        <div ng-click=\"migrateAll($event)\" ng-if=\"!migration && !loading && !isMigrated()\" class=\"btn btn-sm btn-success migration-all\">
            Migrate All
        </div></div>
    
        <b ng-bind-html=\"status\"></b>
    </div>
</div>',
                'type' => 'Text',
            ),
            array (
                'value' => '<div ng-if=\\"migration\\" class=\\"panel panel-default\\">',
                'type' => 'Text',
            ),
            array (
                'value' => '
    <div class=\"panel-heading\" style=\"padding:5px;\">',
                'type' => 'Text',
            ),
            array (
                'value' => '<input id=\\"name\\" name=\\"name\\" placeholder=\\"Your Name\\" type=\\"text\\" class=\\"form-control\\" value=\\"{{model.name}}\\">',
                'type' => 'Text',
            ),
            array (
                'value' => '</div>',
                'type' => 'Text',
            ),
            array (
                'value' => '<div class=\\"panel-body\\" style=\\"padding:5px 5px 0px 5px\\">',
                'type' => 'Text',
            ),
            array (
                'label' => 'New Migration',
                'fieldname' => 'newsql',
                'language' => 'sql',
                'options' => array (
                    'ng-model' => 'model.newsql',
                    'name' => 'newsql',
                ),
                'desc' => 'Pastikan SQL Anda dapat dijalankan berkali-kali. <br/>
SQL ini akan dijalankan 2x untuk memastikan tidak ada error.',
                'type' => 'ExpressionField',
            ),
            array (
                'value' => '    </div>
</div>',
                'type' => 'Text',
            ),
            array (
                'value' => '<div 
    ng-if=\"!migration\" 
    class=\"migration-item panel panel-{{ panelClass(m) }}\" 
    ng-repeat=\"m in model.migrations\">
    
        <div class=\"panel-heading migration-item-head\"  ng-click=\"m.show = !m.show\">
        <div ng-if=\"!loading\" 
        class=\"btn btn-xs pull-right btn-success\" 
        ng-click=\"migrateId($event,m.id)\">Run #{{m.id}}</div>
        Migration #{{m.id}}
    </div>
    <div ng-if=\"m.show === true\" class=\"panel-body\" style=\"padding:0px;\">
        <div ng-repeat=\"(file, sql) in m.items\" 
             class=\"{{fileClass(m,file)}}\">
            <div ng-click=\"m[\'show\' + file] = !m[\'show\' + file]\" 
                 class=\"panel-heading\" style=\"padding:5px 10px;\">
                <div 
                    ng-if=\"!loading\" 
                    class=\"btn btn-xs pull-right btn-info\" 
                    ng-click=\"run($event,m.id,file)\">Run</div>
                {{file}}
            </div>
            
            <div ng-if=\"m[\'show\' + file] === true\" class=\"panel-body\" 
                 style=\"padding:5px 10px;white-space:pre-wrap;font-size:12px;\">{{sql}}</div>
        </div>
    </div>
</div>',
                'type' => 'Text',
            ),
            array (
                'value' => '</div>',
                'type' => 'Text',
            ),
        );
    }

}