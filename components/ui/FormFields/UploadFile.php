<?php

class UploadFile extends FormField {

    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Field Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                ),
                'listExpr' => 'FormsController::$modelFieldList',
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Label',
                'name' => 'label',
                'options' => array (
                    'ng-model' => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'File Type',
                'name' => 'fileType',
                'options' => array (
                    'ng-model' => 'active.fileType',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'fieldOptions' => array (
                    'placeholder' => 'ex: jpg, doc, xls',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Upload Path (PHP)',
                'name' => 'uploadPath',
                'options' => array (
                    'ng-model' => 'active.uploadPath',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'fieldOptions' => array (
                    'placeholder' => 'ex: geo/{$model->id}',
                    'style' => 'min-height:50px;white-space:pre;word-break:break-all;',
                    'auto-grow' => '',
                ),
                'type' => 'TextArea',
            ),
            array (
                'label' => 'File Pattern (PHP)',
                'name' => 'filePattern',
                'fieldHeight' => '0',
                'options' => array (
                    'ng-model' => 'active.filePattern',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'fieldOptions' => array (
                    'placeholder' => 'ex: \'{$model->id}.{$ext}\'',
                    'auto-grow' => '',
                    'style' => 'min-height:50px;white-space:pre;word-break:break-all;',
                ),
                'type' => 'TextArea',
            ),
            array (
                'label' => 'Layout',
                'name' => 'layout',
                'options' => array (
                    'ng-model' => 'active.layout',
                    'ng-change' => 'save();',
                ),
                'listExpr' => 'array(\'Horizontal\',\'Vertical\')',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Mode',
                'name' => 'mode',
                'options' => array (
                    'ng-model' => 'active.mode',
                    'ng-change' => 'save();',
                ),
                'list' => array (
                    'Upload + Browse + Download' => 'Upload + Browse + Download',
                    'Browse + Download' => 'Browse + Download',
                    'Upload + Download' => 'Upload + Download',
                    'Download Only' => 'Download Only',
                ),
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Allow Delete',
                'name' => 'allowDelete',
                'options' => array (
                    'ng-model' => 'active.allowDelete',
                    'ng-change' => 'save()',
                ),
                'listExpr' => '[\'Yes\',\'No\']',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Allow Overwrite',
                'name' => 'allowOverwrite',
                'options' => array (
                    'ng-model' => 'active.allowOverwrite',
                    'ng-change' => 'save()',
                ),
                'listExpr' => '[\'Yes\',\'No\']',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Show File Name',
                'name' => 'showFileName',
                'options' => array (
                    'ng-model' => 'active.showFileName',
                    'ng-change' => 'save()',
                ),
                'listExpr' => '[\'Yes\',\'No\']',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr/>',
            ),
            array (
                'column1' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'label' => 'Label Width',
                        'name' => 'labelWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options' => array (
                            'ng-model' => 'active.labelWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                            'ng-disabled' => 'active.layout == \'Vertical\'',
                        ),
                        'type' => 'TextField',
                    ),
                ),
                'column2' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'label' => 'Field Width',
                        'name' => 'fieldWidth',
                        'layout' => 'Vertical',
                        'labelWidth' => '12',
                        'fieldWidth' => '11',
                        'options' => array (
                            'ng-model' => 'active.fieldWidth',
                            'ng-change' => 'save()',
                            'ng-delay' => '500',
                        ),
                        'type' => 'TextField',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
            array (
                'label' => 'Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Label Options',
                'name' => 'labelOptions',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Field Options',
                'name' => 'fieldOptions',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    public $name;
    public $label = "File Upload";
    public $layout = 'Horizontal';
    public $value;
    public $mode = 'Upload + Download';
    public $filePattern = '';
    public $labelWidth = 4;
    public $fieldWidth = 8;
    public $uploadPath = '';
    public $fileType = '';
    public $options = [];
    public $allowDelete = 'Yes';
    public $allowOverwrite = 'Yes';
    public $showFileName = 'No';
    public $labelOptions = [];
    public $fieldOptions = [];

    /** @var string $toolbarName */
    public static $toolbarName = "Upload File";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-upload";

    public function getUploadPath() {
        $dir = Yii::getPathOfAlias('repo' . '.' . $this->uploadPath);

        if ($dir != "" && !file_exists($dir)) {
            mkdir($dir, 0755, true);
            chmod($dir, 0755);
        }
        return $dir;
    }

    public function getFileType() {
        return $this->fileType;
    }

    public function includeJS() {
        return ['upload-file.v1.js'];
    }

    public function getLayoutClass() {
        return ($this->layout == 'Vertical' ? 'form-vertical' : '');
    }

    public function getErrorClass() {
        return (count($this->errors) > 0 ? 'has-error has-feedback' : '');
    }

    public function getlabelClass() {
        if ($this->layout == 'Vertical') {
            $class = "control-label col-sm-12";
        } else {
            $class = "control-label col-sm-{$this->labelWidth}";
        }

        $class .= @$this->labelOptions['class'];
        return $class;
    }

    public function actionUpload($path = null) {
        if (!isset($_FILES['file'])) {
            echo json_encode(["success" => "No", "files" => json_encode($_FILES)]);
            die();
        }
        $file = $_FILES["file"];
        $name = $file['name'];
        
        $fb = FormBuilder::load($_GET['class']);
        $ff = $fb->findField(['name' => $_GET['name']]);
        
        ## create temporary directory
        $tmpdir = Yii::getPathOfAlias('webroot.assets.tmp');
        if (!is_dir($tmpdir)) {
            mkdir($tmpdir, 0755, true);
            chmod($tmpdir, 0755);
        }

        ## make sure there is no duplicate file name
        $i = 1;
        $actualName = pathinfo($name, PATHINFO_FILENAME);
        $originName = $actualName;
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        while (file_exists($tmpdir . DIRECTORY_SEPARATOR . $actualName . '.' . $extension)) {
            $actualName = (string) $originName . '_' . $i;
            $name = $actualName . '.' . $extension;
            $i++;
        }

        $tmppath = $tmpdir . DIRECTORY_SEPARATOR . $name;
        $tmppath = str_replace(['/',''],'/',$tmppath);
        
        move_uploaded_file($file["tmp_name"], $tmppath);
        
        echo json_encode([
            'success' => 'Yes',
            'path' => $tmppath,
            'downloadPath' => base64_encode($tmppath),
            'name' => $name
        ]);
    }

//    public function actionDescription() {
//        $postdata = file_get_contents("php://input");
//        $post = CJSON::decode($postdata);
//        $name = base64_decode($post['name']);
//        $path = base64_decode($post['path']);
//        $content = base64_decode($post['desc']);
//        $desc = JsonModel::load($path . DIRECTORY_SEPARATOR . $name . '.json');
//        $desc->set('desc', $content);
//    }

    public function actionThumb($t) {
        $file = base64_decode($t);
        if (!is_file($file)) {
            $file = RepoManager::resolve($file);
        }

        $supported_image = array(
            'gif',
            'jpg',
            'jpeg',
            'png',
            'bmp',
            'tga'
        );
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($ext, $supported_image)) {
            return;
        }

        $img = Yii::app()->img->load($file);
        $img->resizeToWidth(250);

        $dir = Yii::getPathOfAlias('webroot.assets.thumb.' . date('Y-m-d'));
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            chmod($dir, 0755);
        }
        $thumb = $dir . DIRECTORY_SEPARATOR . basename(time() . '_' . rand(1, 10000) . "." . pathinfo($file, PATHINFO_EXTENSION));
        $img->save($thumb);
        $url = str_replace(Yii::getPathOfAlias('webroot'), '', $thumb);
        $url = str_replace('', '/', $url);

        echo Yii::app()->baseUrl . $url;
    }

    public function actionCheckFile() {
        $postdata = file_get_contents("php://input");
        $post = json_decode($postdata, true);
        $file = RepoManager::resolve($post['file']);

        if (file_exists($file)) {
            $downloadPath = base64_encode($file);
            echo json_encode([
                'status' => 'exist',
                'desc' => '',
                'downloadPath' => $downloadPath
            ]);
        } else {
            echo json_encode([
                'status' => 'not exist',
            ]);
        }
    }

    public function actionDownload($f, $n) {
        $file = base64_decode($f);
        if (!is_file($file)) {
            $file = RepoManager::resolve($file);
            if (!is_file($file)) {
                throw new CHttpException(404);
                return false;
            }
        }

        $mem_limit = ini_get('memory_limit');
        ini_set('memory_limit', -1);
        if (isset($_GET['d'])) {
            echo file_get_contents($file);
        } else {
            Yii::app()->request->sendFile($n, file_get_contents($file));
        }
        ini_set('memory_limit', $mem_limit);
    }

    public function actionRemove() {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        $file = base64_decode($post['file']);
        $file = RepoManager::resolve($file);
        @unlink($file);
//        unlink($file . '.json');
    }

    public function getFieldColClass() {
        return "col-sm-" . $this->fieldWidth;
    }

    public function render() {
        $this->addClass('form-group form-group-sm', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->addClass('form-control', 'fieldOptions');

        $this->setDefaultOption('ng-model', "model['{$this->originalName}']", $this->options);
        $this->setDefaultOption('style', "min-width:275px;", $this->options);
        return $this->renderInternal('template_render.php');
    }

}

?>