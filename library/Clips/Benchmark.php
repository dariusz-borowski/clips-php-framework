<?php

class Clips_Benchmark {
	
	private static $_benchmark = array(); 

	public static function start($name)
	{
		self::$_benchmark[ $name ]['memory'] = memory_get_usage();
		self::$_benchmark[ $name ]['time'] = microtime(true);
		
		return self::$_benchmark[ $name ];
   	}
   	
	public static function stop($name , $dec = 5)
	{
   		self::$_benchmark[ $name ]['memory'] = memory_get_usage() - self::$_benchmark[$name]['memory'];
   		self::$_benchmark[ $name ]['time'] = number_format(microtime(true) - self::$_benchmark[$name]['time'], $dec);
		
		return self::$_benchmark[ $name ];
		
	}
	
}