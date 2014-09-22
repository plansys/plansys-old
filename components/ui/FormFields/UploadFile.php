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
                'type' => 'TextField',
            ),
            array (
                'label' => 'Upload Path',
                'name' => 'uploadPath',
                'prefix' => 'repo.',
                'options' => array (
                    'ng-model' => 'active.uploadPath',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Layout',
                'name' => 'layout',
                'options' => array (
                    'ng-model' => 'active.layout',
                    'ng-change' => 'save();',
                ),
                'listExpr' => 'array(\\\'Horizontal\\\',\\\'Vertical\\\')',
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
                    'Upload + Download' => 'Upload + Download',
                    'Download Only' => 'Download Only',
                ),
                'type' => 'DropDownList',
            ),
            array (
                'column1' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
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
                            'ng-disabled' => 'active.layout == \\\'Vertical\\\'',
                        ),
                        'type' => 'TextField',
                    ),
                ),
                'column2' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
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
                'type' => 'ColumnField',
            ),
            array (
                'label' => 'Options',
                'fieldname' => 'options',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Label Options',
                'fieldname' => 'labelOptions',
                'type' => 'KeyValueGrid',
            ),
            array (
                'label' => 'Field Options',
                'fieldname' => 'fieldOptions',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    public $name;
    public $label = "File Upload";
    public $layout = 'Horizontal';
    public $value;
    public $mode = 'Upload + Download';
    public $labelWidth = 4;
    public $fieldWidth = 4;
    public $uploadPath = '';
    public $fileType = '';
    public $options = array();
    public $labelOptions = array();
    public $fieldOptions = array();

    /** @var string $toolbarName */
    public static $toolbarName = "Upload File";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-upload";

    public function getUploadPath() {
        $dir = Yii::getPathOfAlias('repo' . '.' . $this->uploadPath);

        if ($dir != "" && !file_exists($dir)) {
            mkdir($dir, '0777', true);
        }
        return $dir;
    }

    public function getFileType() {
        return $this->fileType;
    }

    public function includeJS() {
        return array('upload-file.js');
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
        $file = $_FILES["file"];
        $repo = new RepoManager;
        $name = $file['name'];
        $filePath = base64_decode($path);

        $i = 1;
        $actualName = pathinfo($name, PATHINFO_FILENAME);
        $originName = $actualName;
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        while (file_exists($filePath . DIRECTORY_SEPARATOR . $actualName . '.' . $extension)) {
            $actualName = (string) $originName . '_' . $i;
            $name = $actualName . '.' . $extension;
            $i++;
        }
        echo $name;
        $repo->upload($file["tmp_name"], $name, $filePath);
    }

    public function actionDescription() {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        $name = base64_decode($post['name']);
        $path = base64_decode($post['path']);
        $content = base64_decode($post['desc']);
        $desc = JsonModel::load($path . DIRECTORY_SEPARATOR . $name . '.json');
        $desc->set('desc', $content);
    }

    public function actionCheckFile() {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        $file = str_replace("/", DIRECTORY_SEPARATOR, Setting::get('repo.path') . DIRECTORY_SEPARATOR . base64_decode($post['file']));
        $file = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $file);

        if (file_exists($file)) {

            $json = JsonModel::load($file . '.json');
            $desc = $json->get('desc');

            echo json_encode(array(
                'status' => 'exist',
                'desc' => $desc
            ));
        } else {
            echo json_encode(array(
                'status' => 'not exist',
            ));
        }
    }

    public function actionDownload($f, $n) {
        Yii::app()->request->sendFile($n, file_get_contents(base64_decode($f)));
    }

    public function actionRemove() {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        $file = base64_decode($post['file']);
        unlink($file);
        unlink($file . '.json');
    }

    public function getFieldColClass() {
        return "col-sm-" . $this->fieldWidth;
    }

    public function render() {
        $this->addClass('form-group form-group-sm', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->addClass('form-control', 'fieldOptions');
        $this->setDefaultOption('style', 'height:auto', $this->fieldOptions);

        $this->setDefaultOption('ng-model', "model.{$this->originalName}", $this->options);
        return $this->renderInternal('template_render.php');
    }

}

?>