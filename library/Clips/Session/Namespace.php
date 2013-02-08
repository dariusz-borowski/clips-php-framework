<?

class Clips_Session_Namespace {

	const DEFAULT_NAMESPACE = '__CLIPS';
	
	static private $_instance = array();
	
    public static function factory($namespace = NULL) {

		if (!isset($namespace)) {
			
			$namespace = self::DEFAULT_NAMESPACE;				
			
		}
		
		if (!isset(self::$_instance[$namespace])) {

			self::$_instance[$namespace] = new Clips_Session($namespace);
			
		}
		
		return self::$_instance[$namespace];

    }
	
}