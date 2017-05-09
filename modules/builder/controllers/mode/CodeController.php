<?php

use gossi\formatter\Formatter;

class CodeController extends Controller
{
    public $enableCsrf = false;

    public function actionIndex($f)
    {
        if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
            header('Content-Encoding: gzip');
            ob_start('ob_gzhandler');
        }
        echo FileManager::read($f);
    }

    public function actionFormat()
    {
        // $post = file_get_contents('php://input');
        // $formatter = new Formatter();
        // echo $formatter->format($post);
    }

    public function actionSave($f)
    {
        $post = json_decode(file_get_contents('php://input'), true);
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            echo $errstr;
        });

        try {
            FileManager::write($f, $post['content']);
            echo '1';
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }
}
