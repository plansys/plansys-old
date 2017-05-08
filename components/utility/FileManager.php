<?php
class FileManager extends CComponent {
     public static function write($path, $content) {
          $dir = Yii::getPathOfAlias('webroot.assets.tmp');
          if (!is_dir($dir)) {
               mkdir($dir, true, 0777);
               chmod($dir, 0777);
          }
          $temp = $dir . DIRECTORY_SEPARATOR . microtime(true) . mt_rand(0,10000);
          file_put_contents($temp, $content);
          rename($temp, $path);
     }
}