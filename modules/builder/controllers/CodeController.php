<?php

class CodeController extends Controller {
     public $enableCsrf = false;
     
     public function actionIndex($f) {
          echo file_get_contents($f);
     }
     
     public function actionSave($f) {
          $post = json_decode(file_get_contents("php://input"), true);
          set_error_handler(function($errno, $errstr, $errfile, $errline) {
              echo $errstr;
          });
          
          try {
               # only store history for file less than 1 mb
               if (filesize($f) < 1048576 && @$_GET['h'] == 1) { 
                    $history_path = Setting::getAssetPath() . '/code_history/';
                    if (!is_dir($history_path)) {
                         mkdir($history_path);
                    }
                    
                    if (!is_writable($history_path)) {
                         chmod($history_path, 0777);
                    }
                    
                    $hash = crc32($f);
                    if (!is_dir($history_path . $hash)) {
                         $history_path = $history_path . $hash . "/";
                         mkdir($history_path);
                    }
                    
                    if (!is_writable($history_path)) {
                         chmod($history_path, 0777);
                    }
                    
                    $hash = crc32($f);
                    $i = 0;
                    $history_file = $history_path . "{$i}";
                    while (is_file($history_file)) {
                         $i++;
                         $history_file = $history_path . "{$i}"; 
                    }
               }
               
               if (@file_put_contents($f, $post['content'])) {
                    if (isset($history_file)) {
                         copy($f, $history_file);
                    }
                    echo 'success'; 
               }
          } catch(Exception $e) {
               echo $e->getMessage();
               die();
          }
     }
}