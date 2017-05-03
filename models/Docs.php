<?php

class Docs {
     private static $path = false;
     private static function path() {
          if (!self::$path)
               self::$path = Yii::getPathOfAlias('application.modules.docs.views');
               
          return self::$path;
     }
     
     private static function formatItem($item) {
          $pi = pathinfo($item);
          $dir = dirname($item);
          $isdir = is_dir($item);
          $basepath = self::path();
          $path = trim(substr($item, strlen($basepath)), DIRECTORY_SEPARATOR);
          $pathex = explode(DIRECTORY_SEPARATOR, $path);
          array_pop($pathex);
          $parentex = [];
          foreach($pathex as $p) {
               $i = trim(Helper::explodeFirst(".", $p));
               if ($i) $parentex[] = $i;
          }
          $parent = implode(".", $parentex);
          
          $name = basename($item, '.' . $info['extension']);
          $labelex = explode(".", $name);
          $parentex[] = array_shift($labelex);
          $id = implode(".", $parentex);
          
          if (!$isdir) {
               array_pop($labelex);
          }
          $label = implode(".", $labelex);
          $item = [
               'label' => trim($label),
               'id' => $id,
               'dir' => $path,
               'parent' => $parent,
               'items' => [],
               'canExpand' => $isdir
          ];
          
          return $item;
     }
     
     public static function browse($item) {
          $basepath = self::path();
          $dir = '';
          if ($item) {
               $dir = DIRECTORY_SEPARATOR . $item['dir'];
          }
          $items = glob($basepath . $dir . DIRECTORY_SEPARATOR . "*");
          natsort($items);
          $result = [];
          foreach($items as $item) {
               $result[] = self::formatItem($item);
          } 
          return $result;
     }
}