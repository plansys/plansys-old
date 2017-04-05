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
