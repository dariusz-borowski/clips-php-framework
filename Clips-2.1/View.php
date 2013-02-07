<?php

class Clips_View {

	protected function __construct() {
	}
	
	public static function factory($engine, $config = NULL) {
	
		$c = 'Clips_View_'.$engine;

		$adapter = $c::factory($config);
		
		return $adapter;
	
	}

}