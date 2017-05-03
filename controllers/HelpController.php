<?php

class HelpController extends Controller {
     public function actionWelcome() {
          $this->redirect(['/docs/welcome']);
     }
}