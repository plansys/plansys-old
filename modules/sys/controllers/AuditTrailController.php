<?php

class AuditTrailController extends CController {

    public function actionTrack($t = "view") {
        $postdata = file_get_contents("php://input");
        $path = CJSON::decode($postdata);

        if (!empty($path)) {
            AuditTrail::savePageInfo($path);
            switch ($t) {
                case "create":
                    AuditTrail::track("", "create", $path);
                    break;
                case "update":
                    AuditTrail::track("", "update", $path);
                    break;
                case "delete":
                    AuditTrail::track("", "delete", $path);
                    break;
                default:
                    AuditTrail::track("", "view", $path);
                    break;
            }
        }
    }

}
