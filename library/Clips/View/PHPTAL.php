<?php

require_once(Clips_Core::getLibraryPath().'PHPTAL/PHPTAL.php');

class Clips_View_PHPTAL implements Clips_View_Interface {

	const DEFAULT_TEMPLATE_REPOSITORY = 'View/';
	const DEFAULT_OUTPUT_MODE = PHPTAL::HTML5;

	protected static $_instance = NULL;

	protected static $_template = NULL;

	protected static $_values = array();

	protected static $_config = array(

		'template_repository' => NULL,
		'compile_path' => NULL,
		'output_mode' => self::DEFAULT_OUTPUT_MODE,
		'compression' => true,
		'force_reparce' => false,

	);

	protected function __construct($config) {

		if ($config) {

			foreach($config as $key => $value) {

				self::$_config[$key] = $value;

			}

		}

		if (!isset(self::$_config['compile_path']))
			self::$_config['compile_path'] = sys_get_temp_dir();

		if (!isset(self::$_config['template_repository']))
			self::$_config['template_repository'] = Clips_Core::getApplicationPath().self::DEFAULT_TEMPLATE_REPOSITORY;

		self::$_template = new PHPTAL();

	}

	public static function factory($config) {

		if (!isset(self::$_instance)) {

			$c = (__CLASS__);

			self::$_instance = new $c($config);

		} else {
		
			if ($config) {

				foreach($config as $key => $value) {

					self::$_config[$key] = $value;

				}

			}		
		
		}

		return self::$_instance;

	}

	public function load($page, $ext = '.html') {

		self::$_template -> setTemplate(self::$_config['template_repository'].$page.$ext);

	}

	public function __get($name) {

		return self::$_values[$name];

	}

	public function __set($name, $value) {

		self::$_values[$name] = $value;

	}

	public function __isset($name) {

		return isset(self::$_values[$name]);

	}

	public function __unset($name) {

		unset(self::$_values[$name]);

	}

	public function render($echo = true) {

		self::$_template -> setOutputMode(self::$_config['output_mode']);

		self::$_template -> setPhpCodeDestination(self::$_config['compile_path']);

		if (self::$_config['compression'])
			self::$_template -> addPreFilter(new PHPTAL_PreFilter_Compress());

		self::$_template -> setTemplateRepository(self::$_config['template_repository']);

		if (self::$_config['force_reparce'])
			self::$_template -> setForceReparse(self::$_config['compression']);


		foreach(self::$_values as $key => $value) {

			self::$_template -> $key = $value;

		}

		$page = self::$_template -> execute();

		if ($echo == false){
		
			return $page;
			
		}
		
		

		echo $page;


	}

}

