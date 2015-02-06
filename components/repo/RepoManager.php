<?php

class RepoManager extends CComponent {

    public $repoPath;

    public function relativePath($path) {
        return RepoManager::getRelativePath($path);
    }

    public static function getRelativePath($path) {
        $rp = Setting::get('repo.path');
        $rrp = str_replace("\\", "/", realpath($rp));

        ## check if repo is part of the path (when repopath is a relative path)
        if (strpos($path, $rp) !== 0) {
            ## check if first directory of the path is inside repo
            $pathArr = explode("/", $path);
            $combined = str_replace("//", '/', $rp . "/" . $pathArr[0]);
            if (realpath($combined)) {
                ## the path is inside repo AND already relative
                return $path;
            } else {
                if (strpos($path, $rrp) === 0) {
                    ## this path is inside repo AND absolute path
                    $path = substr($path, strlen($rrp));
                    return $path;
                } else {
                    ## the path is outside repo, just return it.
                    return $path;
                }
            }
        } else {
            ## this path is already relative 
            return $path;
        }
    }

    public static function createDir($path) {
        $path = RepoManager::resolve($path);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }

    public static function count($path, $pattern = "", $params = []) {
        $path = RepoManager::resolve($path);
        if (!is_dir($path)) {
            return 0;
        }
        if (!isset(RepoManager::$fileCounts[md5($path . $pattern . json_encode($params))])) {
            RepoManager::listAll($path, $pattern, $params);
        }
        $count = @RepoManager::$fileCounts[md5($path . $pattern . json_encode($params))];

        return (is_numeric($count) ? $count : 0);
    }

    public static function preparePattern($pattern) {
        $pattern = '/' . trim($pattern, "/") . '/';
        preg_match_all("/\{(.*?)\}/", $pattern, $blocks);
        $columns = [];
        foreach ($blocks[1] as $k => $b) {
            $bl = explode(":", $b);
            $name = trim($bl[0]);
            $columns[] = $name;
            $type = trim($bl[1]);
            switch ($type) {
                case "num":
                    $pattern = str_replace('{' . $b . '}', "(?<{$name}>\d+)", $pattern);
                    break;
                case "date":
                    $pattern = str_replace('{' . $b . '}', "(?<{$name}>\d+-\d+-\d+)", $pattern);
                    break;
                case "string":
                case "str":
                    $pattern = str_replace('{' . $b . '}', "(?<{$name}>[\w \~\!\@\#\$\%\^\&\_\-\.]+)", $pattern);
                case "word":
                    $pattern = str_replace('{' . $b . '}', "(?<{$name}>[\w]+)", $pattern);
                default:
                    $pattern = str_replace('{' . $b . '}', "(?<{$name}>{$type})", $pattern);

                    break;
            }
        }

        return [
            'pattern' => $pattern,
            'columns' => $columns
        ];
    }

    public static function parseName($entry, $pattern, $parseEmpty = true) {
        preg_match($pattern['pattern'], $entry, $preg);
        $f = [];
        $f['file'] = $entry;
        if ($parseEmpty) {
            foreach ($pattern['columns'] as $c) {
                $f[$c] = "";
            }
        }

        if (count($preg) > 0) {
            foreach ($preg as $k => $l) {
                if (is_int($k))
                    continue;

                $f[$k] = $l;
            }
        }
        return $f;
    }

    public static function parse($entry, $pattern) {
        $pattern = RepoManager::preparePattern($pattern);
        return RepoManager::parseName($entry, $pattern);
    }

    public static $fileCounts = [];

