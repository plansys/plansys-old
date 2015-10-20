<?php

class TestCommand extends CConsoleCommand {

    public function actionTest($name){
        file_put_contents($name.".txt", "Testing");
        sleep(15);
        file_put_contents($name."_again".".txt", "Testing New");
    }

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
