<?php

class Clips_Validate_Nip {

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

		// zmiana Jacek 19.07.2011
		//$pattern = '/^[A-Z]{2}[0-9]{10}$/i';

		$pattern = '/^[0-9]{10}$/i';
		
		$str = str_replace("-","",self::$_value);

		if (!preg_match($pattern, $str)) return false;

		if (strlen($str) != 10) 	return false;

		$arrSteps = array(6, 5, 7, 2, 3, 4, 5, 6, 7);

		//$str = self::$_value;

		$intSum = 0;

		for ($i = 0; $i < 9; $i++) {
			$intSum += $arrSteps[$i] * $str[$i];
		}

		$int = $intSum % 11;

		$intControlNr = ($int == 10) ? 0 : $int;

		if ($intControlNr == $str[9]) {

			return true;

		}

		return false;

    }

}

?>