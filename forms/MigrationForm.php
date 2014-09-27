<?php

class MigrationForm extends form { 
    public $idx;
    public $name;
    public $newsql = '';
    public $migrations = array();
    
    public function init() {
        $dir = Yii::getPathOfAlias('app.migrations');
        
        if (!is_dir($dir)) {
            mkdir($dir);
            mkdir($dir . DIRECTORY_SEPARATOR . '1');
        }

        $mig_dir = glob($dir . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR);

        $mig = array();
        $migrations = array();
        foreach ($mig_dir as $d) {
            $sqls = glob($d . DIRECTORY_SEPARATOR . "*.sql");
            $idx = str_replace($dir . DIRECTORY_SEPARATOR, '', $d);
            if (!isset($mig[$idx])) {
                $mig = array($idx => array()) + $mig;
            }

            foreach ($sqls as $sql_file) {
                $data = file_get_contents($sql_file);
                $file = str_replace($d .  DIRECTORY_SEPARATOR, '', $sql_file);
                $mig[$idx][$file] = $data;
            }
        }
        foreach ($mig as $id=>$m) {
            $migrations[] = array(
                'id' => $id,
                'items' => $m
            );
        }
        
        $this->migrations = $migrations;
        $this->name = Setting::get('db', 'migration_name');
        $this->idx = Setting::get('db', 'migration_idx');
    }
    
    public function getForm() {
        return array (
            'title' => 'Database Migration',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'includeJS' => array (),
            'inlineJS' => 'migration.js',
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
                            'ng-if' => 'migration',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'label' => 'Submit',
                        'buttonType' => 'success',
                        'options' => array (
                            'ng-click' => 'form.submit(this)',
                            'ng-if' => 'migration',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'label' => 'New Migration',
                        'buttonType' => 'success',
                        'icon' => 'plus',
                        'options' => array (
                            'ng-click' => 'toggleMigration()',
                            'ng-if' => '!migration',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'label' => 'Run All',
                        'buttonType' => 'success',
                        'options' => array (
                            'ng-click' => 'runMigration()',
                            'ng-if' => '!migration',
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
                'value' => '<div ng-if=\\"migration\\" class=\\"panel panel-default\\">',
                'type' => 'Text',
            ),
            array (
                'value' => '
    <div class=\"panel-heading\" style=\"padding:5px;\">',
                'type' => 'Text',
            ),
            array (
                'value' => '                <input id=\\"name\\" name=\\"name\\" placeholder=\\"Your Name\\" type=\\"text\\" class=\\"form-control\\">',
                'type' => 'Text',
            ),
            array (
                'value' => '        </div>',
                'type' => 'Text',
            ),
            array (
                'value' => '
    <div class=\"panel-body\" style=\"padding:5px 5px 0px 5px\">',
                'type' => 'Text',
            ),
            array (
                'label' => 'New Migration',
                'fieldname' => 'newsql',
                'language' => 'sql',
                'options' => array (
                    'ng-model' => 'model.newsql',
                ),
                'type' => 'ExpressionField',
            ),
            array (
                'value' => '    </div>
</div>',
                'type' => 'Text',
            ),
            array (
                'value' => '<div ng-if=\"!migration\" class=\"panel panel-{{ model.idx <= m.id ? \'default\' : \'success\' }}\" ng-repeat=\"m in model.migrations\" style=\"margin-bottom:10px;u\">
    <div class=\"panel-heading\" style=\"padding:5px 10px;cursor:pointer; -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none; \" ng-click=\"m.show = !m.show\">
    Migration #{{m.id}}
    </div>
    <div ng-if=\"m.show === true\" class=\"panel-body\" style=\"padding:0px;\">
        <div ng-repeat=\"(file, sql) in m.items\" class=\"panel panel-default\" style=\"margin:5px;\">
            <div class=\"panel-heading\" style=\"padding:5px 10px;\">
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
            array (
                'value' => '</div>',
                'type' => 'Text',
            ),
        );
    }

}