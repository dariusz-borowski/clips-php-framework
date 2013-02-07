<?php

class Clips_Cache_File implements Clips_Cache_Interface {

	const DEFAULT_LIFETIME = 3600;
	const DEFAULT_NAMESPACE = 'CLIPS';
	const DEFAULT_PREFIX = 'Clips_';

	static private $_config = array(

		'namespace'	=> self::DEFAULT_NAMESPACE,
		'maxlifetime' => self::DEFAULT_LIFETIME,
		'dir' => NULL,
		'autoclean' => true,

	);

	public function __construct($config = NULL) {

		if ($config) {

			foreach($config as $key => $value) {

				self::$_config[$key] = $value;

			}

		}

		if (!self::$_config['dir'])
			self::$_config['dir'] = sys_get_temp_dir();

	}

	protected static function _cleanString( $str, $replace = "_" ) {

		$str = preg_replace("/[^a-z0-9_]/i", $replace, trim($str) );

		$str = iconv('UTF-8', 'ASCII//TRANSLIT', trim($str));

		$charsArr =  array( '^', '\'', '"', '`', '~');
		$str = str_replace( $charsArr, '', $str );

		return $str;

	}

	protected function _autoclean() {

		$files = glob(self::$_config['dir'].'/'.self::DEFAULT_PREFIX.'*');

		if ($files) {

			foreach($files as $file) {

				$expiration = substr($file, strrpos($file, '_') + 1);

				if (filemtime($file) < (time() - $expiration)) {

					unlink($file);

				}
			}

		}

	}

    public function load($key) {

        if ( !is_dir(self::$_config['dir']) OR !is_writable(self::$_config['dir'])) {

            return false;

        }

		if (self::$_config['autoclean']) {

			self::_autoclean();

		}

		$cache_path = glob(self::$_config['dir'].'/'.'*_'.sha1($key).'_*');

		if (!$cache_path) return false;

		$cache_path = $cache_path[0];

		if (!self::$_config['autoclean']) {

			$expiration = substr($cache_path, strrpos($cache_path, '_') + 1);

			if (filemtime($cache_path) < (time() - $expiration)) {

				$this -> clear($key);

				return false;

			}

		}

        if (!@file_exists($cache_path)) {

            return false;

        }

        if (!$fp = @fopen($cache_path, 'rb')) {

            return false;

        }

        flock($fp, LOCK_SH);

        $cache = '';

        if (filesize($cache_path) > 0) {

            $cache = unserialize(fread($fp, filesize($cache_path)));

        } else {

            $cache = NULL;

        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return $cache;

    }

    public function save($key, $data, $tag = NULL, $life = NULL) {

		if (!$tag || $tag === '')
			$tag = self::$_config['namespace'];

		if (!$life)
			$life = self::$_config['maxlifetime'];

        if ( !is_dir(self::$_config['dir']) OR !is_writable(self::$_config['dir'])) {

            return false;

        }

		$cleanTag = self::_cleanString($tag);

		if ($cleanTag == '') {
			throw new Exception('tag name is too short');
		}

		$oldcache = glob(sprintf("%s/%s", self::$_config['dir'], self::DEFAULT_PREFIX.$cleanTag.'_'.sha1($key).'_*'));

		if ($oldcache) {

			foreach($oldcache as $old) {

				unlink($old);

			}

		}

        $cache_path = sprintf("%s/%s", self::$_config['dir'], self::DEFAULT_PREFIX.$cleanTag.'_'.sha1($key).'_'.$life);

        if ( ! $fp = fopen($cache_path, 'wb')) {

            return false;

        }

        if (flock($fp, LOCK_EX)) {

            fwrite($fp, serialize($data));
            flock($fp, LOCK_UN);

        } else {

            return false;

        }

        fclose($fp);

        @chmod($cache_path, 0777);

        return true;
    }

    public function delete($key) {

		$cache_path = glob(self::$_config['dir'].'/'.self::DEFAULT_PREFIX.'*_'.sha1($key).'_*');

		if (!$cache_path) return false;

		$cache_path = $cache_path[0];

        if (file_exists($cache_path)) {

            unlink($cache_path);
            return true;

        }

        return false;

    }

	public function deleteTag($tag) {

		$files = glob(self::$_config['dir'].'/'.self::DEFAULT_PREFIX.$tag.'_*');

		foreach($files as $file) {

			unlink($file);

		}

	}

	public function flush() {

		$files = glob(self::$_config['dir'].'/'.self::DEFAULT_PREFIX.'_*');

		foreach($files as $file) {

			unlink($file);

		}

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

?>
