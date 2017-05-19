<?php

class PageController extends Controller {
     
     public $enableCsrf = false;

     public function resolve($page) {
         $path = ['app.pages', 'application.pages'];
         foreach ($path as $p) {
              $f = Yii::getPathOfAlias($p . '.' . $page) . ".php";
              if (is_file($f)) {
                   return $f;
              }
         }
         return false;
     }
     
     public function actionIndex() {
         ?>
<div id="root"></div>
<script>
     window.page = {
          require: ['Hello', 'Table'],
          render: function(React, el) {
               return h('div', [h('Hello'),  h('Table', 'mimiin ddede')]);
          }
     };
</script>
<script type="text/javascript" src="http://p.plansys.co:8080/bundle.js"></script>
         <?php
    }
}