    public static function isColumnFilterMatch($columns, $filter, $filterColumn) {
        $value = @$columns[$filterColumn];
        if ($value == null)
            return false;

        switch ($filter['type']) {
            case "string":
                if ($filter['value'] != "" || $filter['operator'] == 'Is Empty') {
                    switch ($filter['operator']) {
                        case "Contains":
                            return (strpos($value, $filter['value']) !== false);
                            break;
                        case "Does Not Contain":
                            return (strpos($value, $filter['value']) === false);
                            break;
                        case "Is Equal To":
                            return $value == $filter['value'];
                            break;
                        case "Starts With":
                            return (strpos($value, $filter['value']) === 0);
                            break;
                        case "Ends With":
                            return substr($haystack, -strlen($filter['value'])) === $filter['value'];
                            break;
                        case "Is Any Of":
                            $array = preg_split('/\s+/', trim($filter['value']));

                            foreach ($array as $a) {
                                if (stripos($value, $a) !== false)
                                    return true;
                            }
                            return false;
                            break;
                        case "Is Not Any Of":
                            $array = preg_split('/\s+/', trim($filter['value']));
                            foreach ($array as $a) {
                                if (stripos($value, $a) !== false)
                                    return false;
                            }
                            return true;
                            break;
                        case "Is Empty":
                            return $value == "";
                            break;
                    }
                }
                break;
            case "number":
                if ($filter['value'] != "" || $filter['operator'] == 'Is Empty') {
                    switch ($filter['operator']) {
                        case "=":
                        case "<>":
                        case ">":
                        case '>':
                        case '>=':
                        case '<=':
                        case '<':
                            eval('$result = $value ' . $filter['operator'] . ' ' . $filter['value']);
                            return $result;
                            break;
                        case "Is Empty":
                            return $value == "";
                            break;
                    }
                }
                break;
            case "date":
                switch ($filter['operator']) {
                    case "Between":
                    case "Weekly":
                    case "Monthly":
                    case "Yearly":
                        if (@$filter['value']['from'] != '' && @$filter['value']['to'] != '') {
                            $date = date('Y-m-d', strtotime($value));
                            $from = date('Y-m-d', strtotime(@$filter['value']['from']));
                            $to = date('Y-m-d', strtotime(@$filter['value']['to']));
                            if ($date > $from && $date < $to) {
                                return true;
                            }
                            return false;
                        }
                        break;
                    case "Not Between":
                        if (@$filter['value']['from'] != '' && @$filter['value']['to'] != '') {
                            $date = date('Y-m-d', strtotime($value));
                            $from = date('Y-m-d', strtotime(@$filter['value']['from']));
                            $to = date('Y-m-d', strtotime(@$filter['value']['to']));
                            if ($date > $from && $date < $to) {
                                return false;
                            }
                            return true;
                        }
                        break;
                    case "More Than":
                        if (@$filter['value']['from'] != '') {
                            $date = date('Y-m-d', strtotime($value));
                            $from = date('Y-m-d', strtotime(@$filter['value']['from']));
                            if ($date > $from) {
                                return true;
                            }
                            return false;
                        }
                        break;
                    case "Less Than":
                        if (@$filter['value']['to'] != '') {
                            $date = date('Y-m-d', strtotime($value));
                            $to = date('Y-m-d', strtotime(@$filter['value']['to']));
                            if ($date < $to) {
                                return true;
                            }
                            return false;
                        }
                        break;
                    case "Daily":
                        if (@$filter['value'] != '') {
                            $date = date('Y-m-d', strtotime($value));
                            $to = date('Y-m-d', strtotime(@$filter['value']));
                            if ($date == $to) {
                                return true;
                            }
                            return false;
                        }
                        break;
                }
                break;
            case "list":
                if ($filter['value'] != '') {
                    return $value == $filter['value'];
                }
                break;
            case "relation":
                if ($filter['value'] != '') {
                    return $value == $filter['value'];
                }
                break;
            case "check":
                $array = $filter['value'];

                foreach ($array as $a) {
                    if (stripos($value, $a) !== false)
                        return true;
                }
                return false;
                break;
        }
    }

    public static function isColumnMatch($columns, $params) {
        foreach ($params as $col => $param) {
            $valid = RepoManager::isColumnFilterMatch($columns, $param, $col);
            if (!$valid) {
                return false;
            }
        }
        return true;
    }

