<?php

use PhpParser\Error;
use PhpParser\ParserFactory;

class ControllerGenerator extends CComponent
{


    public static function create($path, $name)
    {
        $parser        = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $prettyPrinter = new CodePrinter;
        $path          = explode('.', $path);

        $controllerPath = '';
        if (count($path) == 1) {
            $controllerPath = $path[0].'.controllers.'.$name;
        } else if (count($path) == 2) {
            $controllerPath = $path[0].'.modules.'.$path[1].'.controllers.'.$name;
        }

        try {
            $templatePath   = Yii::getPathOfAlias('application.components.codegen.templates.controller').'.php';
            $template       = file_get_contents($templatePath);
            $stmts          = $parser->parse($template);
            $stmts[0]->name = ucfirst($name);
            $generated      = $prettyPrinter->prettyPrintFile($stmts);

            $cp = Yii::getPathOfAlias($controllerPath).'.php';
            if (!is_dir(dirname($cp))) {
                mkdir(dirname($cp), 0755, true);
                chmod(dirname($cp), 0755);
            }

            file_put_contents($cp, $generated);
            return true;
        } catch (Exception $e) {
            return false;
        }

    }//end create()


    public static function listCtrlForMenuTree()
    {
        $list    = [];
        $devMode = Setting::get('app.mode') === 'plansys';
        if ($devMode) {
            $dir         = Yii::getPathOfAlias('application.modules').DIRECTORY_SEPARATOR;
            $items       = glob($dir.'*', GLOB_ONLYDIR);
            $plansysList = [];

            foreach ($items as $k => $f) {
                $label     = str_replace($dir, '', $f);
                $classPath = $f.DIRECTORY_SEPARATOR.ucfirst($label).'Module.php';
                if (is_file($classPath)) {
                    $ctrlsDir = $f.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR;
                    $ctrls    = self::listCtrlInDir('plansys.'.$label, $ctrlsDir);

                    $plansysList[] = [
                                      'label'  => $label,
                                      'module' => 'plansys',
                                      'items'  => $ctrls,
                                      'target' => 'col2',
                                     ];
                }
            }

            $ctrlsDir = Yii::getPathOfAlias('application.controllers').DIRECTORY_SEPARATOR;
            $ctrls    = self::listCtrlInDir('plansys', $ctrlsDir);
            foreach ($ctrls as $ctrl) {
                $plansysList[] = $ctrl;
            }

            $list[] = [
                       'label'  => 'Plansys',
                       'module' => 'plansys',
                       'items'  => $plansysList,
                      ];
        }//end if

        $dir     = Yii::getPathOfAlias('app.modules').DIRECTORY_SEPARATOR;
        $items   = glob($dir.'*', GLOB_ONLYDIR);
        $appList = [];
        foreach ($items as $k => $f) {
            $label     = str_replace($dir, '', $f);
            $classPath = $f.DIRECTORY_SEPARATOR.ucfirst($label).'Module.php';
            if (is_file($classPath)) {
                $ctrlsDir = $f.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR;
                $ctrls    = self::listCtrlInDir('app.'.$label, $ctrlsDir);

                $appList[] = [
                              'label'  => $label,
                              'module' => 'app',
                              'items'  => $ctrls,
                              'target' => 'col2',
                             ];
            }
        }

        $ctrlsDir = Yii::getPathOfAlias('app.controllers').DIRECTORY_SEPARATOR;
        $ctrls    = self::listCtrlInDir('app', $ctrlsDir);
        foreach ($ctrls as $ctrl) {
            $appList[] = $ctrl;
        }

        $list[] = [
                   'label'  => 'App',
                   'module' => 'app',
                   'items'  => $appList,
                  ];
        return $list;

    }//end listCtrlForMenuTree()


    private static function listCtrlInDir($module, $ctrlDir)
    {
        $ctrlRaw = glob($ctrlDir.'*.php');
        $ctrls   = [];
        $path    = explode('.', $module);
        $m       = $path[0] != 'plansys' ? 'app' : 'application';

        $controllerPath = '';
        if (count($path) == 1) {
            $controllerPath = $m.'.controllers.';
        } else if (count($path) == 2) {
            $controllerPath = $m.'.modules.'.$path[1].'.controllers.';
        }

        foreach ($ctrlRaw as $ctrl) {
            $c       = str_replace([$ctrlDir, '.php'], '', $ctrl);
            $ctrls[] = [
                        'label'  => $c,
                        'icon'   => 'fa-slack',
                        'active' => @$_GET['active'] == $module.'.'.$c,
                        'url'    => Yii::app()->controller->createUrl(
                            '/dev/genCtrl/index',
                            [
                             'active' => $module.'.'.$c,
                            ]
                        ),
                        'class'  => $controllerPath.$c,
                        'target' => 'col2',
                       ];
        }

        return $ctrls;

    }//end listCtrlInDir()


}//end class
