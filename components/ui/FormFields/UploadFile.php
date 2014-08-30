<?php
class UploadFile extends FormField{
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Field Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                    'searchable' => 'size(modelFieldList) > 5',
                ),
                'listExpr' => 'FormsController::$modelFieldList',
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
                'column1' => array (
                    '<column-placeholder></column-placeholder>',
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
                    '<column-placeholder></column-placeholder>',
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
    
    public function getUploadPath(){
        $dir = Yii::getPathOfAlias('repo'.'.'.$this->uploadPath);
        if(!file_exists($dir)){
            mkdir($dir, '0777', true);
        }
        return $dir;
    }
    
    public function includeJS()
    {
        return array('upload-file.js');
    }
    
    public function getLayoutClass()
    {
        return ($this->layout == 'Vertical' ? 'form-vertical' : '');
    }
    
    public function getErrorClass()
    {
        return (count($this->errors) > 0 ? 'has-error has-feedback' : '');
    }
    
    public function getlabelClass()
    {
        if ($this->layout == 'Vertical') {
            $class = "control-label col-sm-12";
        } else {
            $class = "control-label col-sm-{$this->labelWidth}";
        }

        $class .= @$this->labelOptions['class'];
        return $class;
    }
    
    public function actionUpload($path = null){
        $file = $_FILES["file"];
        //var_dump($this->getUploadPath());
        $repo = new RepoManager;
        $repo->upload($file["tmp_name"] ,$file["name"], $path);
    }
    
    public function getFieldColClass()
    {
        return "col-sm-" . $this->fieldWidth;
    }
    
    public function render()
    {
        $this->addClass('form-group form-group-sm', 'options');
        $this->addClass($this->layoutClass, 'options');
        $this->addClass($this->errorClass, 'options');

        $this->addClass('form-control', 'fieldOptions');
        
        $this->setDefaultOption('ng-model', "model.{$this->originalName}", $this->options);
        return $this->renderInternal('template_render.php');
    }
}
?>