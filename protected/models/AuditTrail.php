<?php

/**
 * This is the model class for table "audit_trail".
 */
class AuditTrail extends ActiveRecord {
    /**
     * The followings are the available columns in table 'tbl_audit_trail':
     * @var integer $id
     * @var string $new_value
     * @var string $old_value
     * @var string $action
     * @var string $model
     * @var string $field
     * @var string $stamp
     * @var integer $user_id
     * @var string $model_id
     */
    
    protected $new_value;
    protected $old_value;
    protected $action;
    protected $model;
    protected $field;
    protected $stamp;
    protected $user_id;
    protected $model_id;
    
    protected $user;
    

    public function getAction_label() {
        $r = "";
        switch ($this->action) {
            case "CREATE":
                $r = '<span class="label-audit label label-green">CREATE</span>';
                break;
            case "SET":
                $r = '<span class="label-audit label label-blue">UPDATE</span>';
                break;
            case "DELETE":
                $r = '<span class="label-audit label label-red">DELETE</span>';
                break;
        }
        return $r;
    }

    /**
     * @return array of used behaviors
     */
    public function behaviors() {
        $behaviors = array(
            'RelatedSearchBehavior' => array(
                'class' => 'RelatedSearchBehavior',
                'relations' => array(
                    'user_search' => 'user.fullname',
                    'role_search' => 'user.roles',
                )
            ),
        );
        return $behaviors + parent::behaviors();
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        if (isset(Yii::app()->params['AuditTrail']) && isset(Yii::app()->params['AuditTrail']['table']))
            return Yii::app()->params['AuditTrail']['table'];
        else
            return 'p_audit_trail';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('action, model, stamp, model_id', 'required'),
            array('action', 'length', 'max' => 255),
            array('model', 'length', 'max' => 255),
            array('field', 'length', 'max' => 255),
            array('model_id', 'length', 'max' => 255),
            array('user_id', 'length', 'max' => 255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, new_value, old_value, action, model, field, stamp, user_id, model_id', 'safe', 'on' => 'search'),
            array('user_search, role_search', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'old_value' => 'Old Value',
            'new_value' => 'New Value',
            'action' => 'Action',
            'model' => 'Type',
            'field' => 'Field',
            'stamp' => 'Stamp',
            'user_id' => 'User',
            'model_id' => 'ID',
        );
    }

    function getParent() {
        $model_name = $this->model;
        return $model_name::model();
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search($options = array()) {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.
        $criteria = new CDbCriteria;
        $criteria->group = "action,model_id";
        $criteria->compare('id', $this->id);
        $criteria->compare('old_value', $this->old_value, true);
        $criteria->compare('new_value', $this->new_value, true);
        $criteria->compare('action', $this->action, true);
        $criteria->compare('model', $this->model);
        $criteria->compare('field', $this->field, true);
        $criteria->compare('stamp', $this->stamp, true);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('model_id', $this->model_id);
        $criteria->mergeWith($this->getDbCriteria());

        $options['sort'] = array(
            'defaultOrder' => 'id DESC',
        );

        return $this->relatedSearch($criteria, $options);
    }
    
    public function search_detail($options = array()) {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.
        $criteria = new CDbCriteria;
        $criteria->compare('id', $this->id);
        $criteria->compare('old_value', $this->old_value, true);
        $criteria->compare('new_value', $this->new_value, true);
        $criteria->compare('action', $this->action, true);
        $criteria->compare('model', $this->model);
        $criteria->compare('field', $this->field, true);
        $criteria->compare('stamp', $this->stamp, true);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('model_id', $this->model_id);
        $criteria->mergeWith($this->getDbCriteria());

        $options['sort'] = array(
            'defaultOrder' => 'id DESC',
        );

        return $this->relatedSearch($criteria, $options);
    }

    public function scopes() {
        return array(
            'recently' => array(
                'order' => ' t.stamp DESC ',
            ),
        );
    }
    
}
