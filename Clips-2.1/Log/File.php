<?php

class Clips_Log_File {

	private $_handle = NULL;
	
	private $_config = NULL;

	public function __construct($config = NULL) {

		if ($config) {
		
			foreach($config as $key => $value) {
			
				$this -> _config[$key] = $value;
				
			}
			
		}
		
		if (!isset($this -> _config['path'])) {
		
			throw new Exception('cannot find path in config parameters');
		
		}
		
		$this -> _handle = fopen($this -> _config['path'], 'a');
		
	}
	
	public function log($message, $type = NULL) {
	
		switch($type) {
						
			case Clips_Log::ERROR: $this -> _report('ERROR', $message); break;
			case Clips_Log::INFO: $this -> _report('INFO', $message); break;
							
			case Clips_Log::LOG: 
			case NULL:
			default:
				$this -> _report('LOG', $message); break;
			
		}
	
	}

	private function _report($type, $message) {
	
		if (is_array($message) && !is_string($message))		
			$message = print_r($message, true);
	
		fwrite($this -> _handle, '['.$type.'] '.date("Y-m-d H:i:s") . ': '. $message."\n");
	
	}

}