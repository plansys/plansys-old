<?php

class m160112_130826_p_role extends Migration {

    public function up() {
        $this->createTable('p_role', array(
            'id' => 'pk',
            'role_name' => 'string NOT NULL',
            'role_description' => 'string NOT NULL',
            'menu_path' => 'string',
            'home_url' => 'string',
            'repo_path' => 'string',
        ));
        $this->addAutoIncrement('p_role', 'id');

        $this->insert('p_role', [
            'role_name' => "dev",
            'role_description' => 'IT - Developer',
			'home_url' => '/help/welcome'			
        ]);
    }

    public function down() {
        $this->dropAutoIncrement('p_role', 'id');
        $this->dropTable('p_role');
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
