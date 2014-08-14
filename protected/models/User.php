<?php

class User extends ActiveRecord {
    
    
    /**
     * @return string the associated database table name
     */
    public function tableName() {
            return 'p_user';
    }

}
