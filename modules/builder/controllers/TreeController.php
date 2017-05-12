<?php

function recursiveDelete($str) {
    if (is_file($str)) {
        return unlink($str);
    } elseif (is_dir($str)) {
        $scan = glob(rtrim($str, '/') . '/*');
        foreach ($scan as $index => $path) {
            recursiveDelete($path);
        }

        return rmdir($str);
    }
}
class TreeController extends Controller {
    private $getcwdlen = 0;

    private function getcwdlen() {
        if ($this->getcwdlen == 0) {
            $this->getcwdlen = strlen(getcwd());
        }

        return $this->getcwdlen;
    }

    private function convertProp($file, $override=[]) {
        $type = $file->isDir() ? 'dir' : 'file';
        $item = [
        'n' => $file->getBasename(),
        'p' => $file->getPathName(),           
        'ext' => $file->getExtension(),            
        'd' => substr($file->getPathname(), $this->getcwdlen() + 1),
        't' => $type, 
        'l' => 0, 
        'id' => crc32($file->getPathname()), 
        ];
        $item = array_merge($item, $override);

        return $item;
    }

    private function search($n, $dir, $ext='', $abspath=false) {
        $dir = '/' . trim($dir, '/');
        
        if (!$abspath) {
            $dir = getcwd() . $dir;
        }
        $it = new RecursiveDirectoryIterator($dir);
        $result = [];
        foreach (new RecursiveIteratorIterator($it) as $file) {
            if ($file->isDir()) {
                continue;
            }
            if (stripos($file->getBasename(), $n) === false) {
                continue;
            }
            if ($ext != '' && $file->getExtension() != $ext) {
                continue;
            }
                
            $result[] = $this->convertProp($file);
        }

        return $result;
    }

    public function actionSearch($n='', $dir='/', $mode='file') {
        $result = [];
        switch ($mode) {
            case 'file':
                $result = $this->search($n, $dir);
            break;
            case 'model':
            case 'controller':
            case 'form':
                $dirs = $this->modeDirList($mode);
                $res = $this->listDir($dirs, $dir);
                $result = [];
                foreach ($res as $r) {
                    if ($r['t'] == 'dir' && is_dir($r['p'])) {
                        $result = array_merge($result, $this->search($n, $r['p'], 'php', true));
                    }
                }
                break;
            case 'module':
                break;
        }
        echo json_encode($result);
    }

    public function actionMkdir($dir='') {
        $rootdir = Yii::getPathOfAlias('webroot');
        if (strpos($dir, $rootdir) === 0) {
            @mkdir($dir, 0777, true);
            @chmod($dir, 0777);
        }
    }

    public function actionTouch($path, $name='', $mode='') {
        $rootdir = Yii::getPathOfAlias('webroot');
        if (strpos($path, $rootdir) === 0) {
            switch ($mode) {
                // case "websockets":
                //      $name = ucfirst(preg_replace("/[^A-Za-z0-9 ]/", '', $name));
                //      if (substr($name, 0, -2) != "Ws") {
                //           $name = $name . "Ws";
                //      }
                //      $content = include("code_template/websocket.txt");
                //      break;
                default:$content = '';
                break;
            }
            if ($name != '') {
                $file = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;
            } else {
                $file = rtrim($path, DIRECTORY_SEPARATOR);
            }
            if (!is_file($file)) {
                file_put_contents($file, $content);
            }
            echo $file;
        }
    }

    public function actionRmrf($path) {
        $rootdir = Yii::getPathOfAlias('webroot');
        if (strpos($path, $rootdir) === 0) {
            recursiveDelete($path);
        }
    }

    public function listDir($dirs, $root) {
        $psdir = Yii::getPathOfAlias('application');

        return $this->listRaw($dirs, function ($val) use ($psdir, $root) {
            if ($root == '') {
                $path = explode(DIRECTORY_SEPARATOR, $val->getPathname());
                $name = $path[count($path) - 2];
                $system = strpos($val->getPathname(), $psdir) === 0;
                $prefix = '';
                if ($name != 'app' && $name != 'plansys') {
                    $prefix = $system ? 'Plansys: ' : '';
                }

                return $this->convertProp($val, ['n' => $prefix . ucfirst($name), 'removable' => false]);
            }
            $opt = [];
            if (!$val->isDir()) {
                $opt = ['n' => substr($val->getBasename(), 0, -4)];
            }

            return $this->convertProp($val, $opt);
        }
        );
    }
    
