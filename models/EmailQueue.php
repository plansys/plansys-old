<?php

class EmailQueue extends ActiveRecord
{

	public function tableName()
	{
		return 'p_email_queue';
	}

	public function rules()
	{
		return array(
			array('user_id, status', 'numerical', 'integerOnly'=>true),
			array('email, subject, template', 'length', 'max'=>255),
			array('content, body', 'safe'),
		);
	}

	public function relations()
	{
		return array(
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'email' => 'Email',
			'subject' => 'Subject',
			'content' => 'Content',
			'body' => 'Body',
			'template' => 'Template',
			'status' => 'Status',
		);
	}

}
