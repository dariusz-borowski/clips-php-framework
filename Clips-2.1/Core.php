<?php

final class Clips_Core {

	private static $_instance = null;

	private static $_config = array(
		'PATH_LIBRARY' => 'library/',
		'PATH_APPLICATION' => 'application/',
	);

	private function __construct($config = NULL) {

		spl_autoload_register(array('Clips_Core', 'autoload'));

		if ($config)
			foreach($config as $key => $value) {

				self::$_config[$key] = $value;

			}

		Clips_Request::factory();

	}

	public static function factory($config = NULL) {

		if (!isset(self::$_instance)) {

			$c = __CLASS__;
			self::$_instance = new $c($config);

		} else {

			if ($config)
				foreach($config as $key => $value) {

					self::$_config[$key] = $value;

				}

		}

		return self::$_instance;

	}


	public static function getLibraryPath() {

		return self::$_config['PATH_LIBRARY'];

	}

	public static function getApplicationPath() {

		return self::$_config['PATH_APPLICATION'];

	}

	public static function autoload($class) {

		$class = str_replace( array('_', '\\'), DIRECTORY_SEPARATOR, $class );

		if (file_exists(self::$_config['PATH_LIBRARY'].$class.'.php')) {

			require_once(self::$_config['PATH_LIBRARY'].$class.'.php');

		} else {

			if (file_exists(self::$_config['PATH_APPLICATION'].$class.'.php')) {

				require_once(self::$_config['PATH_APPLICATION'].$class.'.php');

			}

		}

	}

	public static function routing($routing = NULL) {

		if (!isset(self::$_instance)) self::factory();

		if ($routing) {

			Clips_Router::routing($routing);

		}

	}

	public static function loadModel($model) {

		$model = str_replace( array('_', '\\'), DIRECTORY_SEPARATOR, $model);

	}

	public static function loadController($controller) {

		$controller = str_replace( array('_', '\\'), DIRECTORY_SEPARATOR, $controller );

	}
	
	public static function errortrap($exception) {
	
		if (class_exists('Controller_Error')) {
			
			if (method_exists('Controller_Error', 'errorAction')) {
			
				call_user_func(array(new Controller_Error, 'errorAction'), $exception);
			
			} else throw $exception;

		} else throw $exception;
	
	}

	public static function bootstrap() {

		// Clips_Benchmark::start('Core');

		$uri = substr(Clips_Request::$_SERVER['REQUEST_URI'], strlen(Clips_Request::$_DIR), strpos(Clips_Request::$_SERVER['REQUEST_URI'], '?') ? strpos(Clips_Request::$_SERVER['REQUEST_URI'], '?') - strlen(Clips_Request::$_DIR) : strlen(Clips_Request::$_SERVER['REQUEST_URI']));

		try {
		
			$action = Clips_Router::getAction($uri);
		
		} catch(Exception $e) {
		
			self::errortrap($e);
		
		}
		
		if (!$action) {
		
			self::errortrap(new Exception('Non-routable address, check your routing table or handle the 404 error', -1));
		
		} else {

			$controller = ucwords($action -> controller);

			$parameters = array();

			if ($action -> parameters) {

				foreach($action -> parameters as $parameter) {

					$parameters[$parameter['parameter']] = $parameter['value'];

				}

			}			

			$action = strtolower($action -> action).'Action';
		
		}
		
		$class = 'Controller_'.$controller;

		self::autoload($class);

		if (class_exists($class)) {

			if (method_exists($class, $action)) {

				$object = new $class;

				call_user_func(array($object, $action), $parameters);

			} else {
			
				self::errortrap(new Exception('Method '.$action.' in '.$class.' not found', -2));
				
			}

		} else {

				self::errortrap(new Exception('Class '.$class.' not found', -3));
						
		}

		// $bench = Clips_Benchmark::stop('Core');

	}

}

?>