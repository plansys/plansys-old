<?php
/**
 * Description of JasperException
 *
 * @author Daniel Wendler
 */
namespace Jasper;


class JasperException extends \Exception {
    protected $tomcatMsg;

    public function __construct($message, $code = 600, $tomcatMsg = null) {
        $this->tomcatMsg = $tomcatMsg;
        parent::__construct($message, $code);
    }

    public function __toString() {
        return "JasperException[{$this->code}]: {$this->message}" . PHP_EOL . $this->getTomcatMsg() . PHP_EOL;
    }

    public function getTomcatMsg() {
        if (stripos($this->tomcatMsg, '<b>description</b> <u>') !== false) {
            $tomcatMsg = explode('<b>description</b> <u>', $this->tomcatMsg);
            $tomcatMsg = explode('</u></p>', $tomcatMsg[1]);
            $tomcatMsg = $tomcatMsg[0];
        } else {
            $tomcatMsg = $this->tomcatMsg;
        }
        
        return $tomcatMsg;
    }
}

