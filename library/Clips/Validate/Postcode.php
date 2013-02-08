<?php

class Clips_Validate_Postcode {

	protected static $_instance = NULL;

	protected static $_value = NULL;
	
	protected static $_config = NULL;

	protected function __construct($value, $config = NULL) {

		if ($config) {

			foreach($config as $key => $val) {

				self::$_config[$key] = $val;

			}

		}

		self::$_value = $value;

	}

	public static function factory($value, $config = NULL) {

		if (!isset(self::$_instance)) {

			$c = (__CLASS__);

			self::$_instance = new $c($value, $config);

		} else {
		
			if ($config) {

				foreach($config as $key => $val) {

					self::$_config[$key] = $val;

				}

			}		
			
			self::$_value = $value;
			
		}

		return self::$_instance;

	}

    public static function isValid() {

		$pattern = '/^[0-9]{2}-[0-9]{3}$/';

		if (!preg_match($pattern, self::$_value)) return false;

		return true;


    }

}