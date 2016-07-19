<?php 

class WebRequest extends CHttpRequest
{
    public function validateCsrfToken($event)
    {
        if ($this->getIsPostRequest() ||
            $this->getIsPutRequest() ||
            $this->getIsPatchRequest() ||
            $this->getIsDeleteRequest()
        ) {
            $ctrl = Yii::app()->createController($this->getParam('r'));
            if (count($ctrl) > 0) {
                $ctrl  = $ctrl[0];
                if (!$ctrl->enableCsrf) {
                    return true;
                }
            }
            
            $cookies = $this->getCookies();
            $method = $this->getRequestType();
            switch ($method) {
                case 'POST':
                    if (empty($this->getPost($this->csrfTokenName))) {
                        $input = json_decode(file_get_contents('php://input'), true);;
                        $userToken = $input[$this->csrfTokenName];
                    } else {
                        $userToken = $this->getPost($this->csrfTokenName);
                    }
                    break;
                case 'PUT':
                    if (empty($this->getPut($this->csrfTokenName))) {
                        $input = json_decode(file_get_contents('php://input'), true);;
                        $userToken = $input[$this->csrfTokenName];
                    } else {
                        $userToken = $this->getPut($this->csrfTokenName);
                    }
                    break;
                case 'PATCH':
                    if (empty($this->getPatch($this->csrfTokenName))) {
                        $input = json_decode(file_get_contents('php://input'), true);;
                        $userToken = $input[$this->csrfTokenName];
                    } else {
                        $userToken = $this->getPatch($this->csrfTokenName);
                    }
                    break;
                case 'DELETE':
                    if (empty($this->getDelete($this->csrfTokenName))) {
                        $input = json_decode(file_get_contents('php://input'), true);;
                        $userToken = $input[$this->csrfTokenName];
                    } else {
                        $userToken = $this->getDelete($this->csrfTokenName);
                    }
                    break;
            }

            if (!empty($userToken) && $cookies->contains($this->csrfTokenName)) {
                $cookieToken = $cookies->itemAt($this->csrfTokenName)->value;
                $valid = $cookieToken === $userToken;
            } else
                $valid = false;
            if (!$valid)
                throw new CHttpException(400, Yii::t('yii', 'The CSRF token could not be verified.'));
        }
    }
}