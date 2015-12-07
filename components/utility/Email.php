<?php
	class Email extends CComponent {
		private $config = [];
		private static $instance;
		
		public function __construct() {
			$config = Setting::get('email');
		}
		
		public static function send() {
			if (!isset(self::$instance)) {
				self::$instance = new Email();
			}
		}
		
	}