<?php

class TestCommand extends CConsoleCommand {

    public function actionHello($name = null) {
    	if(isset($name)){
    		echo "Hello ".$name." !!!\n";
    	}else{
    		echo "Hello world !!!\n";
    	}
    }

    public function actionIndex(){
    	echo "Running test command is completed\n";
    }

}
