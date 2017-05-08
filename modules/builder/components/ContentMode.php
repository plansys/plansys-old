<?php 

class ContentMode extends CComponent {
     public static $ctrlalias = "application.modules.builder.controllers.mode";
     public static $alias = "application.modules.builder.views.builder.mode";
     public function getList() {
          return ['code', 'image'];
     }
     
     public function registerJS($mode, $alias) {
          Asset::registerJS(ContentMode::$alias . "." . $mode . "." . $alias);
     }
}