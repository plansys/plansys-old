<?php
class FileManager extends CComponent {
     
     public static function read($path) {
          $state = new State("builder-code:memory");
          $path = realpath($path);
          
          $content = $state->get($path);
          
          if (is_null($content)) {
               $content = file_get_contents($path);
               $state->set($path, $content);
          }
          
          return $content;
     }
     
     public static function write($path, $content) {
          $state = new State("builder-code:memory");
          $path = realpath($path);
          
          $state->set($path, $content);
          
          file_put_contents($path, $content);
     }
     
     public function close($path) {
          $state = new State('builder-code:memory');
          $path = realpath($path);
          
          $state->del($path);
     }
}