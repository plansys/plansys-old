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
    public function checkAccess($operation, $params = []) {
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

    public function getUseLdap() {
        return !is_null(Setting::get('ldap'));
    }

    public function getfullRole() {
        return $this->getState('fullRole');
    }

    public function getRole() {
        return $this->getState('role');
    }

    public function getHomeUrl() {
        if (isset($this->roleInfo['home_url'])) {
            return $this->roleInfo['home_url'];
        } else {
            return "";
        }
    }

    public function getRoleInfo() {
        foreach ($this->info['roles'] as $k=>$i) {
            if (@$i['role_name'] == $this->fullRole) {
                return $i;
            }
        }
        return null;
    }

    public function getMenuPath() {
        if (isset($this->roleInfo['menu_path'])) {
            return $this->roleInfo['menu_path'];
        } else {
            return "";
        }
    }

    public function getReturnUrl($defaultUrl = null) {
        if (is_null($defaultUrl) && $this->homeUrl != "") {
            $defaultUrl = [$this->homeUrl];
        }

        return parent::getReturnUrl($defaultUrl);
    }

    public function getInfo() {
        if (Yii::app()->user->isGuest) {
            return "{}";
        }

        if (!isset(Yii::app()->session['userinfo'])) {
            $attr = $this->model->getAttributes(true, false);
            unset($attr['password']);
            $attr['role'] = $this->role;
            $attr['roles'] = $this->model->roles;
            $attr['full_role'] = $this->fullRole;
            Yii::app()->session['userinfo'] = $attr;
        } else {
            $attr = Yii::app()->session['userinfo'];
        }
        return $attr;
    }

}
