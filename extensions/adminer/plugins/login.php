<?php

class AdminerLogin {

    function credentials() {
        if (!empty(@$_GET)) {

            if (@$_GET['p']) {
                $_SESSION['db_password'] = @$_GET['p'];
            }
            return [@$_GET['s'], @$_GET['username'], $_SESSION['db_password'], @$_GET['db']];
        } else {
            return null;
        }
    }

}
