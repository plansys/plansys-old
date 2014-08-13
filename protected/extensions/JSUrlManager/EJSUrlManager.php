<?php
class EJSUrlManager extends CApplicationComponent
{
    public function init()
    {
        parent::init();
        $urlManager = Yii::app()->urlManager;

        $managerVars              = get_object_vars($urlManager);
        $managerVars['urlFormat'] = $urlManager->urlFormat;

        foreach ($managerVars['rules'] as $pattern => $route) {
            //Ignore custom URL classes
            if(is_array($route) && isset($route['class'])) {
                unset($managerVars['rules'][$pattern]);
            }
        }

        $encodedVars = CJSON::encode($managerVars);

        $asset = Yii::app()->assetManager->publish(
            dirname(__FILE__).'/assets/js',
            true,
            -1,
            YII_DEBUG
        );

        $cs = Yii::app()->getClientScript();

        $baseUrl = Yii::app()->getRequest()->getBaseUrl();
        $scriptUrl = Yii::app()->getRequest()->getScriptUrl();
        $hostInfo = Yii::app()->getRequest()->getHostInfo();

        $cs->registerScriptFile($asset . '/Yii.UrlManager.min.js', CClientScript::POS_HEAD);
        $cs->registerScript(
            "yiijs.create.js",
            "var Yii = Yii || {}; Yii.app = {scriptUrl: '{$scriptUrl}',baseUrl: '{$baseUrl}',
            hostInfo: '{$hostInfo}'};
            Yii.app.urlManager = new UrlManager({$encodedVars});
            Yii.app.createUrl = function(route, params, ampersand)  {
            return this.urlManager.createUrl(route, params, ampersand);};"
            ,
            CClientScript::POS_HEAD
        );

    }

}
