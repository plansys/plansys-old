<?php

class Store extends ActiveRecord
{

	public function tableName()
	{
		return 'store';
	}

	public function rules()
	{
		return array(
			array('manager_staff_id, address_id, last_update', 'required'),
			array('manager_staff_id, address_id', 'numerical', 'integerOnly'=>true),
		);
	}

	public function relations()
	{
		return array(
			'customers' => array(self::HAS_MANY, 'Customer', 'store_id'),
			'inventories' => array(self::HAS_MANY, 'Inventory', 'store_id'),
			'staff' => array(self::HAS_MANY, 'Staff', 'store_id'),
			'address' => array(self::BELONGS_TO, 'Address', 'address_id'),
			'managerStaff' => array(self::BELONGS_TO, 'Staff', 'manager_staff_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'store_id' => 'Store',
			'manager_staff_id' => 'Manager Staff',
			'address_id' => 'Address',
			'last_update' => 'Last Update',
		);
	}

}
