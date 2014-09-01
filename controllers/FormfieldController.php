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
class FormfieldController extends Controller {

    public function createAction($actionID) {

        $controller = explode(".", $actionID);
        if (count($controller) > 1) {
            $actionID = $controller[1];
            $controller = $controller[0];

            if (class_exists($controller)) {
                $formfield = new $controller;
                if (method_exists($formfield, 'action' . ucfirst($actionID))) {
                    return new CInlineAction($formfield, $actionID);
                }
            }
        }
        
        throw new CHttpException('404', 'Action Not Found');
    }

}
