<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {

    private $id;

    public function loggedIn($record) {

        ## set id and role
        $this->id = $record->id;

        ## set role
        $role = UserRole::model()->findByAttributes([
            'user_id'         => $this->id,
            'is_default_role' => 'Yes'
        ]);
        if (!!$role) {
            $this->setState('fullRole', $role->role['role_name']);
            $rootRole = Helper::explodeFirst(".", $role->role['role_name']);
            $this->setState('role', $rootRole);
            $this->setState('roleId', $role->id);
    
            ## reset error code
            $this->errorCode = self::ERROR_NONE;
        }
    }

    public function authenticate() {
        $record = User::model()->findByAttributes(['username' => $this->username]);

        $useLdap = false;
        if (!is_null($record) && $record->password == '' && Yii::app()->user->useLdap) {
            $useLdap = true;
            $ldapSuccess = Yii::app()->ldap->authenticate($this->username, $this->password);
            if ($ldapSuccess) {
                $record->password = Helper::hash($this->password);
                $record->save();

                $this->loggedIn($record);
                return true;
            }
        }

        if ($record === null)
            $this->errorCode = self::ERROR_USERNAME_INVALID;

        else if (!password_verify($this->password, $record->password)) {
            if ($useLdap) {
                $ldapSuccess = Yii::app()->ldap->authenticate($this->username, $this->password);
                if ($ldapSuccess) {
                    $record->password = Helper::hash($this->password);
                    $record->save();

                    $this->loggedIn($record);
                    return true;
                } else {
                    $this->errorCode = self::ERROR_PASSWORD_INVALID;
                }
            } else {
                $this->errorCode = self::ERROR_PASSWORD_INVALID;
            }
        } else {
            $this->loggedIn($record);
        }
        return !$this->errorCode;
    }

    public function getId() {
        return $this->id;
    }

}
