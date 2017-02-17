<?php

class WebRequest extends CHttpRequest {

    public function getIsAjaxRequest() {
        return isset($_SERVER['HTTP_AJAX']) || strpos(@$_SERVER['HTTP_ACCEPT'], 'json') !== false;
    }

    private function getTokenFromInput() {
        $input = json_decode(file_get_contents('php://input'), true);
        return $input[$this->csrfTokenName];
    }

    public function validateCsrfToken($event) {
        if ($this->getIsPostRequest() ||
                $this->getIsPutRequest() ||
                $this->getIsPatchRequest() ||
                $this->getIsDeleteRequest()
        ) {
            $ctrl = Yii::app()->createController($this->getParam('r'));
            if (count($ctrl) > 0) {
                $ctrl = $ctrl[0];
                if (!$ctrl->enableCsrf) {
                    return true;
                }
            }

            $cookies = $this->getCookies();
            $method  = $this->getRequestType();
            switch ($method) {
                case 'POST':
                    if (empty($this->getPost($this->csrfTokenName))) {
                        $userToken = $this->getTokenFromInput();
                    } else {
                        $userToken = $this->getPost($this->csrfTokenName);
                    }

                    break;
                case 'PUT':
                    if (empty($this->getPut($this->csrfTokenName))) {
                        $userToken = $this->getTokenFromInput();
                    } else {
                        $userToken = $this->getPut($this->csrfTokenName);
                    }
                    break;
                case 'PATCH':
                    if (empty($this->getPatch($this->csrfTokenName))) {
                        $userToken = $this->getTokenFromInput();
                    } else {
                        $userToken = $this->getPatch($this->csrfTokenName);
                    }
                    break;
                case 'DELETE':
                    if (empty($this->getDelete($this->csrfTokenName))) {
                        $userToken = $this->getTokenFromInput();
                    } else {
                        $userToken = $this->getDelete($this->csrfTokenName);
                    }
                    break;
            }

            if (!empty($userToken) && $cookies->contains($this->csrfTokenName)) {
                $cookieToken = $cookies->itemAt($this->csrfTokenName)->value;
                $valid       = $cookieToken === $userToken;
            } else
                $valid = false;

            if (!$valid && empty($_FILES) && !Yii::app()->user->isGuest)
                throw new CHttpException(403, Yii::t('yii', 'The CSRF token could not be verified.'));
        }
    }

}
