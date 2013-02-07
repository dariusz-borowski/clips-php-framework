<?php

class Clips_Registry {
	
	protected static $_values = array();

	public static function set($name, $value) {

		self::$_values[$name] = $value;
		
	}	
	
	public static function get($name) {

		if(isset(self::$_values[$name])) {
		
			return self::$_values[$name];
			
		}
		
		return false;
		
	}
	
	public static function getAll() {
	
		return self::$_values;
		
	}
		
	public static function delete($name) {

		if( isset(self::$_values[$name]) ){
		
			unset(self::$_values[$name]);
			
		}
		
	}
	
	public static function deleteAll() {

		self::$_values = array();
		
	}
	
	public function __get($name) {
	
		return $this -> get($name);
	   
	} 
   
	public function __set($name, $value) {

		$this -> set($name, $value);
		
	}
	
	public function __isset($name) {
	
		return isset(self::$_values[$name]);
		
	}


	public function __unset($name) {
	
		unset(self::$_values[$name]);
		
	}		
	
}
