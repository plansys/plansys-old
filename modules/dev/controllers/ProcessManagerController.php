<?php

class ProcessManagerController extends Controller {

    public function actionIndex() {
        $pid = ProcessHelper::run('php yiic.php pm');
        $this->actionList();
    }

    public function actionList() {
        $pmIsRunning = Setting::get('processManager.isRunning', false);
        $this->renderForm('settings.DevSettingsProcessManager', null, [
            'pmIsRunning' => $pmIsRunning
        ]);
    }


    public function actionStart() {
        $isRunning = Setting::get('processManager.isRunning', false);
        if (!$isRunning) {
            //Starting processManager
            Setting::set('processManager.isRunning', true);
            $pid = ProcessHelper::run('php yiic.php pm');

            if (!!$pid) {
                Setting::set('processManager.pid', $pid);
            }
        }
        $this->redirect(['/dev/processManager/']);
    }

    public
    function actionStop() {
        $isRunning = Setting::get('processManager.isRunning', false);
        if ($isRunning) {
            $pid = Setting::get('processManager.pid');

            //Stopping processManager
            Setting::set('processManager.isRunning', false);
            ProcessHelper::kill($pid);
            Setting::set('processManager.pid', null);

            //Stopping running child process
            $prcs = Setting::get('process');
            if (!empty($prcs)) {
                foreach ($prcs as $id => $prc) {
                    $this->actionStopProcess($id);
                }
            }
        }
        $this->redirect(['/dev/processManager/']);
    }

    public
    function actionStartProcess($id) {
        $prc = Setting::get('process.' . $id);
        if (!$prc['isStarted']) {
            Setting::set('process.' . $id . '.isStarted', true);
        }
        $this->redirect(['/dev/processManager/']);
    }

    public
    function actionStopProcess($id) {
        $prc = Setting::get('process.' . $id);
        if ($prc['isStarted']) {
            Setting::set('process.' . $id . '.isStarted', false);
            if (isset($prc['pid'])) {
                ProcessHelper::kill($prc['pid']);
                Setting::set('process.' . $id . '.pid', null);
            }
        }
        $this->redirect(['/dev/processManager/']);
    }

    public function actionCreateCommand() {
        $href    = '';
        $name    = '';
        $command = '';

        if (isset($_POST['DevSettingsProcessManagerPopUp'])) {
            $cmd  = $_POST['DevSettingsProcessManagerPopUp'];
            $href = Yii::app()->createUrl("dev/processManager/create", ["active" => $cmd['processUrl']]);
            $file = end(explode("=", $_POST['processFile']));

            //Creating unique id for setting
            $id = ProcessHelper::createSettingsId($cmd['processName']);

            Setting::set('tmp.process.id', $id);
            Setting::set('tmp.process.name', $cmd['processName']);
            Setting::set('tmp.process.command', $cmd['processCommand']);
            Setting::set('tmp.process.file', $file);
        }

        $this->renderForm('settings.DevSettingsProcessManagerPopUp', null, ['href' => $href], [
            'layout' => '//layouts/blank'
        ]);
    }

    public function actionCreate() {
        $content = '';
        $path    = [];
        $name    = '';
        $command = '';
        $id      = '';
        $file    = '';

        if (isset($_GET['active'])) {
            $path = explode(".", $_GET['active']);

            $id       = Setting::get('tmp.process.id');
            $name     = Setting::get('tmp.process.name');
            $command  = Setting::get('tmp.process.command');
            $file     = Setting::get('tmp.process.file');
            $filePath = Yii::getPathOfAlias($_GET['active']) . ".php";
            $content  = file_get_contents($filePath);
        } else {
            $this->redirect(['/dev/processManager/']);
        }

        $prefix = Helper::explodeFirst("-", Helper::camelToSnake(Helper::explodeLast(".", $file)));
        if (isset($_POST['DevSettingsProcessManagerForm'])) {

            $cmd = $_POST['DevSettingsProcessManagerForm'];
            $id  = $_POST['processSettingsId'];

            Setting::set("process." . $id . ".name", $cmd['processNameDisp']);
            Setting::set("process." . $id . ".command", $prefix . " " . $cmd['processCommand']);
            Setting::set("process." . $id . ".period", $cmd['processPeriod']);
            Setting::set("process." . $id . ".periodType", $cmd['processPeriodType']);
            Setting::set("process." . $id . ".periodCount", ProcessHelper::periodConverter($cmd['processPeriod'], $cmd['processPeriodType']));
            Setting::set("process." . $id . ".lastRun", null);
            Setting::set("process." . $id . ".isStarted", false);
            Setting::set("process." . $id . ".pid", null);
            Setting::set("process." . $id . ".file", $file);
            Setting::remove('tmp.process');

            $this->redirect(['/dev/processManager/']);
        }

        Asset::registerJS('application.static.js.lib.ace');
        $this->renderForm('settings.DevSettingsProcessManagerForm', [
            'content' => $content,
            'name' => count($path) > 1 ? $path : '',
            'processName' => $name,
            'processNameDisp' => $name,
            'processCommand' => $command,
            'prefix' => $prefix,
            'processSettingsId' => $id
        ]);

    }

    public function actionUpdate($id = null) {
        $content    = '';
        $path       = [];
        $name       = '';
        $command    = '';
        $file       = '';
        $period     = '';
        $periodType = '';
        $cmd        = [];

        if (isset($id)) {
            $cmd = Setting::get('process.' . $id);

            if (count($cmd) > 0) {
                $path       = explode(".", $cmd['file']);
                $id         = $id;
                $name       = $cmd['name'];
                $command    = $cmd['command'];
                $period     = $cmd['period'];
                $periodType = $cmd['periodType'];

                $filePath = Yii::getPathOfAlias((count($path) > 2 ? "application.modules." . $path[1] . ".commands." . $path[2] : "application.commands." . $path[1])) . ".php";
                $content  = file_get_contents($filePath);
            } else {
                $this->redirect(['/dev/processManager/']);
            }
        } else {
            $this->redirect(['/dev/processManager/']);
        }

        if (isset($_POST['DevSettingsProcessManagerForm'])) {

            $cmd = $_POST['DevSettingsProcessManagerForm'];
            $id  = $_POST['processSettingsId'];

            Setting::set("process." . $id . ".name", $cmd['processNameDisp']);
            Setting::set("process." . $id . ".command", $cmd['processCommand']);
            Setting::set("process." . $id . ".period", $cmd['processPeriod']);
            Setting::set("process." . $id . ".periodType", $cmd['processPeriodType']);
            Setting::set("process." . $id . ".periodCount", ProcessHelper::periodConverter($cmd['processPeriod'], $cmd['processPeriodType']));

            $this->redirect(['/dev/processManager/']);
        }

        Asset::registerJS('application.static.js.lib.ace');
        $this->renderForm('settings.DevSettingsProcessManagerForm', [
            'content' => $content,
            'name' => count($path) > 1 ? $path : '',
            'processName' => $name,
            'processNameDisp' => $name,
            'processCommand' => $command,
            'processSettingsId' => $id,
            'periodType' => $periodType,
            'period' => $period
        ]);
    }

    public function actionDelete($id = null) {
        if (isset($id)) {
            Setting::remove('process.' . $id);
        }

        $this->redirect(['/dev/processManager/']);
    }

    public function actionSave() {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);
        $path     = explode(".", $post['active']);
        $filePath = Yii::getPathOfAlias((count($path) > 2 ? "application.modules." . $path[1] . ".commands." . $path[2] : "application.commands." . $path[1])) . ".php";

        if (is_file($filePath)) {
            file_put_contents($filePath, $post['content']);
        }
    }

}