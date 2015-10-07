<?php

class User extends ActiveRecord {

    public $subscribed = "";
    public $useLdap = false;
    public $subscriptionCategories = 'EMPTY';

    public function querySubCats() {
        if (!$this->isNewRecord) {
            ## get subscriptions
            $sql = "select category from p_nfy_subscription_categories c "
                    . " inner join p_nfy_subscriptions p on c.subscription_id = p.id "
                    . " where p.subscriber_id = " . $this->id;
            $subscriptions = Yii::app()->db->createCommand($sql)->queryColumn();

            $querySubCats = [];
            foreach ($subscriptions as $s) {
                if (strpos($s, "role_") === 0) {
                    $querySubCats[] = substr($s, 5, strlen($s) - 6);
                }
            }

            return $querySubCats;
        }
        return [];
    }

    public function afterFind() {
        parent::afterFind();
        $this->subscribed = (Yii::app()->nfy->isSubscribed($this->id) ? "on" : "");
        $this->useLdap = Yii::app()->user->useLdap;
        $this->getRoles();
        return true;
    }

    public function afterSave() {
        
        ## get assign user id to user roles
        $ur = $this->userRoles;
        foreach ($ur as $k => $u) {
            $ur[$k]['user_id'] = $this->id;
        }
        $olduser = ActiveRecord::toArray($this->getRelated('userRoles'));
        
        ActiveRecord::batch('UserRole', $ur, $olduser);

        ## re-subscribe user to notification
        if (!$this->isNewRecord) {
            Yii::app()->nfy->unsubscribe($this->id, null, true);
        }
        if ($this->subscribed === "on" || $this->subscribed === "ON" || $this->isNewRecord) {
            $roles = array();
            $sql = 'select DISTINCT role_name from p_user_role p '
                    . ' inner join p_role r on p.role_id = r.id '
                    . ' and p.user_id = ' . $this->id;
            $db = Yii::app()->db->createCommand($sql)->queryAll();

            foreach ($db as $r) {
                if ($this->subscriptionCategories === 'EMPTY' || in_array($r['role_name'], $this->subscriptionCategories)) {
                    $roles[] = "role_" . $r['role_name'] . ".";
                }
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

    public function getSubscription() {
        if (!Yii::app()->session['subscriber_id']) {
            $sql = 'select * from p_nfy_subscriptions where subscriber_id = ' . $this->id;
            Yii::app()->session['subscriber_id'] = Yii::app()->db->createCommand($sql)->queryRow();
        }

        return Yii::app()->session['subscriber_id'];
    }

    public function relations() {
        return array(
            'auditTrail' => array(self::HAS_MANY, 'AuditTrail', 'user_id'),
            'userRoles' => array(self::HAS_MANY, 'UserRole', 'user_id', 'order' => 'is_default_role ASC'),
            'role' => array(self::HAS_ONE, 'Role', array('role_id' => 'id'), 'through' => 'userRoles',
                'condition' => 'is_default_role = "Yes"'),
        );
    }

    public $roles = [''];

    public function getRoles($originalSorting = false) {

        $uid = $this->id;
        if (!$uid) {
            return [$this->role];
        }

        $subs = $this->querySubCats();

        ## get roles
        $sql = "select *,r.id from p_role r"
                . " inner join p_user_role p on r.id = p.role_id "
                . " where p.user_id = {$uid} order by is_default_role asc";
        $roles = Yii::app()->db->createCommand($sql)->queryAll();

        if (empty($roles)) {
            return false;
        }
        $idx = 0;
        foreach ($roles as $k => $role) {
            ## find current role index
            if ($role['role_name'] == Yii::app()->user->getState('role') && $idx == 0) {
                $idx = $k;
            }

            ## assign subscriptions to array,
            if (in_array($role['role_name'], $subs)) {
                $roles[$k]['subscribed'] = true;
            } else {
                $roles[$k]['subscribed'] = false;
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