    public function actionGetsize($dir) {
        echo @filesize(Setting::getRootpath() . DIRECTORY_SEPARATOR . $dir);
    }
    
    public function listRaw($list, $func) {
        $res = [];
        foreach ($list as $l) {
            $modeex = explode(':', $l);
            if (count($modeex) == 1) {
                $mode = 'glob';
                $l = $modeex[0];
            } else {
                $mode = $modeex[0];
                $l = $modeex[1];
            }
            $dir = Yii::getPathOfAlias($l);
            $dir = str_replace('[_]', '.', $dir);
            if ($mode == 'glob') {
                $it = new GlobIterator($dir);
                foreach ($it as $item) {
                    $v = $func($item);
                    if ($v !== false) {
                        $res[] = $v;
                    }
                }
            } elseif ($mode == 'dir') {
                $it = new DirectoryIterator($dir);
                foreach (new IteratorIterator($it) as $file) {
                    if (!$file->isDot() && $file->isDir()) {
                        $v = $func($file);
                        if ($v !== false) {
                            $res[] = $v;
                        }
                    }
                }
            }
        }

        return $res;
    }

    private function modeDirList($mode, $dir='') {
        switch ($mode) {
            case 'form': if ($dir == '') {
                    $dirs = ['application.forms', 'application.modules.*.forms', 'app.forms', 'app.modules.*.forms'];
            } else {
                $dirs = ['dir:' . str_replace('/', '.', $dir), 'glob:' . str_replace('/', '.', $dir) . '.*[_]php'];
            }
            break;
            case 'model': if ($dir == '') {
                    $dirs = ['application.models', 'app.models'];
            } else {
                $dirs = ['dir:' . str_replace('/', '.', $dir), 'glob:' . str_replace('/', '.', $dir) . '.*[_]php'];
            }
            break;
            case 'controller': if ($dir == '') {
                    $dirs = ['application.controllers', 'application.modules.*.controllers', 'app.controllers', 'app.modules.*.controllers'];
            } else {
                $dirs = ['dir:' . str_replace('/', '.', $dir), 'glob:' . str_replace('/', '.', $dir) . '.*[_]php'];
            }
            break;
            case 'module': if ($dir == '') {
                    $dirs = ['application.modules.*.*[_]php', 'app.modules.*.*[_]php'];
            } else {
                $dirs = ['glob:' . str_replace('/', '.', $dir) . '.*[_]php'];
            }
            break;
            default:$dirs = [];
            break;
        }

        return $dirs;
    }

    public function actionLs($dir='', $mode='') {
        try {
            if ($mode == 'file') {
                $result = [];
                $dirs = [];
                $files = [];
                $it = new DirectoryIterator(getcwd() . '/' . $dir);
                foreach (new IteratorIterator($it) as $file) {
                    if (!$file->isDot()) {
                        $item = $this->convertProp($file);
                        if ($item['t'] == 'file') {
                            array_unshift($files, $item);
                        } else {
                            array_unshift($dirs, $item);
                        }
                    }
                }
                asort($dirs);
                asort($files);
                $res = array_merge($dirs, $files);
                echo json_encode($res);
            } else {
                $dirs = $this->modeDirList($mode, $dir);
                echo json_encode($this->listDir($dirs, $dir));
            }
        } catch (Exception$e) {
            echo '[]';
        }
    }

    public function actionMv($from, $to) {
        if (file_exists($from) && !file_exists($to)) {
            if ($from == $to) {
                return;
            }
            if (!rename($from, $to)) {
                throw new CHttpException(403);
            }
            echo trim(substr($to, strlen(Setting::getRootpath())), '/');
            die();
        }
        throw new CHttpException(403, $from . ' ~> ' . $to);
    }
    
    private function recurseCopy($src, $dst) { 
        $dir = opendir($src); 
        @mkdir($dst); 
        while (false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    $this->recurseCopy($src . '/' . $file,$dst . '/' . $file); 
                } else { 
                    copy($src . '/' . $file,$dst . '/' . $file); 
                } 
            } 
        } 
        closedir($dir); 
    }
    
    public function actionCp($from, $to) {
        if (file_exists($from) && !file_exists($to)) {
            if ($from == $to) {
                return;
            }
            $this->recurseCopy($from, $to);
            echo trim(substr($to, strlen(Setting::getRootpath())), '/');
            die();
        }
        throw new CHttpException(403, $from . ' ~> ' . $to);
    }
}
