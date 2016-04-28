<?php

class RepoForm extends Form {

    public function repoDef() {
        ## repoDef() function should be overridden
        return [];
    }

    public function getRepoDef() {
        return @$this->repoDef();
    }

    public function getUploadPath() {
        return @$this->repoDef['path'];
    }

    public function getFileFields() {
        return @$this->repoDef['fileFields'];
    }

    private $_dirpath = '';

    public function getDirPath() {
        return $this->_dirpath;
    }

    private $oldAttr = [];

    public function load($dirpath) {
        $path = RepoManager::createDir($this->uploadPath);
        $dirpath = trim($dirpath, "/");
        if (!is_dir($path . "/" . $dirpath)) {
            throw new CHttpException(404);
            return false;
        } else {
            $this->_dirpath = $dirpath;
        }

        $info = RepoManager::parse($dirpath, $this->repoDef['pattern']);
        $this->attributes = $info;

        $fb = FormBuilder::load(get_class($this));
        $fb->model = $this;
        foreach ($this->fileFields as $fieldName) {
            $field = $fb->findField(['name' => $fieldName]);
            $filePattern = preg_replace("/\{(.*?)\}/", "*", $field['filePattern']);
            $file = glob($path . "/" . $dirpath . "/" . $filePattern);
            if (count($file) > 0) {
                $this->{$fieldName} = RepoManager::getRelativePath($file[0]);
            }
        }

        $this->oldAttr = $this->attributes;
    }

    public function save() {
        $path = $this->uploadPath;
        $path = trim(RepoManager::createDir($path));

        $valid = $this->validate();
        if ($valid) {
            ## remove delimeter character from attributes
            $attr = $this->attributes;
            foreach ($attr as $k => $i) {
                $attr[$k] = str_replace($this->repoDef['delimeter'], "-", $i);
            }

            ## modify attributes, so it can be safely stored as filename
            foreach ($attr as $k => $a) {
                $attr[$k] = preg_replace("([^\w\s\d\-_~,;:\[\]\(\).]|[\.]{2,})", '', $a);
            }
            extract($attr);

            ## get dir 
            $pattern = $this->repoDef['pattern'];
            preg_match_all("/\{(.*?)\}/", $pattern, $blocks);
            foreach ($blocks[1] as $b) {
                $varname = '$' . explode(":", $b)[0];
                $pattern = str_replace('{' . $b . '}', '{' . $varname . '}', $pattern);
            }
            eval('$dir = "' . $pattern . '";');

            ## create dir
            $newdir = $path . "/" . $dir;
            $dirRenamed = false;
            if (!is_dir($newdir)) {
                if ($this->dirPath == "") {
                    mkdir($newdir, 0777, true);
                } else {
                    rename($path . "/" . $this->dirPath, $newdir);
                    $dirRenamed = true;
                }
            }
            $oldDirPath = $path . "/" . $this->dirPath;
            $this->_dirpath = $dir;

            ## move uploaded file
            $fb = FormBuilder::load(get_class($this));
            $fb->model = $this;
            $model = $this;
            foreach ($this->repoDef['fileFields'] as $fieldName) {
                if ($this->{$fieldName} != '') {
                    ## find the FormField to get its file pattern
                    $field = $fb->findField(['name' => $fieldName]);

                    ## make sure file path is in the right path
                    $this->{$fieldName} = RepoManager::resolve($this->{$fieldName});

                    ## get file extension
                    $ext = pathinfo($this->{$fieldName}, PATHINFO_EXTENSION);

                    ## file is uploaded
                    if (is_file($this->{$fieldName})) {
                        ## move the file to correct location
                        eval('@rename($this->{$fieldName}, $newdir . "/' . $field['filePattern'] . '");');

                        ## assign new location to its var
                        eval('$this->{$fieldName} = $newdir . "/' . $field['filePattern'] . '";');

                        if (isset($this->oldAttr[$fieldName])) {
                            $old = RepoManager::resolve($this->oldAttr[$fieldName]);
                            if ($old != $this->{$fieldName}) {
                                @unlink($old);
                            }
                        }
                    } else if ($dirRenamed) {
                        $this->{$fieldName} = $newdir . substr($this->{$fieldName}, strlen($oldDirPath));
                    }

                    ## change the path to relative path
                    $this->{$fieldName} = RepoManager::getRelativePath($this->{$fieldName});
                }
            }
        }
        return $valid;
    }

}
