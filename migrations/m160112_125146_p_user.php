<?php

class m160112_125146_p_user extends Migration {

    public function up() {
        $this->createTable('p_user', array(
            'id' => 'pk',
            'email' => 'string NOT NULL',
            'username' => 'string NOT NULL',
            'password' => 'string NOT NULL',
            'email' => 'string NOT NULL',
            'last_login' => 'datetime',
            'is_deleted' => 'boolean'
        ));
        $this->addAutoIncrement('p_user', 'id');

        $this->insert('p_user', [
            'email' => "dev@company.com",
            'username' => 'dev',
            'password' => Setting::get('devInstallPassword'),
            'last_login' => null,
            'is_deleted' => 0
        ]);
        
        Setting::remove("devInstallPassword");
    }

    public function down() {
        $this->dropAutoIncrement('p_user', 'id');
        $this->dropTable('p_user');
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
