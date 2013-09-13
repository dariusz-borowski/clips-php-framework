<?php

class Clips_Validate_Mail {

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

		return filter_var(self::$_value, FILTER_VALIDATE_EMAIL);

    }

}
