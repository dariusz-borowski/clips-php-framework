<?php

class Clips_Session {

	const DEFAULT_PREFIX = 'Clips_';
	
	private $_namespace = NULL;
	
	static private $_handler = NULL;
	
	static private $_session_started = false;

	static private $_config = array(
	
		'maxlifetime' => '60',
		'name' => NULL,
	
	);
	
	public function __construct($namespace = NULL) {
	
		$this -> _namespace = $namespace;
	
	}
	
	public static function getId() {
	
		return session_id();
	
	}
	
	public static function isSessionStarted() {
	
		return self::$_session_started;
	
	}
	
	public static function start($config = NULL) {
		
		if ($config) {
		
			foreach($config as $key => $value) {
			
				self::$_config[$key] = $value;
				
			}
			
		}

		if (isset(self::$_config['name']))
			session_name(self::$_config['name']);
		
		if (isset(self::$_config['maxlifetime']))
			session_cache_expire(self::$_config['maxlifetime']);		
		
		session_start();

		self::$_session_started = true;
	
	}
	
	public function destroy() {
	
		unset($_SESSION[self::DEFAULT_PREFIX][$this -> _namespace]);
	
	}
	
	public static function kill() {
	
		$_SESSION = array();
		session_destroy();
	
	}
	
	
	public static function regenerateId() {
		
		session_regenerate_id();
	
	}	

	
	public static function getHandler() {
	
		return self::$_handler;	
	
	}
	
    public static function setHandler(Clips_Session_Handler_Interface $handler) {
	
        $result = session_set_save_handler(array(&$handler,'open'),array(&$handler, 'close'), array(&$handler, 'read'), array(&$handler, 'write'), array(&$handler, 'destroy'), array(&$handler, 'gc'));

        self::$_handler = $handler;
		
    }

	
	public function &__get($key) {
	
		if (!self::$_session_started)
			throw new Exception('session not started, call Clips_Session::start() before');

		return $_SESSION[self::DEFAULT_PREFIX][$this -> _namespace][$key];
	   
	} 
   
	private function parseKey($key) {
		
		
	
	}
   
	public function __set($key, $value) {

		if (!self::$_session_started)
			throw new Exception('session not started, call Clips_Session::start() before');	

		$_SESSION[self::DEFAULT_PREFIX][$this -> _namespace][$key] = $value;
		
	}
	
	public function __isset($key) {
	
		return isset($_SESSION[self::DEFAULT_PREFIX][$this -> _namespace][$key]);
		
	}


	public function __unset($key) {

		unset($_SESSION[self::DEFAULT_PREFIX][$this -> _namespace][$key]);
		
	}	
	
}