    public static function listAll($path, $pattern = "", $params = []) {
        $result = [];

        $path = RepoManager::resolve($path);
        if (!is_dir($path)) {
            return $result;
        }

        ## paging
        $pageSize = 25;
        $currentPage = 1;
        if (isset($params['paging'])) {
            $paging = $params['paging'];
            if (is_array($paging)) {
                $pageSize = $paging['pageSize'];
                $currentPage = $paging['currentPage'];
            }
        }
        $pageStart = ($currentPage - 1) * $pageSize;
        $pageEnd = $currentPage * $pageSize;

        ## order
        $order = [];
        if (is_array(@$params['order']) && isset($params['order']['order_by'][0])) {
            $order = @$params['order']['order_by'][0];
        }

        ## filtering 
        $where = [];
        if (isset($params['where']) && is_array($params['where'])) {
            $where = $params['where'];
        }

        ## splitting
        $preparedPattern = RepoManager::preparePattern($pattern);

        ## listing dir
        if ($handle = opendir($path)) {
            $i = 0;
            $count = 0;
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $isPatternMatch = false;
                    if ($pattern != "") {
                        $f = RepoManager::parseName($entry, $preparedPattern, false);
                        if (count($f) > 1) {
                            $isPatternMatch = true;
                        }
                    } else {
                        $f = [];
                        $f['file'] = $entry;
                        $isPatternMatch = true;
                    }

                    $isNameMatch = true;
                    if (count($where) > 0) {
                        $isNameMatch = RepoManager::isColumnMatch($f, $where);
                    }

                    if ($isPatternMatch && $isNameMatch) {
                        if (count($order) > 0) {
                            $colname = $f[$order['field']];
                            while (isset($result[$colname])) {
                                $colname .= "_";
                            }
                            $result[$colname] = $f;
                        } else if ($count >= $pageStart && $count < $pageEnd && $isPatternMatch) {
                            $result[] = $f;
                        }
                        $count++;
                    }
                    $i++;
                }
            }
            closedir($handle);
        }
        RepoManager::$fileCounts[md5($path . $pattern . json_encode($params))] = $count;

        if (count($order) > 0) {
            if ($order['direction'] == "asc") {
                ksort($result);
            } else {
                krsort($result);
            }

            $result = array_values($result);
            $result = array_slice($result, $pageStart, $pageSize);
        }

        return $result;
    }

    public static function resolve($path) {
        if (realpath($path)) {
            return $path;
        }

        $rp = Setting::get('repo.path');
        $rrp = realpath($rp);

        ## check if repo is part of the path
        if (strpos($path, $rp) !== 0) {
            ## check if first directory of the path is inside repo
            $pathArr = explode("/", $path);
            $combined = str_replace("//", '/', $rp . "/" . $pathArr[0]);
            if (realpath($combined)) {
                ## the path is inside repo
                $path = str_replace("//", '/', $rp . "/" . $path);
            } else {
                ## the path is outside repo, just return it.
                $path = str_replace("\\", "/", $path);
                $path = str_replace("//", "/", $path);
                return $path;
            }
        }
        $file = basename($path);
        $dir = dirname($path);
        if (strpos($dir, $rp) === 0) {
            $dir = $rrp . substr($dir, strlen($rp));
        }

        $result = $dir . DIRECTORY_SEPARATOR . $file;
        $result = str_replace("\\", "/", $result);
        $result = str_replace("//", "/", $result);

        return $result;
    }

    public static function getModuleDir() {
        if (Yii::app()->user->isGuest) {
            return DIRECTORY_SEPARATOR;
        } else {
            $roles = Yii::app()->user->model->roles;

            $path = Yii::app()->user->role;
            foreach ($roles as $r) {
                if (Yii::app()->user->fullRole == $r['role_name'] && $r['repo_path'] != '') {
                    $path = $r['repo_path'];
                }
            }
            return DIRECTORY_SEPARATOR . $path;
        }
    }

    public static function getPerms($path) {
        $perms = fileperms($path);

        if (($perms & 0xC000) == 0xC000) {
            // Socket
            $info = 's';
        } elseif (($perms & 0xA000) == 0xA000) {
            // Symbolic Link
            $info = 'l';
        } elseif (($perms & 0x8000) == 0x8000) {
            // Regular
            $info = '-';
        } elseif (($perms & 0x6000) == 0x6000) {
            // Block special
            $info = 'b';
        } elseif (($perms & 0x4000) == 0x4000) {
            // Directory
            $info = 'd';
        } elseif (($perms & 0x2000) == 0x2000) {
            // Character special
            $info = 'c';
        } elseif (($perms & 0x1000) == 0x1000) {
            // FIFO pipe
            $info = 'p';
        } else {
            // Unknown
            $info = 'u';
        }

        // Owner
        $info .= (($perms & 0x0100) ? 'r' : '-');
        $info .= (($perms & 0x0080) ? 'w' : '-');
        $info .= (($perms & 0x0040) ?
                        (($perms & 0x0800) ? 's' : 'x' ) :
                        (($perms & 0x0800) ? 'S' : '-'));

        // Group
        $info .= (($perms & 0x0020) ? 'r' : '-');
        $info .= (($perms & 0x0010) ? 'w' : '-');
        $info .= (($perms & 0x0008) ?
                        (($perms & 0x0400) ? 's' : 'x' ) :
                        (($perms & 0x0400) ? 'S' : '-'));

        // World
        $info .= (($perms & 0x0004) ? 'r' : '-');
        $info .= (($perms & 0x0002) ? 'w' : '-');
        $info .= (($perms & 0x0001) ?
                        (($perms & 0x0200) ? 't' : 'x' ) :
                        (($perms & 0x0200) ? 'T' : '-'));

        return $info;
    }

    public function browse($dir = "") {
        if (!is_dir(Setting::get('repo.path'))) {
            mkdir(Setting::get('repo.path'));
        }

        $originaldir = $dir;
        $isRelativePath = false;
        if ($dir == "" || $dir == DIRECTORY_SEPARATOR) {
            $dir = $this->repoPath;
            $parent = "";
        } else {
            if (strpos($this->repoPath, $dir) === 0) {
                $isRelativePath = true;
            }
            $dir = $this->repoPath . DIRECTORY_SEPARATOR . trim($dir, DIRECTORY_SEPARATOR);
            $parent = dirname($dir);
        }


        if (!realpath($dir) && $isRelativePath) {
            $dir = getcwd() . DIRECTORY_SEPARATOR . $dir;
        }


        $list = [];

        if (!is_dir($dir)) {
            if (RepoManager::getModuleDir() == $originaldir) {
                mkdir($dir);
            } else {
                return false;
            }
        }

        ## listing dir
        $list = [];

        if ($handle = opendir($dir)) {
            $count = 0;
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $path = RepoManager::resolve($dir . DIRECTORY_SEPARATOR . $entry);

                    $perm = RepoManager::getPerms($path);
                    $size = filesize($path);

                    $path = $this->relativePath($dir . DIRECTORY_SEPARATOR . $entry);
                    $path = substr($path, strlen($this->repoPath));

                    $list[] = [
                        'name' => $entry,
                        'type' => $perm[0] == 'd' ? "dir" : "." . substr($entry, strrpos($entry, '.') + 1),
                        'size' => $size,
                        'downloadPath' => base64_encode($path),
                        'path' => $path
                    ];
                    $count++;
                }
            }
            closedir($handle);
        }

        usort($list, ['RepoManager', 'sortItem']);
        $count = count($list);

        if ($originaldir != "" && $originaldir != RepoManager::getModuleDir()) {
            $parent = $this->relativePath($parent);
            $parent = substr($parent, strlen($this->repoPath));
        } else {
            $parent = "";
        }

        $detail = [
            'parent' => $parent,
            'path' => $this->relativePath($dir),
            'type' => 'dir',
            'item' => $list,
            'count' => $count,
        ];


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
//        $json = JsonModel::load($path . DIRECTORY_SEPARATOR . $file . '.json');
//        $json->default;
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

            $zip = "zip";
            $delim = ';';
            if (substr(php_uname(), 0, 7) == "Windows") {
                $zip = Yii::getPathOfAlias('application.commands.shell.zip') . ".exe";
                $delim = '&';
            }

            // zip the stuff (dir and all in there) into the tmp_zip file
            $dir = dirname($path);
            $command = "cd {$dir} {$delim} {$zip} -r '{$tmp_zip}' '{$base}'";
            `$command`;

            // deliver the zip file
            $fp = fopen("$tmp_zip", "r");
            echo fpassthru($fp);

            // clean up the tmp zip file
            `rm $tmp_zip `;
        } else {
            Yii::app()->request->sendFile($name, file_get_contents($path));
        }
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
    }

}
