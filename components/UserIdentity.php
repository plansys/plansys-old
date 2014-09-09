<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {

    private $id;

    public function authenticate() {
        $record = User::model()->findByAttributes(array('username' => $this->username));
        
        if ($record === null)
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        else if (trim($record->password) !== md5($this->password))
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        else {
            $this->id = $record->id;
            $role = UserRole::model()->findByAttributes(array('user_id' => $this->id, 'default_role' => '1'));
            $roles = $role->role->role_name;
            $this->setState('roles', $roles);
            $this->errorCode = self::ERROR_NONE;
        }
        return !$this->errorCode;
    }

    public function getId() {
        return $this->id;
    }

}