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
	 *
	 */
	class cache_handler
	{
		/**
		 * Die geladenen Cache-Bereiche und -Werte
		 * $_cache[Bereich/section/file][Schluessel/key] = (string)Wert/value
		 *
		 * @var array
		 */
		var $_cache = array();
		
		/**
		 * Das Cache-Verzeichnis
		 *
		 * @var string
		 */
		var $_cache_dir = '';
		
		/**
		 * Konstruktor
		 * Ueberprueft und setzt Verzeichnisnamen
		 *
		 * @access public
		 * @param string $dir	directoryname
		 * @return bool
		 */
		public function __construct($dir=false)
		{
			$this->_cache_dir = (isset($dir)) ? $dir : $this->_cache_dir;
			return (!empty($this->_cache_dir));
		}
		/**
		 * Dekonstruktor
		 * Schreibt die geladenen Cache-Bereiche in die Dateien
		 *
		 * @access public
		 * @param void
		 * @return void
		 */
		public function __destruct()
		{
			foreach($this->_cache as $sec => $key)
				$this->write_buffer($sec);
		}

		/**
		 * get
		 * fordert Cache-Eintraege an
		 *
		 * @access public
		 * @param string $sec	Cache-Bereich(section)
		 * @param string $key	Cache-Schluessel
		 * @return mixed 		Cache-Wert(value)
		 */
		public function get($sec=false, $k=false)
		{
			// existiert bereits ein gueltiger wert im geladenen Cache
			if(array_key_exists($sec,$this->_cache))
				if(array_key_exists($k,$this->_cache[$sec]))
					return unserialize($this->_cache[$sec][$k]['val']);
			// aus datei nachladen, wenn der wert noch nicht aus dem cache geladen wurde
			if($this->loadCache($sec))
				if(array_key_exists($sec,$this->_cache))
					if(array_key_exists($k,$this->_cache[$sec]))
						return unserialize($this->_cache[$sec][$k]['val']);
			return false;
		}
		/**
		 * loadCache
		 * nachladen von Cache-Bereichen aus Cache-Dateien
		 * gibt bei Fehlern (bool)false zurueck
		 *
		 * @access private
		 * @param string $sec	Cache-Bereich(section)
		 * @return mixed 		(array)aktualisierter Cache-Bereich
		 */
		private function loadCache($sec=false)
		{
			if(empty($sec))
				return false;
			// existiert die cache-section-datei
			$file = @file_get_contents($this->_cache_dir.$sec.'.cache');
			if(!$file)
				return false;
			// inhalt entpacken
			$data = unserialize($file);
			if(!is_array($data))
				return false;
			unset($file);
			$time = time();
			// gueltige schluessel uebernehmen
			foreach($data as $k => $v)
				if($v['time'] >= $time)
					$this->_cache[$sec][$k]=array(
						'time'=>(int)$v['time'],
						'val'=>$v['val']
					);
			// return updated section
			return $this->_cache[$sec];
		}
		/**
		 * set
		 * erstellen von Cache-Eintraegen
		 * gibt bei Fehlern (bool)false zurueck
		 * ist $buffer==false wird die Cache-Datei sofort geschrieben
		 *
		 * @access public
		 * @param string $sec	Cache-Bereich(section)
		 * @param string $k		Cache-Schluessel(key)
		 * @param mixed $val	Cache-Wert(value)
		 * @param int $ttl		Gueltigkeitsdauer
		 * @param bool $buffer	Buffer verwenden
		 * @return bool
		 */
		public function set($sec, $k, $val, $ttl=3600, $buffer=true)
		{
			// werte checken
			if(empty($sec) || $k==''||$val==''||$k==false||$val==false||$ttl==0)
				return false;
			// cache-section updaten
			$this->loadCache($sec);
			// set values
			$this->_cache[$sec][$k]['time']=time() + $ttl;
			$this->_cache[$sec][$k]['val']=(string)serialize($val);
			// cache-file schreiben
			if(!$buffer)
				return(safefilerewrite($this->_cache_dir.$sec.'.cache', serialize($this->_cache[$sec])));
			return true;
		}
		/**
		 * write_buffer
		 * erstellt die Cache-Datei des Cache-Bereichs $sec
		 *
		 * @access public
		 * @param string $sec	Cache-Bereich(section)
		 * @return bool
		 */
		public function write_buffer($sec)
		{
			return(safefilerewrite($this->_cache_dir.$sec.'.cache', serialize($this->_cache[$sec])));
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
			else
				$res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
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
