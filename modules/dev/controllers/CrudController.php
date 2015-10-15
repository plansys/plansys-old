<?php

class CrudController extends Controller {
    public $layout = '//layouts/blank';

    public function actionNew() {
        $model = new DevCrudMainForm();
        $this->renderForm('DevCrudMainForm', $model);
    }

    public function actionlistRelation($m) {
        if (class_exists($m)) {
            $result = [];
            $rel    = $m::model()->metaData->relations;
            foreach ($rel as $k => $r) {
                $relClass = $r->className;
                if (class_exists($relClass)) {
                    $tableName = $relClass::model()->tableName();
                } else {
                    $tableName = false;
                }
                $result[$k] = [
                    'type' => get_class($r),
                    'tableName' => $tableName
                ];

                $result[$k] += json_decode(json_encode($r), true);
            }
            echo json_encode($result);
        }
    }

    public function actionCheckFile() {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);

        if ($post['file']['type'] == 'folder') {
            echo "ready";
            die();
        } else {
            if (isset($post['file']['path'])) {
                $check = Yii::getPathOfAlias($post['file']['path']) . DIRECTORY_SEPARATOR;
            } else {
                $check = Yii::getPathOfAlias($post['path']) . DIRECTORY_SEPARATOR;
            }
            $check .= $post['file']['name'];
        }

        echo file_exists($check) ? "exist" : "ready";
    }

    public function actionGenerate() {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);

        $path   = Yii::getPathOfAlias($post['path']);
        $file   = $path . DIRECTORY_SEPARATOR . $post['name'];
        $result = [
            'status' => 'ok',
            'path' => $path,
            'file' => $file
        ];

        switch ($post['type']) {
            case "folder":
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                break;
            case "index":
            case "form":
            case "master":
                if (!file_exists($file) || !!@$post['overwrite']) {
                    $this->generateAR($post, $result);
                }
                break;
            case "controller":
                if (!file_exists($file) || !!@$post['overwrite']) {
                    $this->generateController($post, $result);
                }
                break;
            default:
                break;
        }
        echo json_encode($result);
    }

    public function generateAR($post, &$result) {
        $result['touch'] = $this->createUrl('/dev/forms/update&class=' . $post['path'] . "." . $post['className']);

        if (!isset($_SESSION['CrudGenerator'])) {
            $_SESSION['CrudGenerator'] = [];
        }
        $_SESSION['CrudGenerator'][$post['className']] = $post;

        $content = <<<EOF
<?php

class {$post['className']} extends {$post['extendsName']} {
}
EOF;
        file_put_contents($result['file'], $content);
    }

    public function generateController($post, &$result) {
        $tplName = $post['mode'] == 'crud' ? 'TplCrudController' : 'TplMasterController';
        $tpl     = file_get_contents(Yii::getPathOfAlias('application.components.codegen.templates.' . $tplName) . ".php");
        $replace = [
            $tplName => $post['className'],
            '##IMPORT-PLACEHOLDER##' => 'Yii::import("' . $post['alias'] . '.*");',
        ];

        if (isset($post['formName'])) {
            $replace['TemplateForm'] = $post['formName'];
        }
        if (isset($post['indexName'])) {
            $replace['TemplateIndex'] = $post['indexName'];
        }

        $content = str_replace(array_keys($replace), array_values($replace), $tpl);
        file_put_contents($result['file'], $content);
    }
}