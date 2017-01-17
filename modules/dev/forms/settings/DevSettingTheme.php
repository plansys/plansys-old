<?php

class DevSettingTheme extends Form {
    public $themes = [];
    public $active = null;
    
    public function init() {
        $this->active = Setting::get('app.theme');
        
        $themes = [];
        if (is_dir(Yii::getPathOfAlias('app.views'))) {
            $themes[] = [
                'name' => 'Custom',
                'shortname' => null,
                'dir' => Yii::getPathOfAlias('app.views'),
                'img' => Yii::app()->controller->staticUrl('/img/theme-placeholder.png')
            ];
        }
        
        $pstheme = glob(Yii::getPathOfAlias('application.themes') . '/*');
        foreach ($pstheme as $k=>$p) {
            $p = str_replace("\\", "/", $p);
            $name = explode("/", $p);
            $name = end($name);
            $img = Yii::app()->controller->staticUrl('/img/theme-placeholder.png');
            if (is_file($p .'/views/preview.png')) {
                $img = Yii::app()->request->baseUrl . '/plansys/themes/' . $name . '/views/preview.png';
            }
            
            $themes[] = [
                'name' => ucfirst($name),
                'shortname' => $name,
                'dir'=> $p . '/views',
                'img' => $img,
            ];
        }
        $this->themes = $themes;
    }
    
    public function save() {
        Setting::set('app.theme', $this->active);
        return true;
    }
    
    public function getFields() {
        return array (
            array (
                'linkBar' => array (),
                'title' => '<i class=\\"fa fa-image\\"></i> {{form.title}}',
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'type' => 'Text',
                'value' => '<style>
    .themes {
        margin:15px 0px;
    }
    .theme {
        cursor:pointer;
        width:140px;
        height:165px;
        float:left;
        padding:10px;
        margin:10px;
        color:#000;
        text-decoration:none;
        border:1px solid #ececeb;
        text-align:center;
    }
    .theme:hover {
        text-decoration:none;
        background:#ececeb;
        color:#000;
    }
    .theme-img {
        width:120px;
        height:120px;
        background:#fff;
        margin-bottom:5px;
        overflow:hidden;
        border:1px solid #ececeb;
    }
    
    .theme.active {
        background:#666;
        color:#fff;
    }
</style>',
            ),
            array (
                'display' => 'all-line',
                'type' => 'Text',
                'value' => '<div class=\"themes\">
    <a ng-url=\"dev/setting/theme&id={{t.shortname}}\" 
       class=\"theme {{ t.shortname ===  model.active ? \'active\' : \'\'}}\" 
        ng-repeat=\"t in model.themes\">
        <div class=\"theme-img\">
            <img ng-if=\"t.img\" 
                 ng-src=\"{{ t.img }}\"
                 alt=\"{{ t.name }}\">
        </div>
        <div class=\"theme-name\">
            {{ t.name }}
        </div>
    </a>
</div>
<div class=\"clearfix\"></div>',
            ),
        );
    }

    public function getForm() {
        return array (
            'title' => 'Theme Setting',
            'layout' => array (
                'name' => '2-cols',
                'data' => array (
                    'col1' => array (
                        'size' => '200',
                        'sizetype' => 'px',
                        'type' => 'menu',
                        'name' => 'col1',
                        'file' => 'application.modules.dev.menus.Setting',
                        'icon' => 'fa-sliders',
                        'title' => 'Main Setting',
                        'menuOptions' => array (),
                    ),
                    'col2' => array (
                        'size' => '',
                        'sizetype' => '',
                        'type' => 'mainform',
                    ),
                ),
            ),
        );
    }

}