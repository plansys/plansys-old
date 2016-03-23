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
            case "relform":
            case "chooserelform":
            case "subform":
            case "master":
                if (!file_exists($file) || !!@$post['overwrite']) {
                    $this->generateAR($post, $result);
                }
                break;
            case "js":
                $this->generateJS($post, $result);
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

    public function generateJS($post, &$result) {
        $tpl     = file_get_contents(Yii::getPathOfAlias('application.components.codegen.templates.' . $post['template']) . ".js");
        $content = str_replace(array_keys($post['replace']), array_values($post['replace']), $tpl);
        file_put_contents($result['file'], $content);
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


        $relations = "";
        if (isset($post['relations'])) {
            foreach ($post['relations'] as $rel) {
                if (!isset($rel['name'])) continue;

                switch ($rel['type']) {
                    case "CBelongsToRelation":
                        $relName  = ucfirst($rel['name']);
                        $formName = substr($post['formName'], 0, -4) . $relName . 'Relform';
                        $tprel    = file_get_contents(Yii::getPathOfAlias('application.components.codegen.templates.TplRelBTController') . ".php");
                        $tprel    = Helper::getStringBetween($tprel, '### TEMPLATE-START ###', '### TEMPLATE-END ###');
                        $reprel   = [
                            'RelModel' => $relName,
                            'TemplateForm' => $formName,
                        ];
                        $tprel    = str_replace(array_keys($reprel), array_values($reprel), $tprel);
                        $relations .= "
{$tprel}
                    ";
                        break;

                    case "CHasManyRelation":
                    case "CManyManyRelation":
                        if (@$rel['editable'] == "PopUp" || @$rel['insertable'] == 'PopUp') {
                            $foreignKey = $rel['foreignKey'];
                            if ($rel['type'] == "CManyManyRelation") {
                                $token   = token_get_all("<?php " . str_replace(" ", "", $rel['foreignKey']));
                                $mmTable = $token[1][1];
                                $mmFrom  = $token[3][1];
                                $mmTo    = $token[5][1];

                                $foreignKey = $mmFrom;
                            }

                            $relName  = ucfirst($rel['name']);
                            $formName = substr($post['formName'], 0, -4) . $relName . 'Relform';
                            $tprel    = file_get_contents(Yii::getPathOfAlias('application.components.codegen.templates.TplRelMController') . ".php");
                            $tprel    = Helper::getStringBetween($tprel, '### TEMPLATE-START ###', '### TEMPLATE-END ###');
                            $reprel   = [
                                'RelModel' => $relName,
                                'TemplateForm' => $formName,
                                'foreignKey' => $foreignKey,
                                'insertMethod' => $rel['type'] == 'CHasManyRelation' ? 'validate' : 'save'
                            ];
                            $tprel    = str_replace(array_keys($reprel), array_values($reprel), $tprel);
                            $relations .= "
{$tprel}
                    ";
                        }

                        if ($rel['type'] == 'CManyManyRelation') {
                            $relName        = ucfirst($rel['name']);
                            $chooseFormName = substr($post['formName'], 0, -4) . $relName . 'ChooseRelform';
                            $tprel          = file_get_contents(Yii::getPathOfAlias('application.components.codegen.templates.TplRelMMController') . ".php");
                            $tprel          = Helper::getStringBetween($tprel, '### TEMPLATE-START ###', '### TEMPLATE-END ###');
                            $reprel         = [
                                'RelModel' => $relName,
                                'TemplateChooseForm' => $chooseFormName,
                            ];
                            $tprel          = str_replace(array_keys($reprel), array_values($reprel), $tprel);
                            $relations .= "
{$tprel}
                    ";
                        }
                        break;
                }
            }
        }

        $replace['##RELATION-PLACEHOLDER##'] = $relations;
        $content                             = str_replace(array_keys($replace), array_values($replace), $tpl);
        
        if(!is_dir(dirname($result['file']))) {
            mkdir(dirname($result['file']), 0777, true);
        }
        
        file_put_contents($result['file'], $content);
    }

    public function actionWarning($c) {
        echo @$_SESSION['CrudGenerator'][$c]['msg'];
    }
}