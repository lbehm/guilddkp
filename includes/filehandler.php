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
		var $_filename = '';
		
		// constructor
		function cache_handler($filename=false)
		{
			$this->_filename = ($filename) ? $filename : $this->_filename;
			if($this->_filename=='')
				return false;
			if(!file_exists($this->_filename))
				write_php_ini($this->_cache, $this->_filename);
			$time = time();
			$changed = false;
			$tmp = parse_ini_file($this->_filename, true);
			foreach($tmp as $k=>$v)
				if(((int)$v['time']+(int)$v['ttl']) > $time)
					$this->_cache[$k]=$v;
			return true;
		}
		function get($k=false)
		{
			return(($k)?( array_key_exists($k, $this->_cache)?(unserialize($this->_cache[$k]['val'])):false):false);
		}
		function set($k, $val, $ttl=3600, $buffer=false)
		{
			if($k==''||$val==''||$k==false||$val==false||$ttl==0)
				return false;
			$this->_cache[$k]['time']=time();
			$this->_cache[$k]['ttl']=$ttl;
			$this->_cache[$k]['val']=serialize($val);
			if(!$buffer)
				return(write_php_ini($this->_cache, $this->_filename));
			return true;
		}
		function write_buffer()
		{
			return(write_php_ini($this->_cache, $this->_filename));
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
