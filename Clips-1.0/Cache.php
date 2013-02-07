<?php

class Clips_Cache {

	public static function factory($engine, $config = NULL) {

		$c = 'Clips_Cache_'.$engine;

		return new $c($config);
	
	}

}