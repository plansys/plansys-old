<?php

class PhpProcess extends CComponent {

    private static function checkPhp() {
        if (substr(php_uname(), 0, 7) == "Windows") {
            exec("plansys\commands\shell\psexec.exe -accepteula -d php -v", $output, $input);

            if ($input < 100) {
                throw new CException("PHP Command not found " . $input);
            }
        } else {
            $returnVal = shell_exec("which php");
            if (empty($returnVal)) {
                throw new CException("PHP Command not found");
            }
        }
    }

    public static function yiic($cmd = "") {
        PhpProcess::checkPhp();

        if (substr(php_uname(), 0, 7) == "Windows") {
            exec("plansys\commands\shell\psexec.exe -accepteula -d php  plansys\yiic.php " . $cmd, $output, $input);
        } else {
            $pid = exec("php plansys/yiic " . $cmd . " > /dev/null 2>&1 & echo $!;", $output, $input);
        }
        return implode("\n", $output);
    }

    public static function start($jsfile, $params = "") {
        $cmd = realpath(Yii::getPathOfAlias('webroot') . '/' . trim($jsfile, '/'));

        if (is_file($cmd)) {
            PhpProcess::checkPhp();

            if (substr(php_uname(), 0, 7) == "Windows") {
                exec("plansys\commands\shell\psexec.exe -accepteula -d php " . $cmd . " " . $params, $output, $input);
                return $input;
            } else {
                $pid = exec("php " . $cmd . " " . $params . " > /dev/null 2>&1 & echo $!;", $output, $input);
                return $pid;
            }
        } else {
            throw new CException("File Not Found " . $cmd);
        }
    }

    public static function isRunning($pid = 0) {
        if (is_numeric($pid)) {
            if (substr(php_uname(), 0, 7) == "Windows") {
                exec("plansys\commands\shell\pslist.exe -accepteula " . $pid, $output, $input);
                return $input == 0;
            } else {
                return (file_exists("/proc/$pid"));
            }
        }
    }

    public static function stop($pid) {
        if ($pid) {
            if (substr(php_uname(), 0, 7) == "Windows") {
                exec("plansys\commands\shell\pskill.exe -accepteula " . $pid, $output, $input);
                return $input;
            } else {
                exec("kill -9 $pid");
            }
        }
    }

}
