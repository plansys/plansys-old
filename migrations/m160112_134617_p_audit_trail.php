<?php

class m160112_134617_p_audit_trail extends Migration
{
	public function up()
	{
        $this->createTable('p_audit_trail', array(
            'id' => 'pk',
            'type' => 'string',
            'url' => 'text',
            'description' => 'text',
            'pathinfo' => 'text',
            'module' => 'text',
            'ctrl' => 'text',
            'action' => 'text',
            'params' => 'text',
            'data' => 'text',
            'stamp' => 'datetime',
            'user_id' => 'integer',
            'key' => 'string',
            'form_class' => 'string',
            'model_class' => 'string',
            'model_id' => 'integer'
        ));

        $this->addForeignKey('p_audit_trail_has_p_user', 'p_audit_trail', 'user_id', 'p_user', 'id');
        $this->addAutoIncrement('p_audit_trail','id');
	}

	public function down()
	{
        $this->dropAutoIncrement('p_audit_trail','id');
        $this->dropForeignKey('p_audit_trail_has_p_user', 'p_audit_trail');
        $this->dropTable('p_audit_trail');
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}