<?php

class AuditTrail extends ActiveRecord {

    public function getAction_label() {
        $r = "";
        switch ($this->action) {
            case "CREATE":
                $r = '<span class="label-audit label label-green">CREATE</span>';
                break;
            case "SET":
                $r = '<span class="label-audit label label-blue">UPDATE</span>';
                break;
            case "DELETE":
                $r = '<span class="label-audit label label-red">DELETE</span>';
                break;
        }
        return $r;
    }

    public function tableName() {
        return "p_audit_trail";
    }

}
