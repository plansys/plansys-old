<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $id
 * @property string $nip
 * @property string $username
 * @property string $fullname
 * @property string $password
 * @property string $roles
 */
class User extends ActiveRecord {

    protected $nip;
    protected $username;
    protected $fullname;
    protected $password;
    protected $roles;
    
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'p_user';
    }

    public function getRoles_text() {
        return str_replace(array_keys(self::itemAlias('roles')), self::itemAlias('roles'), $this->roles);
    }

    protected function beforeSave() {
        if (strlen($this->password) != 32) {
            $this->password = md5($this->password);
        }
        
        return parent::beforeSave();
    }    
    
    /**
     * @return array of used behaviors
     */
    public function behaviors() {
        $behaviors = array();
        return $behaviors + parent::behaviors();
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'nip' => 'NIP',
            'username' => 'Username',
            'fullname' => 'Nama Lengkap',
            'password' => 'Password',
            'roles' => 'Roles',
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return User the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
