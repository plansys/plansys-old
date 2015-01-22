<?php

class AuditTrailController extends CController {

    public function actionTrack() {
        $postdata = file_get_contents("php://input");
        $path = CJSON::decode($postdata);

        if (!empty($path)) {
            AuditTrail::savePageInfo($path);
            AuditTrail::view($path);
        }
    }

}
