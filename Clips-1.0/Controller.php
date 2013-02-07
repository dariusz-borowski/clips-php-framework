<?php

class Clips_Controller {

	private static $_instance = NULL;

	protected static $_config = NULL;
	
	public function __construct($config = NULL) {

		if ($config)
			foreach($config as $key => $value) {

				self::$_config[$key] = $value;

			}
		
	}
	
	public static function factory($config = NULL) {

		if (!isset(self::$_instance)) {

			$c = __CLASS__;
			self::$_instance = new $c($config);

		} else {

			if ($config)
				foreach($config as $key => $value) {

					self::$_config[$key] = $value;

				}

		}

		return self::$_instance;

	}

}

?>