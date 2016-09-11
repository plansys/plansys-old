<?php
/**
 * Class ExpressionField
 * @author rizky
 */
class ExpressionField extends FormField {
    /**
     * @return array me-return array property ExpressionField.
     */
    public function getFieldProperties() {
        return  [
             [
                'label' => 'Field Name:',
                'name' => 'fieldname',
                'options' =>  [
                    'ng-model' => 'active.fieldname',
                    'ng-change' => 'changeActiveName()',
                    'ps-list' => 'modelFieldList',
                ],
                'list' =>  [],
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'type' => 'DropDownList',
            ],
             [
                'value' => '<hr/>',
                'type' => 'Text',
            ],
             [
                'label' => 'Label',
                'name' => 'label',
                'options' =>  [
                    'ng-model' => 'active.label',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'type' => 'TextField',
            ],
             [
                'label' => 'Language',
                'name' => 'language',
                'options' =>  [
                    'ng-model' => 'active.language',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'list' =>  [
                    'html' => 'HTML',
                    'php' => 'PHP',
                    'js' => 'JS',
                    'sql' => 'SQL',
                ],
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ],
             [
                'value' => '<hr/>',
                'type' => 'Text',
            ],
             [
                'label' => 'Options',
                'name' => 'options',
                'type' => 'KeyValueGrid',
            ],
             [
                'label' => 'Info Message (HTML allowed)',
                'name' => 'desc',
                'labelWidth' => '5',
                'fieldWidth' => '12',
                'layout' => 'Vertical',
                'fieldHeight' => '0',
                'options' =>  [
                    'ng-model' => 'active.desc',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ],
                'fieldOptions' =>  [
                    'auto-grow' => '',
                ],
                'type' => 'TextArea',
            ],
        ];
    }

    /** @var string $label */
    public $label = '';
	
    /** @var string $fieldname */
    public $fieldname = '';
	
    /** @var string $language */
    public $language = 'php';
	
    /** @var string $value */
    public $value = '';
	
    /** @var array $options */
    public $options = [];
	
    /** @var string $desc */
    public $desc = '';
    
    /** @var array $labelOptions */
    public $labelOptions = [];
	
    /** @var string $toolbarName */
    public static $toolbarName = "Expression Field";
	
    /** @var string $category */
    public static $category = "Data & Tables";
	
    /** @var string $toolbarIcon */
    public static $toolbarIcon = "fa fa-terminal";
    
    public static $deprecated = true;
	
    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['expression-field.js'];
    }

    /**
     * @return null Fungsi ini akan memvalidasi action dengan menjalankan function evaluate.
    */
    public function actionValidate() {
        $postdata = file_get_contents("php://input");
        $post = json_decode($postdata, true);
        $result = '';

        $this->evaluate(@$post['expr']);
    }
    
    /**
     * @return string me-return PHP type.
    */
    public function getIcon() {
        if ($this->language == "php") return "php";
        if ($this->language == "sql") return "php-database-alt2";
        if ($this->language == "js") return "javascript";
        if ($this->language == "html") return "shell";
    }

    /**
     * render
     * Fungsi ini untuk me-render field dan atributnya
     * @return mixed me-return sebuah field dan atribut ExpressionField dari hasil render
     */
    public function render() {
        $this->addClass('field-box');
        
        $ngModelAvailable = isset($this->options['ng-model']) 
            && is_string($this->options['ng-model']) 
            && trim($this->options['ng-model']) != "";
        
        if ($this->fieldname != '' && !$ngModelAvailable) {
            $this->options['ng-model'] = 'active.' . $this->fieldname;
        } 
        
        return $this->renderInternal('template_render.php');
    }

}