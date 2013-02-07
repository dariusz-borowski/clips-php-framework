<?php
/*
Copyright (c) 2010-2013 Dariusz Borowski <http://e-borowski.pl>

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

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

