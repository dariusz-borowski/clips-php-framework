<?php

class Clips_Router {

	private static $_routing = null;
	
	private static $_table;
	
	public static function routing($routing = array()) {
	
		self::$_routing = $routing;
		
		self::$_table = array();

		foreach($routing as $keymethod => $method) {
		
			foreach($method as $keyroute => $route) {
			
				list($controller, $action) =  explode('/', $route);
				
				$keyroute = substr($keyroute, 1);
				$parts = explode('/',$keyroute);

				$trace = array();
				$parameters = NULL;
				
				foreach($parts as $keypart => $part) {
				
					$element = $part;
				
					if ($part[0] === '@') {
					
						if (strpos($part, '=') === false) {
						
							$parameter = $part;
							$regexp = '(.*)';
						
						} else {
					
							list($parameter, $regexp) = explode('=', $part);
							
						}
											
						$p['parameter'] = substr($parameter, 1);
						$p['regexp'] = $regexp;
						$parameters[] = $p;						
						
						$element = $regexp;
					}
					
					$trace[] = $element;
				
				}
				
				if (is_array($trace))
					$regexp = implode('/', $trace); else
					$regexp = $trace;

				self::$_table[$keymethod][$regexp] = new stdClass;
				self::$_table[$keymethod][$regexp] -> controller = $controller;
				self::$_table[$keymethod][$regexp] -> action = $action;
				self::$_table[$keymethod][$regexp] -> method = $keymethod;
				self::$_table[$keymethod][$regexp] -> parameters = $parameters;
			
			}
			
		}

	}

	
	public static function location($uri) {
	
		if ($uri[0] == '/')
			$uri = substr($uri, 1);
	
		header('Location: '.$uri);
		die();
	
	}
	
	public static function getAction($uri) {

		$i = 0;

		$uri = substr($uri, 1);	
		
		if ($uri === false) $uri = '';
		
		if (!isset(self::$_table)) {
			
			throw new Exception('Routing table not configured, call Clips_Core::routing before Clips_Core::bootstrap', -999);
		
			return NULL;
			
		}
		
		foreach(self::$_table as $keymethod => $method) {
		
			if ($keymethod != Clips_Request::$_SERVER['REQUEST_METHOD'] && $keymethod != '*') continue;
		
			if (isset($method[$uri]))
				return $method[$uri];

			foreach($method as $keyroute => $route) {
				
				if ($keyroute == $uri) {
								
				} else {
				
					if ($route -> parameters) {
				
						$result = @preg_match('#^'.str_replace('/', '\/', $keyroute).'$#Ui', $uri, $matches);
					
						if ($result) {
							
							if ((count($matches) - 1) == count($route -> parameters)) {
								
								$count = count($matches);
								
								foreach($route -> parameters as $keyparameters => $parameters) {
								
									$route -> parameters[$keyparameters]['value'] = $matches[$keyparameters + 1];
								
								}
								
								return $route;
								
							}
							
						}
						
					
					}
					
				}
			
			}
		
		}
			
		return NULL;
	
	}


}

?>