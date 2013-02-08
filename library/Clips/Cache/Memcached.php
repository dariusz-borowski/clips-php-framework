<?php

class Clips_Cache_Memcached implements Clips_Cache_Interface {

	const DEFAULT_PREFIX = 'Clips_';
	const DEFAULT_LIFETIME = 3600;
    const DEFAULT_HOST = '127.0.0.1';
    const DEFAULT_PORT =  11211;
    const DEFAULT_PERSISTENT = true;
    const DEFAULT_WEIGHT  = 1;
    const DEFAULT_TIMEOUT = 1;
    const DEFAULT_RETRY_INTERVAL = 15;
    const DEFAULT_STATUS = true;
    const DEFAULT_FAILURE_CALLBACK = NULL;

	protected $_memcache = NULL;

	protected $_config = array(

		'namespace' => '',
		'maxlifetime' => self::DEFAULT_LIFETIME,

		'servers' => array(array(
			'host' => self::DEFAULT_HOST,
			'port' => self::DEFAULT_PORT,
			'persistent' => self::DEFAULT_PERSISTENT,
			'weight' => self::DEFAULT_WEIGHT,
			'timeout' => self::DEFAULT_TIMEOUT,
			'retry_interval' => self::DEFAULT_RETRY_INTERVAL,
			'status' => self::DEFAULT_STATUS,
			'failure_callback' => self::DEFAULT_FAILURE_CALLBACK,
		)),

	);

	public function __construct($config = NULL) {

		if ($config) {

			foreach($config as $key => $value) {

				$this -> _config[$key] = $value;

			}

			foreach($this -> _config['servers'] as $keyserver => $server) {

				if (!isset($this -> _config['servers'][$keyserver]['host']))
					$this -> _config['servers'][$keyserver]['host'] = self::DEFAULT_HOST;

				if (!isset($this -> _config['servers'][$keyserver]['port']))
					$this -> _config['servers'][$keyserver]['port'] = self::DEFAULT_PORT;

				if (!isset($this -> _config['servers'][$keyserver]['persistent']))
					$this -> _config['servers'][$keyserver]['persistent'] = self::DEFAULT_PERSISTENT;

				if (!isset($this -> _config['servers'][$keyserver]['weight']))
					$this -> _config['servers'][$keyserver]['weight'] = self::DEFAULT_WEIGHT;

				if (!isset($this -> _config['servers'][$keyserver]['timeout']))
					$this -> _config['servers'][$keyserver]['timeout'] = self::DEFAULT_TIMEOUT;

				if (!isset($this -> _config['servers'][$keyserver]['retry_interval']))
					$this -> _config['servers'][$keyserver]['retry_interval'] = self::DEFAULT_RETRY_INTERVAL;

				if (!isset($this -> _config['servers'][$keyserver]['status']))
					$this -> _config['servers'][$keyserver]['status'] = self::DEFAULT_STATUS;

				if (!isset($this -> _config['servers'][$keyserver]['failure_callback']))
					$this -> _config['servers'][$keyserver]['failure_callback'] = self::DEFAULT_FAILURE_CALLBACK;

			}

		}

		$this -> _memcache = new Memcache;		

		foreach($this -> _config['servers'] as $server) {			
			$this -> _memcache -> addServer($server['host'], $server['port'], $server['persistent'],$server['weight'], $server['timeout'],$server['retry_interval']);
		}


	}

	public function load($key) {

		return $this -> _memcache -> get(self::DEFAULT_PREFIX.$this -> _config['namespace'].'_'.$key);

	}

	public function save($key, $data, $tag = '', $life = NULL) {

		if (!$life)
			$life = $this -> _config['maxlifetime'];

		return $this -> _memcache -> set(self::DEFAULT_PREFIX.$this -> _config['namespace'].'_'.$key, $data, (is_int($data) ? NULL : MEMCACHE_COMPRESSED), $life);

	}

	public function delete($key) {

		return $this -> _memcache -> delete(self::DEFAULT_PREFIX.$this -> _config['namespace'].'_'.$key);

	}

	public function flush() {

		return $this -> _memcache -> flush();

	}

	public function deleteTag($tag) {

		// not implemented

	}

	public function __get($key) {

		return $this -> load($key);

	}

	public function __set($key, $data) {

		$this -> save($key, $data);

	}

	public function __isset($key) {

		return ($this -> load($key) !== false);

	}


	public function __unset($key) {

		return $this -> delete($key);

	}

}