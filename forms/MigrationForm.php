<?php

class MigrationForm extends form {

    public $idx;
    public $name;
    public $newsql = '';
    public $isNew = false;
    public $migrations = array();

    public function newMigration($post) {
        Setting::set('db.migration_name', $post['name']);

        $sql = $post['newsql'];
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
        }
        
        return $valid;
    }

    public function run($sql) {
        $sql = "SET FOREIGN_KEY_CHECKS = 0;\n" . $sql;
        $success = true;
        try {
            Yii::app()->db->pdoInstance->exec("SET FOREIGN_KEY_CHECKS = 0;" . $sql);
        } catch (PDOException $e) {
            $success = false;
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
        $this->idx = Setting::get('db.migration_idx', 0);
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
        return array(
            array(
                'linkBar' => array(
                    array(
                        'label' => 'Cancel',
                        'buttonType' => 'danger',
                        'options' => array(
                            'ng-click' => 'toggleMigration()',
                            'ng-if' => 'migration && !loading',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array(
                        'label' => 'Submit',
                        'buttonType' => 'success',
                        'options' => array(
                            'ng-click' => 'form.submit(this)',
                            'ng-if' => 'migration && !loading',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array(
                        'label' => 'New Migration',
                        'buttonType' => 'success',
                        'icon' => 'plus',
                        'options' => array(
                            'ng-click' => 'toggleMigration()',
                            'ng-if' => '!migration && !loading',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array(
                'value' => '<div style=\\"width:700px;margin:0px auto;padding-top:20px;\\">',
                'type' => 'Text',
            ),
            array(
                'value' => '<div class=\"panel panel-primary\" style=\"margin-bottom:5px;\">
    <div class=\"panel-heading\" 
    style=\"padding:10px;font-size:13px;border:0px;\">
        <div ng-if=\"!migration && !loading\" style=\"margin:-6px -6px;\" 
            class=\"btn btn-sm btn-success pull-right\">
            Run All
        </div>
    
        <b>{{ status }}</b>
    </div>
</div>',
                'type' => 'Text',
            ),
            array(
                'value' => '<div ng-if=\\"migration\\" class=\\"panel panel-default\\">',
                'type' => 'Text',
            ),
            array(
                'value' => '
    <div class=\"panel-heading\" style=\"padding:5px;\">',
                'type' => 'Text',
            ),
            array(
                'value' => '                                                                                                <input id=\\"name\\" name=\\"name\\" placeholder=\\"Your Name\\" type=\\"text\\" class=\\"form-control\\" value=\\"{{model.name}}\\">',
                'type' => 'Text',
            ),
            array(
                'value' => '                                                                                                                                </div>',
                'type' => 'Text',
            ),
            array(
                'value' => '                                                                <div class=\\"panel-body\\" style=\\"padding:5px 5px 0px 5px\\">',
                'type' => 'Text',
            ),
            array(
                'label' => 'New Migration',
                'fieldname' => 'newsql',
                'language' => 'sql',
                'options' => array(
                    'ng-model' => 'model.newsql',
                    'name' => 'newsql',
                ),
                'desc' => 'SQL ini akan dijalankan 2x untuk memastikan tidak ada error.',
                'type' => 'ExpressionField',
            ),
            array(
                'value' => '    </div>
</div>',
                'type' => 'Text',
            ),
            array(
                'value' => '<div 
    ng-if=\"!migration\" 
    class=\"migration-item panel panel-{{ model.idx <= m.id ? \'success\' : \'default\' }}\" 
    ng-repeat=\"m in model.migrations\">
        
        <div class=\"panel-heading migration-item-head\"  ng-click=\"m.show = !m.show\">
        <div ng-if=\"!loading\" 
        class=\"btn btn-xs pull-right btn-success\" 
        ng-click=\"run($event,m.id,null)\">Run #{{m.id}}</div>
       
        Migration #{{m.id}}
    </div>
    <div ng-if=\"m.show === true\" class=\"panel-body\" style=\"padding:0px;\">
        <div ng-repeat=\"(file, sql) in m.items\" 
             class=\"migration-file panel panel-info\">
            
            <div class=\"panel-heading\" style=\"padding:5px 10px;\">
                <div 
                    ng-if=\"!loading\" 
                    class=\"btn btn-xs pull-right btn-info\" 
                    ng-click=\"run($event,m.id,file)\">Run</div>
                    
                {{file}}
            </div>
            
            <div class=\"panel-body\" style=\"padding:5px 10px;\">
                {{sql}}
            </div>
        </div>
    </div>
</div>',
                'type' => 'Text',
            ),
            array(
                'value' => '</div>',
                'type' => 'Text',
            ),
        );
    }

}
