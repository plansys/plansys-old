<?php

class User extends ActiveRecord {

    public $useLdap = false;

    public function afterFind() {
        parent::afterFind();
        if (get_class(Yii::app()) != "CConsoleApplication") {
            $this->useLdap = Yii::app()->user->useLdap;
            $this->getRoles();
        }
        return true;
    }

    public function afterSave() {
        parent::doAfterSave(false);

        ## get assign user id to user roles
        $ur = $this->userRoles;
        foreach ($ur as $k => $u) {
            $ur[$k]['user_id'] = $this->id;
        }
        $olduser = ActiveRecord::toArray($this->getRelated('userRoles'));
        ActiveRecord::batch('UserRole', $ur, $olduser);

        return true;
    }

    public function rules() {
        $passwordReq = ', password, email';
        if ($this->useLdap) {
            $passwordReq = '';
        }

        return array(
            array('username' . $passwordReq, 'required'),
            array('username', 'unique'),
            array('email', 'email'),
            array('last_login', 'safe')
        );
    }

    public function relations() {
        return array(
            'auditTrail' => array(self::HAS_MANY, 'AuditTrail', 'user_id'),
            'userRoles' => array(self::HAS_MANY, 'UserRole', 'user_id', 'order' => '|is_default_role| DESC'),
            'role' => array(self::HAS_ONE, 'Role', array('role_id' => 'id'), 'through' => 'userRoles',
                'condition' => '|is_default_role| = \'Yes\''),
        );
    }
    
    public $roles = [''];
    public function getRoles($originalSorting = false) {
        $uid = $this->id;
        if (!$uid) {
            return [$this->role];
        }

        ## get roles
        $roles = Role::model()->with('userRoles')->findAll(ActiveRecord::formatCriteria([
            'condition' => '|user_id| = :p',
            'order' => '|is_default_role| DESC',
            'params' => [
                ':p' => $uid
            ]
        ]));
        
        $roles = ActiveRecord::toArray($roles);
        if (empty($roles)) {
            return false;
        }
        $idx = 0;
        foreach ($roles as $k => $role) {
            ## find current role index
            if ($role['role_name'] == Yii::app()->user->getState('role') && $idx == 0) {
                $idx = $k;
            }
        }
        
        if ($originalSorting) {
            return $roles;
        }

        $role = array_splice($roles, $idx, 1);
        array_unshift($roles, $role[0]);

        $this->roles = $roles;
        return $roles;
    }

    public function tableName() {
        return 'p_user';
    }

}
