<?php

class AuditTrail extends ActiveRecord {

    public static function indexQuery($params) {
        $model = AuditTrail::model()->findByAttributes(['key' => @$_GET['key']]);
        $where = AuditTrail::queryWhere($params, $model);

        $sql = "SELECT id, stamp, type, user_id, description, url
            FROM p_audit_trail {$where}  {id desc, [order]} {[paging]}";

        return $sql;
    }

    public static function queryWhere($params, $model) {
        $sql = "WHERE {[where] AND}  (`key` = '{$model->key}'";

        if (Helper::endsWith($model->form_class, "Index")) {
            $sql .= "{OR} (model_class = '{$model->model_class}' AND ctrl = '{$model->ctrl}' AND module = '{$model->module}'))";
        } else {
            $sql .= ')';
        }

        return $sql;
    }

    public static function countQuery($params) {
        $model = AuditTrail::model()->findByAttributes(['key' => @$_GET['key']]);
        $where = AuditTrail::queryWhere($params, $model);
        $sql   = "SELECT count(1) FROM p_audit_trail {$where}";
        return $sql;
    }

    public static function typeDropdown($all = true) {
        if ($all) {
            return [
                'general' => [
                    'view' => 'View',
                    'create' => 'Create',
                    'update' => 'Update',
                    'delete' => 'Delete',
                ],
                'other' => [
                    'login' => 'Login',
                    'logout' => 'Logout',
                    'other' => 'Other'
                ]
            ];
        } else {
            return [
                'view' => 'View',
                'create' => 'Create',
                'update' => 'Update',
                'delete' => 'Delete',
            ];
        }
    }

    public static function savePageInfo($info) {
        if (!isset(Yii::app()->session['PageTitle'])) {
            Yii::app()->session['PageTitle'] = [];
        }
        $session                         = Yii::app()->session['PageTitle'];
        $urlID                           = AuditTrail::urlID($info);
        $session[$urlID]                 = $info;
        Yii::app()->session['PageTitle'] = $session;
    }

    public static function urlID($info) {
        return md5($info['pathinfo'] . "##" . $info['params']);
    }

    public static function logout() {
        $ip = Yii::app()->request->getUserHostAddress();
        AuditTrail::track("Logged out from {$ip}", "logout");
    }

    public static function track($msg, $type = "other", $info = "") {
        if (!Yii::app()->user->isGuest) {
            ## load info
            $pathInfo = AuditTrail::loadPageInfo($info);
            $uid      = Yii::app()->user->id;

            if ($pathInfo['module'] == "sys")
                return;

            ## get last audit trail
            $sql       = "select * from p_audit_trail where user_id = {$uid} order by id desc";
            $lastTrail = Yii::app()->db->createCommand($sql)->queryRow();

            ## detect duplicate
            $isDuplicate     = $lastTrail['pathinfo'] == $pathInfo['pathinfo'] && $lastTrail['params'] == $pathInfo['params'];
            $lastInsertHour  = round(abs(strtotime($lastTrail['stamp']) - time()) / 3600);
            $isDifferentType = $lastTrail['type'] != $type;

            if ($isDuplicate) {
                if (isset($pathInfo['data']) && $lastTrail['data'] != $pathInfo['data']) {
                    $isDuplicate = false;
                }
            }

            ## if not duplicate OR is different type OR last tracked time is more than 1 hour ago
            if (!$isDuplicate || ($isDuplicate && $isDifferentType) || ($isDuplicate && $lastInsertHour > 1)) {
                ## create new track
                if ($isDuplicate) {
                    ## skip tracking view for same page after CRUD
                    $isCrud = in_array($lastTrail['type'], ['create', 'update', 'delete']);
                    if ($isCrud && $type == "view") {
                        return;
                    }
                }

                $at = $pathInfo;

                ## remove data from view tracker...
                if ($type == "view") {
                    $at['data'] = "{}";
                }


                if (is_string($msg) && $msg != "") {
                    $at['description'] = $msg;
                }
                $at['type']    = $type;
                $at['stamp']   = date("Y-m-d H:i:s");
                $at['user_id'] = Yii::app()->user->id;
                
                if (@$at['model_id'] != '') {
                    ActiveRecord::batch('AuditTrail', [$at]);
                }
            }
        }
    }

    public static function loadPageInfo($info = "") {
        if ($info == '') {
            $info = AuditTrail::getPathInfo();
        }
        $urlID     = AuditTrail::urlID($info);
        $session   = Yii::app()->session['PageTitle'];
        $savedInfo = @$session[$urlID];

        return (!!$savedInfo ? $savedInfo : $info);
    }

    public static function getPathInfo() {
        $ctrl     = lcfirst(Yii::app()->controller->id);
        $module   = lcfirst(!Yii::app()->controller->module ? '' : Yii::app()->controller->module->id);
        $action   = lcfirst(Yii::app()->controller->action->id);
        $urlParts = AuditTrail::parseUrl(Yii::app()->request->requestUri);
        $url      = Helper::buildUrl('', $urlParts);
        $params   = @$urlParts['queryParams'];
        if (isset($params['r'])) {
            unset($params['r']);
        }
        $params = http_build_query($params);

        $return = [
            'url' => $url,
            'module' => $module,
            'ctrl' => $ctrl,
            'action' => $action,
            'params' => $params,
            'pathinfo' => "/" . ($module != '' ? $module . "/" : "") . $ctrl . "/" . $action
        ];

        $return['key'] = AuditTrail::generateKey($return);
        return $return;
    }

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
                $parts['query']       = http_build_query($query);
            }
            return $parts;
        }
    }

    public static function generateKey($info) {
        return md5($info['pathinfo'] . $info['params']);
    }

    public static function login() {
        $ip = Yii::app()->request->getUserHostAddress();
        AuditTrail::track("Logged in from {$ip}", "login");
    }

    public function tableName() {
        return "p_audit_trail";
    }

}