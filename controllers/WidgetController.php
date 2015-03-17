<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FormField
 *
 * @author rizky
 */
class WidgetController extends Controller {

    public function createAction($actionID) {
        $controller = explode(".", $actionID);
        if (count($controller) > 1) {
            $actionID = $controller[1];
            $controller = $controller[0];

            if (class_exists($controller)) {
                $widget = new $controller;
                if (method_exists($widget, 'action' . ucfirst($actionID))) {
                    return new CInlineAction($widget, $actionID);
                }
            }
        }
        
        throw new CHttpException('404', 'Action Not Found');
    }

}
