<?php
use \InlineStyle\InlineStyle;

class EmailBuilder extends CComponent {
	public $template;
	private $renderCache = "";
	
	public function url($url) {
		return Setting::get('app.url'). '/' . trim($url,'/');
	}
	
	public function getUrl() {
		return $this->url('');
	}
	
	public function render($params=[]) {
        $path = explode('.', $this->template);
        
		$filePath = Yii::getPathOfAlias($this->template) . '.php';
        if (count($path) == 2) {
			$filePath = Yii::getPathOfAlias(($path[0] == 'plansys' ? 'application' : 'app') . ".views.layouts.email." . $path[1]) . ".php";
        }
    	
    	if (!is_file($filePath)) {
    		throw new CException('File `' . $filePath . '` not found');
    	}
    	
    	extract($params);
    	
		ob_start();
		include($filePath);
		$result = ob_get_clean();
		
		$htmldoc = new InlineStyle($result);
		$htmldoc->applyStylesheet($htmldoc->extractStylesheets());
		return $this->renderCache = $htmldoc->getHTML();
	}
	
	public function getSubject() {
		return trim(Helper::getStringBetween($this->renderCache, "<title>", "</title>"));
	}
	
	public static function load($template) {
		$eb = new EmailBuilder;
		$eb->template = $template;
		return $eb;
	}
}
?>