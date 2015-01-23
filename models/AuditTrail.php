<?php

class AuditTrail extends ActiveRecord {

    public static function parseUrl($url) {
        if (is_string($url)) {
            $parts = parse_url($url);

            if (isset($parts['query'])) {
                $parts['query'] = parse_str($parts['query'], $query);
                if (isset($query['r'])) {
                    $parts['pathinfo'] = $query['r'];
                }
                ksort($query);
                $parts['queryParams'] = $query;
                $parts['query'] = http_build_query($query);
            }
            return $parts;
        }
    }

    public static function urlID($info) {
        return md5($info['pathinfo'] . "##" . $info['params']);
    }

    public static function getPathInfo() {
        $ctrl = lcfirst(Yii::app()->controller->id);
        $module = lcfirst(!Yii::app()->controller->module ? '' : Yii::app()->controller->module->id);
        $action = lcfirst(Yii::app()->controller->action->id);
        $urlParts = AuditTrail::parseUrl(Yii::app()->request->requestUri);
        $url = Helper::buildUrl('', $urlParts);
        $params = @$urlParts['queryParams'];
        if (isset($params['r'])) {
            unset($params['r']);
        }
        $params = http_build_query($params);

        return [
            'url' => $url,
            'module' => $module,
            'ctrl' => $ctrl,
            'action' => $action,
            'params' => $params,
            'pathinfo' => "/" . ($module != '' ? $module . "/" : "") . $ctrl . "/" . $action
        ];
    }

    public static function savePageInfo($info) {
        if (!isset(Yii::app()->session['PageTitle'])) {
            Yii::app()->session['PageTitle'] = [];
        }
        $session = Yii::app()->session['PageTitle'];
        $urlID = AuditTrail::urlID($info);
        $session[$urlID] = $info;
        Yii::app()->session['PageTitle'] = $session;
    }

    public static function loadPageInfo($info = "") {
        if ($info == '') {
            $info = AuditTrail::getPathInfo();
        }
        $urlID = AuditTrail::urlID($info);
        $session = Yii::app()->session['PageTitle'];
        $savedInfo = @$session[$urlID];

        return (!!$savedInfo ? $savedInfo : $info);
    }

    public static function login() {
        $ip = Yii::app()->request->getUserHostAddress();  
        AuditTrail::track("Logged in from {$ip}", "login");
    }

    public static function view($info) {
        AuditTrail::track("", "view", $info);
    }

    public static function track($msg, $type = "other", $info = "") {
        if (!Yii::app()->user->isGuest) {
            ## load info
            $pathInfo = AuditTrail::loadPageInfo($info);
            $uid = Yii::app()->user->id;

            ## get last audit trail
            $sql = "select * from p_audit_trail where user_id = {$uid} order by id desc";
            $lastTrail = Yii::app()->db->createCommand($sql)->queryRow();

            ## detect duplicate
            $isDuplicate = $lastTrail['type'] == $type &&
                    $lastTrail['pathinfo'] == $pathInfo['pathinfo'] &&
                    $lastTrail['params'] == $pathInfo['params'];
            $lastInsertHour = round(abs(strtotime($lastTrail['stamp']) - time()) / 3600);

            ## if not duplicate OR last tracked time is more than 1 hour ago
            if (!$isDuplicate || ($isDuplicate && $lastInsertHour > 1)) {
                ## create new track
                $at = new AuditTrail;
                $at->attributes = $pathInfo;
                if (is_string($msg) && $msg != "") {
                    $at->description = $msg;
                }
                $at->type = $type;
                $at->stamp = date("Y-m-d H:i:s");
                $at->user_id = Yii::app()->user->id;
                $at->save();
            }
        }
    }

    public function tableName() {
        return "p_audit_trail";
    }

}
