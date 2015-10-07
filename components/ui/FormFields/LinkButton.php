<?php

/**
 * Class LinkButton
 * @author rizky
 */
class LinkButton extends FormField {

    /** @var string $label */
    public $label = '';

    /** @var string $url */
    public $url = '#';

    /** @var array $urlparams */
    public $urlparams = [];

    /** @var string $group */
    public $group = '';

    /** @var string $groupType */
    public $groupType = 'ButtonGroup';

    /** @var string $buttonType */
    public $buttonType = 'default';

    /** @var string $icon */
    public $icon = '';
    public $position = 'left';

    /** @var string $buttonSize */
    public $buttonSize = 'btn-sm';

    /** @var array $options */
    public $options = [];

    /** @var boolean $displayInline */
    public $displayInline = true;

    /** @var string $toolbarName */
    public static $toolbarName = "Link Button";

    /** @var string $category */
    public static $category = "User Interface";

    /** @var string $toolbarIcon */
    public static $toolbarIcon = "glyphicon glyphicon-link";

    /**
     * @return array me-return array javascript yang di-include
     */
    public function includeJS() {
        return ['link-button.js'];
    }

    /**
     * @param string $url sebuah rute url.
     * @return string me-return constructed URL.  
     */
    public function createUrl($url) {
        if ($url == "#") {
            return "#";
        } else {
            return Yii::app()->controller->createUrl($url);
        }
    }

    /**
     * @return array me-return array property LinkButton.
     */
    public function getFieldProperties() {
        return array (
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
                'label' => 'Group',
                'name' => 'group',
                'options' => array (
                    'ng-model' => 'active.group',
                    'ng-change' => 'save();',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Group Type',
                'name' => 'groupType',
                'options' => array (
                    'ng-show' => 'active.group.trim() != \'\'',
                    'ng-model' => 'active.groupType',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'array(\'ButtonGroup\', \'DropDown\')',
                'type' => 'DropDownList',
            ),
            array (
                'type' => 'Text',
                'value' => '<Hr/>',
            ),
            array (
                'label' => 'Icon',
                'name' => 'icon',
                'fieldWidth' => '7',
                'prefix' => 'fa-',
                'options' => array (
                    'ng-model' => 'active.icon',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Button Type',
                'name' => 'buttonType',
                'options' => array (
                    'ng-model' => 'active.buttonType',
                    'ng-change' => 'save()',
                    'ng-delay' => '500',
                ),
                'listExpr' => 'array(
     \'primary\' => \'Primary\',
     \'info\' => \'Info\',
     \'default\' => \'Default\',
     \'success\' => \'Success\',
     \'warning\' => \'Warning\',
     \'danger\' => \'Danger\'
);',
                'fieldWidth' => '4',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Button Size',
                'name' => 'buttonSize',
                'options' => array (
                    'ng-model' => 'active.buttonSize',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'array(
    \'btn-xs\' => \'Very Small\',
    \'btn-sm\' => \'Small\',
    \'\' => \'Default\',
    \'btn-lg\' => \'Large\',
)',
                'fieldWidth' => '6',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Position',
                'name' => 'position',
                'options' => array (
                    'ng-model' => 'active.position',
                    'ng-change' => 'save()',
                ),
                'list' => array (
                    'left' => 'Left',
                    'right' => 'Right',
                ),
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Options',
                'name' => 'options',
                'show' => 'Show',
                'type' => 'KeyValueGrid',
            ),
        );
    }

    public function render() {
        $this->options['link-btn'] = '';
        $this->options['group'] = $this->group;
        $this->options['id'] = $this->renderID;
        $this->addClass('link-btn');
        if ($this->buttonType != 'not-btn') {
            $this->addClass("btn btn-{$this->buttonType} {$this->buttonSize}");
        }
        if ($this->position != 'left') {
            $this->addClass('btn-position-' . $this->position);
        }

        return $this->renderInternal('template_render.php');
    }

}