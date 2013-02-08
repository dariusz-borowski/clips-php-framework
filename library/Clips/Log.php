<?php

class Clips_Log {

	const LOG = 0;
	const ERROR = 1;
	const INFO = 2;

	public function factory($engine, $config = NULL) {
	
		$c = 'Clips_Log_'.$engine;

		return new $c($config);
	
	}

}