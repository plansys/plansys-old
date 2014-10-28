<?php

class RepoManager extends CComponent {

    public $repoPath;

    public function relativePath($path) {
        $p = str_replace($this->repoPath, '', $path);
        if ($p == "") {
            return DIRECTORY_SEPARATOR;
        }
        return $p;
    }

    public static function getModuleDir() {
        return "/";
    }

    public function browse($dir = "") {
        if ($dir == "" || $dir == DIRECTORY_SEPARATOR) {
            $dir = $this->repoPath;
            $parent = "";
        } else {
            $dir = $this->repoPath . DIRECTORY_SEPARATOR . trim($dir, DIRECTORY_SEPARATOR);
            $parent = dirname($dir);
        }

        $list = array();
        $olddir = getcwd();
        chdir($dir);
        $output = "";
        exec('ls -la | awk \'{print $1, $5, $9}\'', $output);
        chdir($olddir);

        $list = [];
        foreach ($output as $o) {
            $f = explode(" ", $o);
            if (count($f) > 2) {
                $perm = array_shift($f);
                $size = array_shift($f);
                $file = implode(" ", $f);

                if ($file == "." || $file == ".." || $file[0] == ".")
                    continue;

                $new = array(
                    'name' => $file,
                    'type' => $perm[0] == 'd' ? "dir" : "." . substr($file, strrpos($file, '.') + 1),
                    'size' => $size,
                    'path' => $this->relativePath($dir . DIRECTORY_SEPARATOR . $file)
                );

                if ($new['type'] == "dir") {
                    $new['size'] = 0;
                    array_unshift($list, $new);
                } else {
                    array_push($list, $new);
                }
            }
        }

//        $dire = opendir($dir);
//        while (($currentFile = readdir($dire)) !== false) {
//            if ($currentFile == '.' or $currentFile == '..' or $currentFile[0] == '.') {
//                continue;
//            }
//            $l = $dir . DIRECTORY_SEPARATOR. $currentFile;
//            $list[] = array(
//                'name' => $currentFile,
//                'type' => $this->fileType($l),
//                'size' => filesize($l),
//                'path' => $this->relativePath($l)
//            );
//        }
//        2r    th
//        $list = array();
//        $glob = glob($dir . DIRECTORY_SEPARATOR . '*', GLOB_NOSORT);
//        foreach ($glob as $l) {
//            $itemName = explode(DIRECTORY_SEPARATOR, $l);
//            $itemName = array_pop($itemName);
//            if (substr($itemName, -5) != '.json') {
//                if (is_dir($l)) {
//                    $list[] = array(
//                        'name' => $itemName,
//                        'type' => 'dir',
//                        'size' => 0,
//                        'path' => $this->relativePath($l)
//                    );
//                } else {
//                    $list[] = array(
//                        'name' => $itemName,
//                        'type' => $this->fileType($l),
//                        'size' => filesize($l),
//                        'path' => $this->relativePath($l)
//                    );
//                }
//            }
//        }
        usort($list, array('RepoManager', 'sortItem'));
        $count = count($list);

        $detail = array(
            'parent' => $dir != "" ? $this->relativePath($parent) : "",
            'path' => $this->relativePath($dir),
            'type' => 'dir',
            'item' => $list,
            'count' => $count,
        );
        return $detail;
    }

    public function fileType($file) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($finfo, $file);
        finfo_close($finfo);

        return $fileType == "directory" ? "dir" : $fileType;
    }

    public function fileInfo($file) {
        
    }

    protected static function sortItem($a, $b) {
        if ($a['type'] == 'dir') {
            if ($b['type'] == 'dir')
                return strcmp($a['name'], $b['name']);
            else
                return -1;
        }else if ($a['type'] != 'dir') {
            if ($b['type'] == 'dir')
                return 1;
            else {
                if ($a['type'] != $b['type'])
                    return strcmp($a['type'], $b['type']);

                return strcmp($a['name'], $b['name']);
            }
        }
    }

    public function editMeta() {
        
    }

    public function rename($oldName, $newName) {
        
    }

    public function move($file, $path) {
        
    }

    public function copy($file, $path) {
        
    }

    public function upload($temp, $file, $path) {
        $json = JsonModel::load($path . DIRECTORY_SEPARATOR . $file . '.json');
        $json->default;
        move_uploaded_file($temp, $path . DIRECTORY_SEPARATOR . $file);
    }

    public function replace($file) {
        
    }

    public static function download($name, $path) {
        $oripath = $path;
        $basePath = RepoManager::model()->repoPath;
        $path = $basePath . base64_decode($path);


        if (is_dir($path)) {
            $base = basename($path);
            // we deliver a zip file
            header("Content-Type: archive/zip");

            // filename for the browser to save the zip file
            header("Content-Disposition: attachment; filename=$base" . ".zip");

            // get a tmp name for the .zip
            $tmp_zip = tempnam("tmp", "tempname") . ".zip";

            // zip the stuff (dir and all in there) into the tmp_zip file
            $dir = dirname($path);
            `cd $dir; zip -r $tmp_zip $base`;

            // calc the length of the zip. it is needed for the progress bar of the browser
            $filesize = filesize($tmp_zip);
            header("Content-Length: $filesize");

            // deliver the zip file
            $fp = fopen("$tmp_zip", "r");
            echo fpassthru($fp);

            // clean up the tmp zip file
            `rm $tmp_zip `;
        }
        Yii::app()->request->sendFile($name, file_get_contents($path));
    }

    public function search() {
        
    }

    private static $_model = null;

    public static function model() {
        if (is_null(self::$_model)) {
            self::$_model = new RepoManager();
        }
        return self::$_model;
    }

    public function __construct() {
        if (Setting::get("repo.path") == '') {
            $path = Setting::getRootPath() . DIRECTORY_SEPARATOR . 'repo';
            Setting::set("repo.path", $path);
            $this->repoPath = Setting::get("repo.path");
        } else {
            $this->repoPath = Setting::get("repo.path");
        }

        if (Yii::app()->user->role != 'admin' && Yii::app()->user->role != 'dev') {
            $module = Yii::app()->user->role;
            if (strpos($module, '.') == true) {
                $module = explode('.', $module);
                $module = implode(DIRECTORY_SEPARATOR, $module);
            }
            $this->repoPath = $this->repoPath . DIRECTORY_SEPARATOR . $module;
            if (!file_exists($this->repoPath)) {
                mkdir($this->repoPath, 0777, true);
            }
        }
    }

}
