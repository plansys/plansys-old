<?php

class Customer extends ActiveRecord
{

	public function tableName()
	{
		return 'customer';
	}

	public function rules()
	{
		return array(
			array('store_id, first_name, last_name, address_id, create_date', 'required'),
			array('store_id, address_id, active', 'numerical', 'integerOnly'=>true),
			array('first_name, last_name', 'length', 'max'=>45),
			array('email', 'length', 'max'=>50),
			array('last_update', 'safe'),
		);
	}

	public function relations()
	{
		return array(
			'address' => array(self::BELONGS_TO, 'Address', 'address_id'),
			'store' => array(self::BELONGS_TO, 'Store', 'store_id'),
			'payments' => array(self::HAS_MANY, 'Payment', 'customer_id'),
			'rentals' => array(self::HAS_MANY, 'Rental', 'customer_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'customer_id' => 'Customer',
			'store_id' => 'Store',
			'first_name' => 'First Name',
			'last_name' => 'Last Name',
			'email' => 'Email',
			'address_id' => 'Address',
			'active' => 'Active',
			'create_date' => 'Create Date',
			'last_update' => 'Last Update',
		);
	}

}
