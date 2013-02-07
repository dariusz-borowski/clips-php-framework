<?php

abstract class Clips_Orm_Abstract {

    private static $instances = array();
	
    public static function factory() {

		$key = serialize(func_get_args());
		
		if (!isset(self::$instances[$key])) {
		
			$rc = new ReflectionClass(get_called_class());					
			self::$instances[$key] = $rc -> newInstanceArgs(func_get_args());
			
		}
		
		return self::$instances[$key];
		
    }
	
}
