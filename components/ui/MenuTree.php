<?php

/**
 * Class MenuTree
 * @author rizky
 */
class MenuTree extends CComponent {

    public static function listAllFile() {
        $files = [];
        
        ## dev
        if (Setting::get('app.mode') == "plansys") {
            $dir = Yii::getPathOfAlias('application.modules');
            $modules = glob($dir . DIRECTORY_SEPARATOR . "*");
            foreach ($modules as $m) {
                $module = ucfirst(str_replace($dir . DIRECTORY_SEPARATOR, '', $m));
                $items = MenuTree::listFile($module);
                if (count($items) > 0) {
                    $files[] = [
                        'module' => $module,
                        'items' => $items
                    ];
                }
            }
        }

        ## app
        $dir = Yii::getPathOfAlias('app.modules');
        $modules = glob($dir . DIRECTORY_SEPARATOR . "*");
        foreach ($modules as $m) {
            $module = ucfirst(str_replace($dir . DIRECTORY_SEPARATOR, '', $m));
            $items = MenuTree::listFile($module);
            if (count($items) > 0) {
                $files[] = [
                    'module' => $module,
                    'items' => $items
                ];
            }
        }
        return $files;
    }

    public static function listFile($module) {
        $path = "application.modules." . lcfirst($module) . ".menus";
        $dir = Yii::getPathOfAlias($path);

        if (!is_dir($dir)) {
            $path = "app.modules." . lcfirst($module) . ".menus";
            $dir = Yii::getPathOfAlias($path);
        }

        $items = glob($dir . DIRECTORY_SEPARATOR . "*");
        foreach ($items as $k => $m) {
            $m = str_replace($dir . DIRECTORY_SEPARATOR, "", $m);
            $m = str_replace('.php', "", $m);

            $items[$k] = [
                'name' => $m,
                'module' => $module,
                'class' => $path . '.' . $m,
                'class_path' => $path
            ];
        }
        return $items;
    }

    public static function listHtml($module, $includeEmpty = true) {
        $raw = MenuTree::listFile($module);
        $list = [];
        foreach ($raw as $r) {
            $list[$r['class']] = $r['name'];
        }

        if ($includeEmpty) {
            $list[''] = "-- Empty --";
        }

        return $list;
    }

    public static function fillMenuItems(&$list) {
        if (!is_array($list)) {
            $list = [];
        }

        foreach ($list as $k => $v) {
            if (isset($v['url']) && is_string($v['url'])) {
                $list[$k]['url'] = str_replace('?', '&', $v['url']);
            }

            if (!isset($v['items'])) {
                $list[$k]['items'] = [];
            } else {
                MenuTree::fillMenuItems($list[$k]['items']);
            }
        }
    }

    public static function formatMenuItems(&$list, $recursed = false) {
        foreach ($list as $k => $v) {
            if (@$v['icon'] != '') {
                $list[$k]['label'] = '<i class="fa fa-fw ' . $v['icon'] . '"></i> ' . $list[$k]['label'];
            }

            if ($v['label'] == '---') {
                $list[$k]['template'] = '<hr/>';
            }

            if (!isset($v['url'])) {
                $list[$k]['url'] = ['#'];
            } else {
                if (!is_array($v['url'])) {
                    if ($v['url'] == '#') {
                        $v['url'] = ['#'];
                    } else if (substr($v['url'], 0, 4) != 'http') {
                        $list[$k]['url'] = [str_replace(["\n", "\r"], "", str_replace('?', '&', $v['url']))];
                    }
                }
            }

            if (isset($v['items'])) {
                if (!$recursed) {
                    $list[$k]['label'] = ' <span class="caret"></span> ' . $list[$k]['label'];
                }
                MenuTree::formatMenuItems($list[$k]['items'], true);
            } else {
                $list[$k]['itemOptions'] = [
                    'class' => 'no-menu'
                ];
            }
        }
    }

    public static function cleanMenuItems(&$list) {
        foreach ($list as $k => $v) {
            if (isset($v['items'])) {
                MenuTree::cleanMenuItems($list[$k]['items']);
            }
            if (count($list[$k]['items']) == 0) {
                unset($list[$k]['items']);
            }
            if (@$list[$k]['state'] == "") {
                unset($list[$k]['state']);
            }
        }
    }

    public $title = "";
    public $list = "";
    public $class = "";
    public $classpath = "";
    public $options = "";

    public static function load($classpath, $options = null) {
        $mt = new MenuTree;

        $mt->title = @$options['title'];
        $mt->options = @$options['options'];
        $mt->classpath = $classpath;
        $mt->class = Helper::explodeLast(".", $classpath);
        $mt->list = include(Yii::getPathOfAlias($classpath) . ".php");
        MenuTree::fillMenuItems($mt->list);
        return $mt;
    }

    public function renderScript() {
        $script = Yii::app()->controller->renderPartial('//layouts/menu_js', [
            'list' => $this->list,
            'class' => $this->class,
            'options' => $this->options,
                ], true);

        return str_replace(["<script>", "</script>"], "", $script);
    }

    public function render($registerScript = true) {
        $ctrl = Yii::app()->controller;

        if ($registerScript) {
            $id = "NGCTRLMENUTREE_{$this->class}_" . rand(0, 1000);
            Yii::app()->clientScript->registerScript($id, $this->renderScript(), CClientScript::POS_END);
            $script = false;
        } else {
            $script = $this->renderScript();
        }

        return $ctrl->renderPartial("//layouts/menu", [
                    'class' => $this->class,
                    'classpath' => $this->classpath,
                    'title' => $this->title,
                    'options' => $this->options,
                    'script' => $script,
                        ], true);
    }

}
