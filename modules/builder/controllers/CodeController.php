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
               if (@file_put_contents($f, $post['content'])) {
                   echo 'success'; 
               }
          } catch(Exception $e) {
               echo $e->getMessage();
               die();
          }
     }
}