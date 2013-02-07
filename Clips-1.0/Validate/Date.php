<?php

class Clips_Validate_Date implements Clips_Validate_Interface {

	protected static $_instance = NULL;

	protected static $_value = NULL;
	
	protected static $_config = array(
	
		'format' => 'Y-m-d',
		
	);

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

		if (self::$_value == '' || !is_string(self::$_value)) return false;
	
		return date(self::$_config['format'], strtotime(self::$_value)) == trim(self::$_value);
		
    }

}