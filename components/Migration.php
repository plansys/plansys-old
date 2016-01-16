<?php

class Migration extends CDbMigration {
    public function addAutoIncrement($table_name, $col) {
        $driver = $this->dbConnection->driverName;
        if($driver == 'mysql') {
            $createAutoincrement = <<< SQL
ALTER TABLE `$table_name`
CHANGE `{$col}` `{$col}` int(11) NOT NULL AUTO_INCREMENT;
SQL;
            
            $this->execute($createAutoincrement);
        } else if($driver == 'oci') {
            $createSequenceSql = <<< SQL
create sequence {$table_name}_{$col}_SEQ 
start with 1 
increment by 1 
nomaxvalue
nocache
SQL;
            $createTriggerSql = <<< SQL
create or replace trigger {$table_name}_{$col}_SEQ_TRIGGER
before insert on "{$table_name}"
for each row
begin
select {$table_name}_{$col}_SEQ.nextval into :new."{$col}" from dual;
end;
SQL;
            $this->execute($createSequenceSql);
            $this->execute($createTriggerSql);
        }
    }

    public function dropAutoIncrement($table_name, $col) {
        $driver = $this->dbConnection->driverName;
        if($driver == 'oci') {
            // trigger needs to be dropped before the table or it gets "kindof" dropped with the table drop
            $this->execute("DROP SEQUENCE {$table_name}_{$col}_SEQ");
            $this->execute("DROP TRIGGER {$table_name}_{$col}_SEQ_TRIGGER");
        }
    }
}