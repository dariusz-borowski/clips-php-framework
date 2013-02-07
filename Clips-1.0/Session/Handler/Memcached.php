<?php

final class Clips_Session_Handler_Memcached implements Clips_Session_Handler_Interface {

	protected $_cache = null;

	public function __construct($cache) {

		$this -> _cache = $cache;

	}

	public function read($id) {

		if(!($data = $this -> _cache -> load($id))) {

			return '';
		  
		} else {

			return $data;
		  
		}

	}
	
	public function write($id, $data) {

		$this -> _cache -> save($id, $data, 'session');

		return true;

	}

	public function open($save_path, $name) {

		return true;

	}

	public function close() {

		return true;

	}


	public function destroy($id) {

	}


	public function gc($maxlifetime) {

		return true;

	}

};

?>
