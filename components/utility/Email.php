<?php
class Email extends CComponent {
	private $config = [];
	private static $instance;
	private $validator = null;
	
	public function __construct() {
		$this->config = Setting::get('email');
		$this->validator = new CEmailValidator();
	}
	
	public static function preview($from, $template, $params = [], $options = []) {
		if (!isset(self::$instance)) {
			self::$instance = new Email();
		}
		
		if (is_string($from)) {
			$from = [$from];
		}
		array_walk_recursive($params, function (&$value) {
		    if (is_string($value)) {
		    	$value = htmlentities($value);
		    }
		});
		
		$currentUserId = null;
		if (isset(Yii::app()->user)) {
			$currentUserId = Yii::app()->user->id; 
		}
		$eb = EmailBuilder::load($template);
		$mails = [];
		foreach ($from as $key=>$value) {
			$email = $key;
			if (is_numeric($key)) {
				$email = $value;
			}
			
			## merge local parameters
			if (is_array($value)) {
				$params = array_merge($params, $value);
			}
			$params['to'] = $email;
			
			if (!self::$instance->validator->validateValue($email)) {
				return "Failed to render email..";
			}
			
			return $eb->render($params);
		}
	}
	
	public static function send($from, $template, $params = [], $options = []) {
		if (!isset(self::$instance)) {
			self::$instance = new Email();
		}
		
		if (is_string($from)) {
			$from = [$from];
		}
		array_walk_recursive($params, function (&$value) {
		    if (is_string($value)) {
		    	$value = htmlentities($value);
		    }
		});

		$currentUserId = null;
		if (isset(Yii::app()->user)) {
			$currentUserId = Yii::app()->user->id; 
		}
		$eb = EmailBuilder::load($template);
		$mails = [];
		foreach ($from as $key=>$value) {
			$email = $key;
			if (is_numeric($key)) {
				$email = $value;
			}
			
			## merge local parameters
			if (is_array($value)) {
				$params = array_merge($params, $value);
			}
			$params['to'] = $email;
			$params['isPreview'] = false;
			
			if (!self::$instance->validator->validateValue($email)) {
				## when email is not valid, fails silently...
				continue;
			}
			
			$html = $eb->render($params);
			$mails[] = [
				'subject' => $eb->subject,
				'body' => $html,
				'to' => $email
			];
		}
		
		ServiceManager::run('SendEmail', ['mails'=>$mails]);
	}
}