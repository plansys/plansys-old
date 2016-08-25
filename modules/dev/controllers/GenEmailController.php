<?php

class GenEmailController extends Controller {
    public function actionTest() {
        $from = [
            'rizky05@gmail.com' => [
                'lokal' => 'lokal-rizky',
                'global' => 'test'
            ], 
            'jon@gmail.com' => [
                'lokal' => 'lokal-jon'
            ]
        ];
        
        Email::send($from, 'app.Test', [
            'global' => 'global'
        ]);
    }

    public function actionPreview($template){
        $eb = EmailBuilder::load($template);
        echo $eb->render(['isPreview' => true]);
    }
    
	public function actionIndex($value='') {
    	if (isset($_GET['active'])){
        	$path = explode('.', $_GET['active']);
            $filePath = Yii::getPathOfAlias(($path[0] == 'plansys' ? 'application' : 'app') . ".views.layouts.email." . $path[1]) . ".php";	
            $content = file_get_contents($filePath);

            Asset::registerJS('application.static.js.lib.ace');
	        $this->renderForm('genemail.DevEmailBuilderIndex', [
	            'content' => $content,
	            'name' => count($path) > 1 ? $path[1] : '',
	            'template' => $path[1]
	        ]);
	     } else {
	     	$this->renderForm("genemail.DevEmailBuilderIndex");
	     }
    }


public function actionNewEmail() {
	$templateName = '';
	$href='';
	
	$default_tag = <<<EOF
<?php 
    if (@\$isPreview) {
        ## Put your preview variable here        
    }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><!-- Put Email Subject Here --></title>
    <style>
        /** Put your style here **/
    </style>
</head>
<body>
    <!-- Put your html here -->
</html>
EOF;
	
	
	if(isset($_POST['DevEmailBuilderNew'])){
		$templateName = $_POST['DevEmailBuilderNew']['templateName'];
		$module = $_POST['DevEmailBuilderNew']['module'];
		$href = Yii::app()->createUrl('/dev/genEmail/index&active='.$module.'.'.$templateName);

		//create file
		$html = "<html>\n&emsp<head></head>&emsp\n<body></body>\n</html>";
		$dir = Yii::getPathOfAlias($module . '.views.layouts.email');
		if (!is_dir($dir)) {
		    mkdir($dir, 0777, true);
		}
		
		$path = $dir . DIRECTORY_SEPARATOR .$templateName .'.php';
		$file = fopen($path, 'w');
		fwrite($file,$default_tag);
		fclose($file);
	}
    
    $this->renderForm("genemail.DevEmailBuilderNew",  [
        'href' => $href,
        'template' => $templateName
        ], [ 
        'layout' => '//layouts/blank'
    ]);

    
}

public function actionSave() {
	$postdata = file_get_contents("php://input");
    $post     = CJSON::decode($postdata);
    $path     = explode(".", $post['active']);
    $filePath = Yii::getPathOfAlias(($path[0] == 'plansys' ? 'application' : 'app') . ".views.layouts.email." . $path[1]) . ".php";

    if (is_file($filePath)) {
        file_put_contents($filePath, $post['content']);
    }
}

public function actionDel($p) {
    $file = Yii::getPathOfAlias($p) . ".php";
    if (is_file($file)) {
        @unlink($file);
    }
}


}