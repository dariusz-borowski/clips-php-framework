<?php


class Clips_Orm extends Clips_Orm_Abstract {

	private $_orm = NULL;
	private $_name = NULL;

	function __construct($name) {
	
		$c = 'Model_Orm_'.$name;
		$this -> _name = $name;
		$this -> _orm = new $c();
		
	}
	

	public static function loadCache($method, $_VARS = NULL) {

		if (!isset($_VARS['lifetime']))
			$_VARS['lifetime'] = self::DEFAULT_LIFETIME;

		$lifetime = $_VARS['lifetime'];

		unset($_VARS['lifetime']);

		$cache = Model_Cache::factory();

		$tag = __CLASS__.'_'.$method.($_VARS ? '_'.json_encode($_VARS) : '');

		$result = $cache -> load($tag);

		if ($result === false) {

			$result = self::$method($_VARS);

			$cache -> save($tag, $result, NULL, $lifetime);

		}

		return $result;

	}	
	
	public function find($id) {

		$select = $this -> _orm -> _db -> select() -> from($this -> _orm -> _table, array('*'));
		
		$result = $select -> query() -> fetch();
		
		return $result;
	
	}

}
