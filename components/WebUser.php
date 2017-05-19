<?php

class WebUser extends CWebUser {

    private $_model = null;

    /**
     * Overrides a Yii method that is used for roles in controllers (accessRules).
     *
     * @param string $operation Name of the operation required (here, a role).
     * @param mixed $params (opt) Parameters for this operation, usually the object to access.
     * @return bool Permission granted?
     */
    public function checkAccess($operation, $params=[]) {
        if (empty($this->id)) {
            // Not identified => no rights
            return false;
        }

        if (Yii::app()->user->isGuest) {
            return false;
        }

        $role = $this->getState('fullRole');
        // allow access if the operation request is the current user's role
        return ($operation === $role);
    }

    protected function changeIdentity($id, $name, $states) {
        @Yii::app()->getSession()->regenerateID(true);
        $this->setId($id);
        $this->setName($name);
        $this->loadIdentityStates($states);
    }
    
    public function getCacheDep() {
        return new CExpressionDependency('true');
    }
    
    public function getModel() {
        if (is_null($this->_model)) {
            $this->_model = User::model()->findByPk($this->id);
        }

        return $this->_model;
    }

    public function getUseLdap() {
        $useLdap = Setting::get('ldap');
        if (is_null($useLdap)) { return false;
        } else {
            return $useLdap['enable'];
        }

    }

    public function getfullRole() {
        return $this->getState('fullRole');
    }

    public function getRole() {
        return $this->getState('role');
    }

    public function getRoleId() {
        return $this->getState('roleId');
    }

    public function getHomeUrl() {
        if (isset($this->roleInfo['home_url'])) {
            return $this->roleInfo['home_url'];
        } else {
            return '';
        }
    }

    public function getRoleInfo() {
        if (is_array($this->info['roles'])) {
            foreach ($this->info['roles'] as $k => $i) {
                if (@$i['role_name'] == $this->fullRole) {
                    return $i;
                }
            }
        }
        return null;
    }

    public function getMenuPath() {
        if (isset($this->roleInfo['menu_path'])) {
            return $this->roleInfo['menu_path'];
        } else {
            return '';
        }
    }

    public function getReturnUrl($defaultUrl=null) {
        if (is_null($defaultUrl) && $this->homeUrl != '') {
            $defaultUrl = [$this->homeUrl];
        }

        return parent::getReturnUrl($defaultUrl);
    }

    public function getInfo() {
        if (Setting::$mode == 'init' || Setting::$mode == 'install') {
            return '{}';
        }

        if (Yii::app()->user->isGuest) {
            return '{}';
        }

        $attr = false;
        if (isset(Yii::app()->session['userinfo'])) {
            $attr = Yii::app()->session['userinfo'];
        }
        $baseSession = '';
        if (isset(Yii::app()->session['baseSession'])) {
            $baseSession = Yii::app()->session['baseSession'];
        }
        
        
        if (!!$attr) {
            if (@$attr['db_name'] != Setting::get('db.dbname')) {
                $attr = false;
            }
        }    
        
        if (!$attr) {
            if (is_null($this->model)) {
                return false;
            }
            
            $attr = $this->model->getAttributes(true, false);
            unset($attr['password']);
            $attr['role']                   = $this->role;
            $attr['roles']                  = $this->model->getRoles();
            $attr['full_role']              = $this->fullRole;
            $attr['db_name']                = Setting::get('db.dbname');
            $attr['session']                = $baseSession;
            Yii::app()->session['userinfo'] = $attr;
        } 

        return $attr;
    }
    
    public function getPositionGroup() {
        $model = ErisPositionGroup::model()->findAll();
        
        return $model;
    }
    
    public function getLastEvent() {
        $criteria = new CDbCriteria();
        $criteria->order = 'tgl_selesai DESC';
        $criteria->limit = '5';
        $model = ErisChannel::model()->findAll($criteria);
        
        return $model;
    }
}
