<?php

class User extends ActiveRecord {

    public $subscribed = "";

    public function afterFind() {
        parent::afterFind();
        $this->subscribed = Yii::app()->nfy->isSubscribed($this->id);
        return true;
    }

    public function afterSave() {
        parent::afterSave();

		## last insert id hack
		if ($this->isNewRecord) {
			$this->id = Yii::app()->db->getLastInsertID();
		}
	
		Yii::app()->nfy->unsubscribe($this->id, null, true);
		
		if ($this->subscribed === "on" || $this->isNewRecord) {
			$roles = array();

			foreach ($this->roles as $r) {
				$roles[] = "role_" . $r->role_name . ".";
			}
			$category = array_merge(array(
				'uid_' . $this->id,
					), $roles);

			Yii::app()->nfy->subscribe($this->id, $this->username, $category);
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
        return array(
            array('nip, fullname, email, phone, username, password', 'required'),
            array('nip, fullname, email, username, password', 'length', 'max' => 255),
            array('last_login', 'safe')
        );
    }

    public function relations() {
        return array(
            'userInfos' => array(self::HAS_MANY, 'UserInfo', 'user_id'),
            'userRoles' => array(self::HAS_MANY, 'UserRole', 'user_id', 'order' => 'is_default_role ASC'),
            'roles' => array(self::HAS_MANY, 'Role', array('role_id' => 'id'), 'through' => 'userRoles')
        );
    }

    public function tableName() {
        return 'p_user';
    }

}
