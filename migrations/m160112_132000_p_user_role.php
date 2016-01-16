<?php

class m160112_132000_p_user_role extends Migration {

    public function up() {
        $this->createTable('p_user_role', array(
            'id' => 'pk',
            'user_id' => 'integer NOT NULL',
            'role_id' => 'integer NOT NULL',
            'is_default_role' => "string DEFAULT 'No'"
        ));

        $this->addForeignKey('p_user_has_p_role', 'p_user_role', 'user_id', 'p_user', 'id');
        $this->addForeignKey('p_role_has_p_user', 'p_user_role', 'role_id', 'p_role', 'id');
        $this->addAutoIncrement('p_user_role', 'id');
        
        $this->insert('p_user_role', [
            'user_id' => 1,
            'role_id' => 1,
            'is_default_role' => 'Yes'
        ]);
    }

    public function down() {
        $this->dropAutoIncrement('p_user_role', 'id');
        $this->dropForeignKey('p_user_has_p_role', 'p_user_role');
        $this->dropForeignKey('p_role_has_p_user', 'p_user_role');
        $this->dropTable('p_user_role');
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
