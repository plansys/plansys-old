<?php

class WebUser extends CWebUser {

    private $_model;

    /**
     * Overrides a Yii method that is used for roles in controllers (accessRules).
     *
     * @param string $operation Name of the operation required (here, a role).
     * @param mixed $params (opt) Parameters for this operation, usually the object to access.
     * @return bool Permission granted?
     */
    public function checkAccess($operation, $params = array()) {
        if (empty($this->id)) {
            // Not identified => no rights
            return false;
        }

        if (Yii::app()->user->isGuest) {
            return false;
        }

        $role = $this->getState("role");
        if ($role === 'admin') {
            return true; // admin role has access to everything
        }
        // allow access if the operation request is the current user's role
        return ($operation === $role);
    }

    public function getModel() {
        if (is_null($this->_model)) {
            $this->_model = User::model()->findByPk($this->id);
        }

        return $this->_model;
    }

    public function getfullRole() {
        return $this->getState('fullRole');
    }

    public function getRole() {
        return $this->getState('role');
    }

    public function getInfo() {
        if (Yii::app()->user->isGuest) {
            return "{}";
        }

        $attr = $this->model->getAttributes(true, false);
        unset($attr['password']);
        $attr['role'] = $this->role;
        $attr['full_role'] = $this->fullRole;
        return $attr;
    }

}
