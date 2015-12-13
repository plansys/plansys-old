<?php

class Rental extends ActiveRecord
{

	public function tableName()
	{
		return 'rental';
	}

	public function rules()
	{
		return array(
			array('rental_date, inventory_id, customer_id, staff_id, last_update', 'required'),
			array('inventory_id, customer_id, staff_id', 'numerical', 'integerOnly'=>true),
			array('return_date', 'safe'),
		);
	}

	public function relations()
	{
		return array(
			'payments' => array(self::HAS_MANY, 'Payment', 'rental_id'),
			'customer' => array(self::BELONGS_TO, 'Customer', 'customer_id'),
			'inventory' => array(self::BELONGS_TO, 'Inventory', 'inventory_id'),
			'staff' => array(self::BELONGS_TO, 'Staff', 'staff_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'rental_id' => 'Rental',
			'rental_date' => 'Rental Date',
			'inventory_id' => 'Inventory',
			'customer_id' => 'Customer',
			'return_date' => 'Return Date',
			'staff_id' => 'Staff',
			'last_update' => 'Last Update',
		);
	}

}
