<?php

if (!class_exists('PHP_CodeSniffer\Config', false)) {
    $pcs_autoload = Yii::getPathOfAlias('plansys.vendor.squizlabs.php_codesniffer.autoload') . ".php"; 
    require($pcs_autoload);
}
use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Ruleset;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Util\Tokens;
use PhpParser\ParserFactory;

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

    public function format($content) {
        ini_set('xdebug.max_nesting_level', 3000);
        
        if (!defined('PHP_CODESNIFFER_CBF')) {
            define('PHP_CODESNIFFER_CBF', true);
        }
        if (!defined('PHP_CODESNIFFER_VERBOSITY')) {
            define('PHP_CODESNIFFER_VERBOSITY', false);
        }
        $config = new Config(['dummy'], false);
        $config->standards = [dirname(__FILE__) . DIRECTORY_SEPARATOR . 'wp.xml'];
        // $config->standards = ['Squiz'];
        spl_autoload_call(Tokens::class);
        
        $file = new DummyFile($content, new Ruleset($config), $config);
        $file->process();
        $fixed = $file->fixer->fixFile();
        
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP5);
        try {
            $stmts = $parser->parse($content);
        } catch (Exception $e) {
            http_response_code (503);
            echo $e->getMessage();
            die();
        }
        $new = $file->fixer->getContents();
        return $new;
    }

    public function actionFormat()
    {   
        $content = file_get_contents('php://input');
        echo $this->format($content);
    }

    public function actionSave($f)
    {
        $post = json_decode(file_get_contents('php://input'), true);
        
        if (Helper::endsWith($f, ".php")) {
            $post['content'] = $this->format($post['content']);
        }
        FileManager::write($f, $post['content']);
        
        if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
            header('Content-Encoding: gzip');
            ob_start('ob_gzhandler');
        }
        echo $post['content'];
    }
}
