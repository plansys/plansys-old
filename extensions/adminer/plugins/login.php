<?php

class AdminerLogin {

    function credentials() {
        if (!empty(@$_GET)) {

            if (@$_GET['p']) {
                $_SESSION['db_password'] = @$_GET['p'];
            }

            if (@$_GET['s']) {
                $_SESSION['db_host'] = @$_GET['s'];
            }

            if (!isset($_SESSION['db_password']) || !isset($_SESSION['db_host'])) {
                return null;
            }


            return [$_SESSION['db_host'], @$_GET['username'], $_SESSION['db_password'], @$_GET['db']];
        } else {
            return null;
        }
    }

}
