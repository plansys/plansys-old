<?php

class ProfileController extends Controller {

    public function actionIndex() {
        Yii::import('application.modules.dev.forms.users.user.DevUserForm');
        $model = $this->loadModel(Yii::app()->user->id, 'DevUserForm');

        if (isset($_POST['DevUserForm'])) {
            $model->attributes = $_POST['DevUserForm'];

            if ($model->save()) {
                Yii::app()->user->setFlash('info', 'Profil Anda Tersimpan.');
                $model = $this->loadModel(Yii::app()->user->id, 'DevUserForm');
            }
        }
        
        $this->renderForm('DevUserForm', $model, [
            'auditTrailEnabled' => Setting::get('app.auditTrail') == 'Enabled'
        ]);
    }

    public function actionChangeRole($id) {
        $roles = Yii::app()->user->model->roles;
        foreach ($roles as $r) {
            if ($r['id'] == $id) {
                $rootRole = Helper::explodeFirst('.', $r['role_name']);
                Yii::app()->user->setState('fullRole', $r['role_name']);
                Yii::app()->user->setState('role', $rootRole);
                Yii::app()->user->setState('roleId', $id);
            }
        }

        if (@Yii::app()->user->roleInfo['home_url'] == '') {
            
            $this->redirect(Yii::app()->user->returnUrl);    
        }
        $this->redirect([Yii::app()->user->roleInfo['home_url']]);
    }
    
    public function actionAppSetting() {
        echo json_encode(Setting::get('app'));
    }
    
    public function actionGetSystemLoad() {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $res['os'] = 'Windows';
            $res['cpu'] = '??';
            $res['mem'] = '??';
            $res['php'] = explode('-', phpversion())[0];
            $res['hdd'] = '??';
        } else {
            $res['os'] = 'Linux';
            
            $load = sys_getloadavg();
            $res['cpu'] = $load[0];
        
            $free = shell_exec('free');
            $free = (string) trim($free);
            $free_arr = explode("\n", $free);
            $mem = explode(' ', $free_arr[1]);
            $mem = array_filter($mem);
            $mem = array_merge($mem);
            $memory_usage = $mem[2]/$mem[1]*100;
            $res['mem'] = round($memory_usage);
            
            $res['hdd'] = ProfileController::dataSize(disk_free_space('/')) . ' Free';
            $res['php'] = explode('-', phpversion())[0];
            
            
            echo json_encode($res);
        }

        
    }
    
    function dataSize($Bytes) {
        $Type=['', 'K', 'M', 'G', 'T'];
        $counter=0;
        while ($Bytes>=1024) {
            $Bytes/=1024;
            $counter++;
        }
        return('' . floor($Bytes) . ' ' . $Type[$counter] . 'B');
    }


}
