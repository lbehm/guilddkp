<?php
	if ( !defined('intern') )
	{
		header('HTTP/1.0 404 Not Found');
		exit;
	}
	/*
	 * Interface to Config-Files
	 * @var _config array
	 * @access private
	 */
	class config_handler
	{
		var $_config = array();
		var $_filename = '';
		
		// constructor
		function config_handler($filename=false)
		{
			$this->_filename = ($filename) ? $filename : $this->_filename;
			if($this->_filename=='' || !file_exists($this->_filename))
				return false;
			$this->_config = parse_ini_file($this->_filename, true);
			foreach($this->_config['define'] as $k=>$v)
				define($k, $v);
			return true;
		}
		function get($key, $sec='general')
		{
			return $this->_config[$sec][$key];
		}
		function get_config()
		{
			return $this->_config;
		}
		function put($key, $val, $sec='general')
		{
			$this->set($key, $val, $sec);
		}
		function set($key, $val, $sec='general')
		{
			$this->_config[$sec][$key] = $val;

			write_php_ini($this->_config, $this->_filename);
			$this->config_handler();
		}
	}

	/*
	 * Interface to Cache-Files
	 * @var _cache array
	 * @access private
	 */
	class cache_handler
	{
		var $_cache = array();
		var $_cache_dir = '';
		
		// constructor
		function cache_handler($dir=false)
		{
			$this->_cache_dir = ($dir) ? $dir : $this->_cache_dir;
			if($this->_cache_dir=='')
				return false;
			if(!file_exists($this->_cache_dir.'index.html'))
				return false;
			return true;
		}
		function get($sec=false, $k=false)
		{
			if(array_key_exists($sec,$this->_cache))
			{
				if(array_key_exists($k,$this->_cache[$sec]))
				{
					return unserialize($this->_cache[$sec][$k]['val']);
				}
			}
			else
			{
				if(file_exists($this->_cache_dir.$sec.'.cache.php'))
				{
					$time = time();
					$tmp = parse_ini_file($this->_cache_dir.$sec.'.cache.php', true);
					foreach($tmp as $k=>$v)
						if(((int)$v['time']+(int)$v['ttl']) > $time)
							$this->_cache[$sec][$k]=$v;
					return(array_key_exists($k,$this->_cache[$sec])?(unserialize($this->_cache[$sec][$k]['val'])):false);
				}
			}
			return false;
		}
		function _loadCache($sec=false)
		{
			if(file_exists($this->_cache_dir.$sec.'.cache.php'))
			{
				$time = time();
				$tmp = parse_ini_file($this->_cache_dir.$sec.'.cache.php', true);
				foreach($tmp as $k=>$v)
					if(((int)$v['time']+(int)$v['ttl']) > $time)
						$this->_cache[$sec][$k]=$v;
				return $this->_cache[$sec];
			}
			return false;
		}
		function set($sec, $k, $val, $ttl=3600, $buffer=false)
		{
			if($k==''||$val==''||$k==false||$val==false||$ttl==0)
				return false;
			$this->_loadCache($sec);
			$this->_cache[$sec][$k]['time']=time();
			$this->_cache[$sec][$k]['ttl']=$ttl;
			$this->_cache[$sec][$k]['val']=serialize($val);
			if(!$buffer)
				return(write_php_ini($this->_cache[$sec], $this->_cache_dir.$sec.'.cache.php'));
			return true;
		}
		function write_buffer()
		{
			return(write_php_ini($this->_cache[$sec], $this->_cache_dir.$sec.'.cache.php'));
		}
	}

	function write_php_ini($array, $file)
	{
		$res = array();
		foreach($array as $key => $val)
		{
			if(is_array($val))
			{
				$res[] = "\r\n[$key]";
				foreach($val as $skey => $sval) $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
			}
			else $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
		}
		safefilerewrite($file, "; <?php die(); ?>\r\n".implode("\r\n", $res)."\r\n");
	}
	function safefilerewrite($filename, $dataToSave)
	{    
		if ($fp = fopen($filename, 'w'))
		{
			$startTime = microtime();
			do
			{            
				$canWrite = flock($fp, LOCK_EX);
				// If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
				if(!$canWrite) 
					usleep(round(rand(0, 100)*1000));
			}while((!$canWrite)and((microtime()-$startTime) < 1000));
			//file was locked so now we can store information
			if ($canWrite)
			{
				fwrite($fp, $dataToSave);
				flock($fp, LOCK_UN);
			}
			fclose($fp);
			return true;
		}
	}
?>
