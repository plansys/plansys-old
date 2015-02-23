<?php
class JsonModel{
    private $data;
    public $path;
    public $default = [];
    
    
    public function setPath($path) {
        $this->path = $path;
    }

    public function init() {
        if (!is_file($this->path)) {
            $json = $this->default;
            $json = json_encode($json, JSON_PRETTY_PRINT);

            file_put_contents($this->path, $json);
        }
        $this->data = json_decode(file_get_contents($this->path), true);
    }
    
    public function get($key) {
        $keys = explode('.', $key);

        $arr = $this->data;
        while ($k = array_shift($keys)) {
            $arr = &$arr[$k];
        }

        return $arr;
    }

    public function set($key, $value) {
        $this->setInternal($this->data, $key, $value);
        file_put_contents($this->path, json_encode($this->data, JSON_PRETTY_PRINT));
    }

    private function setInternal(&$arr, $path, $value) {
        $keys = explode('.', $path);

        while ($key = array_shift($keys)) {
            $arr = &$arr[$key];
        }

        $arr = $value;
    }
    
    public static function load($file){
        $model = new JsonModel();
        $model->path = $file;
        $model->init();
        return $model;
    }
}