<?php

/**
 * This is the model class for table "p_role".
 *
 * The followings are the available columns in table 'p_role':
 * @property integer $id
 * @property string $role_name
 * @property string $role_description
 */
class Role extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'p_role';
	}
    
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('role_name, role_description', 'required'),
			array('role_name, role_description', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, role_name, role_description', 'safe', 'on'=>'search'),
        );
	}
    
	/**
	 * @return array of used behaviors
	 */
	public function behaviors() {
		$behaviors = array(
		);
		return $behaviors + parent::behaviors();
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'role_name' => 'Role Name',
			'role_description' => 'Role Description',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('role_name',$this->role_name,true);
		$criteria->compare('role_description',$this->role_description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

}