<?php

class TreeController extends Controller {
     
     private $getcwdlen = 0;
     private function getcwdlen() {
          if ($this->getcwdlen == 0) {
               $this->getcwdlen = strlen(getcwd()); 
          }
          return $this->getcwdlen;
     }
     
     private function convertProp($file) {
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
          return $item; 
     }
     
     public function actionSearch($n = '', $dir = '/') {
          $dir = '/' . trim($dir, '/');
          
          $it = new RecursiveDirectoryIterator(getcwd() . $dir);
          $result = []; 
          foreach(new RecursiveIteratorIterator($it) as $file)
          {
               if ($file->isDir()) continue;
               if (stripos($file->getBasename(), $n) === false) continue;
               
               $result[] = $this->convertProp($file);
          }

          echo json_encode($result);
     }
     
     public function actionMkdir($dir = '') {
          $rootdir = Yii::getPathOfAlias('webroot');
          if (strpos($dir, $rootdir) === 0) {
               @mkdir($dir, 0777, true);
               @chmod($dir, 0777);
          }
     }
     
     public function actionTouch($path, $name = "", $mode = "") {
          $rootdir = Yii::getPathOfAlias('webroot');
          if (strpos($path, $rootdir) === 0) {
               
               switch ($mode) {
                    case "websockets":
                         $name = ucfirst(preg_replace("/[^A-Za-z0-9 ]/", '', $name));
                         if (substr($name, 0, -2) != "Ws") {
                              $name = $name . "Ws";
                         }
                         $path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name . ".php"; 
                         $content = <<<EOF
<?php 

class $name extends WebSocketController {

    // this function will be executed when 
    // there is new client connnected to websocket
    public function connected(\$client) {
        \$this->broadcast('Hello World'); // broadcast Hello world message to all client
    }
    
    // this function will be executed when 
    // client disconnected from server
    public function disconnected (\$client, \$reason) {
        
    }
    
    // this function will be executed when 
    // server received new message from client
    public function received(\$msg, \$from) {
        
    }
}

EOF;
                         break;
                    default:
                         $content = "";
                         break;
               }
               
               echo $path;
               @file_put_contents($path, $content);
          }
     }
     
     public function actionRmrf($path) {
          function recursiveDelete($str) {
              if (is_file($str)) {
                  return @unlink($str);
              }
              elseif (is_dir($str)) {
                  $scan = glob(rtrim($str,'/').'/*');
                  foreach($scan as $index=>$path) {
                      recursiveDelete($path);
                  }
                  return @rmdir($str);
              }
          }
          
          $rootdir = Yii::getPathOfAlias('webroot');
          if (strpos($path, $rootdir) === 0) {
               recursiveDelete($path);
          }
     }
     
     public function actionLs($dir = '') {
          $it = new DirectoryIterator(getcwd() . '/' . $dir);
          $result = []; 
          $dirs = [];
          $files = [];
          foreach(new IteratorIterator($it) as $file)
          {
               if ($file->getBasename() != '.' && $file->getBasename() != '..') {
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
          echo json_encode(array_merge($dirs, $files));
     }
}
