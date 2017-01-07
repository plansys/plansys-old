<?php

class ErrorHandler extends CErrorHandler {

    public $errorAction = 'site/error';
    public $handledErrorCode = [404, 403, 400];

    protected function renderError() {
        $error = $this->getError();

        if (!in_array($error['code'], $this->handledErrorCode)) {
            $this->errorAction = null;
        }

        parent::renderError();
    }

}
