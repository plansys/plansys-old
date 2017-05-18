<?php

if (!class_exists('ContentMode', false)) {
    Yii::import('application.modules.builder.components.ContentMode');
}

class BuilderController extends Controller {
    public $bt;
    public $vpath = 'application.modules.builder.views.builder';
    public $enableCsrf = false;
    public $mode;
    public $treebar = ['form', 'model', 'controller', 'module', 'file', 'service', 'template'];

    public function getTabsUri() {
        return Yii::app()->request->getBaseUrl(true) . '/plansys/modules/builder/views/builder/tabs';
    }
                
    public function getTreeUri() {
        return Yii::app()->request->getBaseUrl(true) . '/plansys/modules/builder/views/builder/tree';
    }
                
    public function getViewPath() {
        parent::getViewPath();
        return Yii::getPathOfAlias($this->vpath);
    }
                        
    public function beforeAction($action) {
        parent::beforeAction($action);
        $this->bt = new State('builder-tabs:memory');
        return true;
    }
                
    public function actionIndex() {
        $this->mode = new ContentMode();

        $bc = new State('builder-chat');
        $all = $bc->getByKey('*');
        $chat = [];
        foreach ($all as $msg) {
            $chat[] = $msg['val'];
        }
        
        //# register main builder js
        $this->render('index', [
            'width' => $this->bt->get(Yii::app()->user->id . '!layout.width'),
            'tree' => [
                'expand' => $this->bt->get(Yii::app()->user->id . '!tree.expand'),
                'treebar' => [
                    'active' => $this->bt->get(Yii::app()->user->id . '!tree.treebar.active'),
                ],
            ],
            'chat' => $chat
        ]);
    }
    
    public function actionGetUserName($id) {
        $user = User::model()->findByPk($id);
        if (!is_null($user)) {
            echo  $user->username;
        }
    }

    public function actionGetstate($key) {
        echo $this->bt->get(Yii::app()->user->id . '!' . $key);
    }
    
    public function actionGetallstate($key) {
        $result = [];
        $array = $this->bt->getByKey(Yii::app()->user->id . '!' . $key);
        if (!is_null($array)) {
            foreach ($array as $item) {
                $result[] = $item['val'];
            }
        }
        
        if (@$_GET['mode'] == 'tabs') {
            usort($result, function($x, $y) {
                return  $x['idx'] - $y['idx'];
            });
        }
        
        echo json_encode($result);
    }

    public function actionDelState($key) {
        $this->bt->del(Yii::app()->user->id . '!' . $key);
    }
    
    public function actionRemoveClosedTab() {
        $post = file_get_contents('php://input');
        $list = json_decode($post);
        $active = json_decode($this->bt->get(Yii::app()->user->id . '!tabs.active'), true);
        $tabs = $this->bt->getByKey(Yii::app()->user->id . '!tabs.list.*');
        if (is_array($tabs)) {
            foreach ($tabs as $tab) {
                if (!in_array($tab['val']['id'], $list)) {
                    $this->bt->del($tab['key']);
                    FileManager::close($tab['val']['p']);
                    if ($active['id'] == $tab['val']['id']) {
                        $this->bt->del(Yii::app()->user->id . '!tabs.active');
                    }
                }
            }
        }
    }
    
    public function actionUpdateTabIndex() {
        $post = file_get_contents('php://input');
        $list = json_decode($post, true);
        
        $tabs = $this->bt->getByKey(Yii::app()->user->id . '!tabs.list.*');
        if (is_array($tabs)) {
            foreach ($tabs as $tab) {
                if (isset($list[$tab['val']['id']])) {
                    $tab['val']['idx'] = $list[$tab['val']['id']];
                    $this->bt->set($tab['key'], $tab['val']);
                }
            }
        }
    }
}
