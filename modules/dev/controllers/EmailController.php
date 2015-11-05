<?php
	/**
	* 
	*/
	class EmailController extends Controller
	{

		public function actionIndex($value='')
        {	
        	//var_dump($_POST);
        	//die();

        	if( isset($_POST['DevEmailBuilderIndex']) ){
				 $template = Yii::getPathOfAlias(($path[0] == 'plansys' ? 'application' : 'app') . ".views.layouts.email." . $path[1]) . ".php";


				//$this->renderForm($template);        		
        	}

        	if(  isset($_GET['active'])){
	        	$path = explode('.', $_GET['active']);

	            $filePath = Yii::getPathOfAlias(($path[0] == 'plansys' ? 'application' : 'app') . ".views.layouts.email." . $path[1]) . ".php";	
	            $content = file_get_contents($filePath);


	            Asset::registerJS('application.static.js.lib.ace');
		        $this->renderForm('genemail.DevEmailBuilderIndex', [
		            'content' => $content,
		            'name' => count($path) > 1 ? $path[1] : '',
		            'template' => $path[1]
		        ]);

		     }else{
		         //echo "haha";
		     	$this->renderForm("genemail.DevEmailBuilderIndex");
		     }

			//$this->renderForm("emailbuilder.DevNewEmailBuilder");
        
        }
	

	public function actionNewEmail() {
		$templateName = '';
		$href='';
		
		$default_tag =
"<html>
    <head>
        <title> Template Title </title>
    </head>
    <body>
        <div style='width:70%;height:80%;border:1px solid #bbb;display:block;margin:auto'>
            <div style='width;100%;padding:2% 2%;border-bottom:1px solid #bbb'>
                HEADER
            </div>
            <div style='width:100%;height:70%;padding:2%;display:block'>
                CONTENT
            </div>
            <div style='width;100%;padding:2% 2%;border-top:1px solid #bbb'>
                FOOTER
            </div>
        </div>
    </body>
</html>
		";
		
		
		if(isset($_POST['DevEmailBuilderNew'])){
			$templateName = $_POST['DevEmailBuilderNew']['templateName'];
			$href = Yii::app()->createUrl('/dev/email/index&active=plansys.'.$templateName);

			//create file
			$html = "<html>\n&emsp<head></head>&emsp\n<body></body>\n</html>";
			$path = Yii::getPathOfAlias('application.views.layouts.email.'.$templateName).'.php';
			$file = fopen($path, 'w');
			fwrite($file,$default_tag);
			fclose($file);
			//file_put_contents($path, "<html><head></head><body></body></html>");
		}
        
        $this->renderForm("genemail.DevEmailBuilderNew",  ['href' => $href,
            'template' => $templateName], [ 
            'layout' => '//layouts/blank'
        ]);
    
	    
	}

    public function actionSave()
    {
    	
    	$postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);
        $path     = explode(".", $post['active']);
        $filePath = Yii::getPathOfAlias(($path[0] == 'plansys' ? 'application' : 'app') . ".views.layouts.email." . $path[1]) . ".php";

        if (is_file($filePath)) {
            file_put_contents($filePath, $post['content']);
        }
        
    }

    public function actionPreview(){
        $EmailBuilder = new EmailBuilder();
        echo $EmailBuilder->render($_GET['template']);
    }
    
    public function actionSendemail(){
    	EmailSender::send([
    	    'subject'	=> "Testing email builder",
            'to' 		=> "aufamutawakkil@gmail.com",
            'template'	=> 'email2',
    		'params'    => [
                			'ini' => [
                				'variable' => 'agak',
                				'dalem' => [
                					'sekali' => [1,23,4]
                				]
                			],
            	    		'text'		=> ['header','content','footer'],
            	    		'img'		=> ['manjadda.jpg','manjadda.jpg']
                    		]
                    		
    	]);
        
    }
    
}