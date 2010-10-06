<?php
if ( !defined('intern') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}
/*
 * Interface to Config-Files
 * @var _cache array
 * @access private
 *
 *
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
	function get($key)
	{
		return $this->_config[$key];
	}
	function get_config()
	{
		return $this->_config;
	}
	function put($key, $val, $sec=false)
	{
		if(!$sec)
			$this->_config[$key] = $val;
		else
			$this->_config[$sec][$key] = $val;
		
		$this->_write_php_ini($this->_config, $this->_filename);
		$this->config_handler();
	}
	function _write_php_ini($array, $file)
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
		$this->_safefilerewrite($file, "; <?php die(); ?>\r\n\r\n".implode("\r\n", $res)."\r\n");
	}
	function _safefilerewrite($filename, $dataToSave)
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
		}
	}
}
?>
