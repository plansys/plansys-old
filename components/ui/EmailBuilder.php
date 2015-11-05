<?php
	/**
	* 
	*/
	class EmailBuilder extends CComponent
	{
		public static $text = array();		//text from template
		public static $imgs = array();		//image from template
		public static $img = array();		//image from template
		
		public static function render($template,$params=null)
		{
		
			if( $params != null ){
				extract($params);	
			}
			$pathRender = Yii::getPathOfAlias("application.views.layouts.email." .$template).'.php';
			
			ob_start();
			include($pathRender);
			$result = ob_get_clean();

			return $result;
		}

		public static function img($file){
			$ext = explode(".",$file);
			$pathImg = Yii::getPathOfAlias("application.views.layouts.email.images.".$ext[0]).'.'.end($ext);
			$data = file_get_contents($pathImg);
			$base64 = 'data:image/' . end($ext) . ';base64,' . base64_encode($data);
			return $base64;
		}
			
	}
?>