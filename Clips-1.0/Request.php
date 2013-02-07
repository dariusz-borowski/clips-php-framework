<?php

class Clips_Request {

	private static $_instance = null;

	private static $_config = null;

	public static $_VARS = null;
	public static $_GET = null;
	public static $_POST = null;
	public static $_COOKIE = null;
	public static $_REQUEST = null;
	public static $_FILES = null;
	public static $_SERVER = null;
	public static $_BASE = null;
	public static $_DIR = null;
	public static $_METHOD = null;

	private function __construct($config = NULL) {

		if ($config)
			foreach($config as $key => $value) {

				self::$_config[$key] = $value;

			}

		self::$_VARS = array_merge($_GET, $_POST);
		self::$_REQUEST = $_REQUEST;
		self::$_SERVER = $_SERVER;
		self::$_METHOD = $_SERVER['REQUEST_METHOD'];
		self::$_GET = $_GET;
		self::$_POST = $_POST;
		self::$_FILES = $_FILES;
		self::$_COOKIE = $_COOKIE;
		self::$_BASE = 'http://'.$_SERVER['SERVER_NAME'].substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'], "/")).'/';
		self::$_DIR = substr($_SERVER['SCRIPT_NAME'], 1, strpos($_SERVER['SCRIPT_NAME'], '/', 1));

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