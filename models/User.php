<?php

class User extends ActiveRecord {

    public $subscribed = "";
    public $useLdap = false;

    public function afterFind() {
        parent::afterFind();
        $this->subscribed = (Yii::app()->nfy->isSubscribed($this->id) ? "on" : "");
        $this->useLdap = Yii::app()->user->useLdap;
        $this->getRoles();
        return true;
    }

    public function afterSave() {
        parent::afterSave();
        $ur = $this->userRoles;
        foreach ($ur as $k => $u) {
            $ur[$k]['user_id'] = $this->id;
        }
        ActiveRecord::batch('UserRole', $ur);

        if (!$this->isNewRecord) {
            Yii::app()->nfy->unsubscribe($this->id, null, true);
        }

        if ($this->subscribed === "on" || $this->isNewRecord) {
            $roles = array();

            $db = Yii::app()->db->createCommand('select DISTINCT role_name from p_user_role p inner join p_role r on p.role_id = r.id and p.user_id = ' . $this->id)->queryAll();
            
            foreach ($db as $r) {
                $roles[] = "role_" . $r['role_name'] . ".";
            }

            $category = array_merge(array(
                'uid_' . $this->id,
                ), $roles);


            
            Yii::app()->nfy->subscribe($this->id, $this->username, $category);
            $this->subscribed = true;
        } else {
            $this->subscribed = false;
        }
        return true;
    }

    public function afterDelete() {
        $result = parent::afterDelete();
        if ($result) {
            Yii::app()->nfy->unsubscribe($this->id, null, true);
        }
        return $result;
    }

    public function rules() {
        $passwordReq = ', password, email, phone, nip';
        if ($this->useLdap) {
            $passwordReq = '';
        }


        return array(
            array(' fullname,  username' . $passwordReq, 'required'),
            array('username', 'unique'),
            array('email', 'email'),
            array('last_login', 'safe')
        );
    }

    public function getSubscription() {

        if (!Yii::app()->session['subscriber_id']) {
            $sql = 'select * from p_nfy_subscriptions where subscriber_id = ' . $this->id;
            Yii::app()->session['subscriber_id'] = Yii::app()->db->createCommand($sql)->queryRow();
        }

        return Yii::app()->session['subscriber_id'];
    }

    public function relations() {
        return array(
            'userInfos' => array(self::HAS_MANY, 'UserInfo', 'user_id'),
            'userRoles' => array(self::HAS_MANY, 'UserRole', 'user_id', 'order' => 'is_default_role ASC'),
            'role' => array(self::HAS_ONE, 'Role', array('role_id' => 'id'), 'through' => 'userRoles',
                'condition' => 'is_default_role = "Yes"'),
        );
    }

    public $roles = ['a'];
    
    public function getRoles($originalSorting = false) {
        $uid = Yii::app()->user->id;
        if (!$uid) {
            return [$this->role];
        }
        
        $sql = "select *,r.id from p_role r inner join p_user_role p on r.id = p.role_id where p.user_id = {$uid} order by is_default_role asc";
        $roles = Yii::app()->db->createCommand($sql)->queryAll();
        if ($originalSorting) {
            return $roles;
        }
        
        $idx = 0;
        foreach ($roles as $k => $role) {
            if ($role['role_name'] == Yii::app()->user->getState('role')) {
                $idx = $k;
                break;
            }
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
