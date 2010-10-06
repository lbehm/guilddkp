<?php
	if(!defined('loadet'))
	{
		die('Do not access this file directly.');
	}
	define('intern', true);
	
	$root_dir = dirname(__FILE__);
	set_include_path($root_dir.'/includes/');
	require_once('filehandler.php');
	require_once('IDS/Init.php');
	require_once('input.php');
	require_once('db_conf.php'); // contains an array
	require_once('mysql.php');
	require_once('tpl/Smarty.class.php');

	$config = new config_handler($root_dir."/includes/config.php");
	// IDS
	try{
		$ids_init = IDS_Init::init($path.'includes/IDS/Config/Config.ini.php');
		$ids = new IDS_Monitor(array_merge_recursive($_GET, $_POST), $ids_init);
		$ids_result = $ids->run();
		if(!$ids_result->isEmpty())
		{
			if(is_array($config->get('IDS')))
			{
				require_once('IDS/Log/Composite.php');
				$ids_logger = new IDS_Log_Composite();
				foreach($config->get('IDS') as $k => $v)
					if($v)
					{
						require_once('IDS/Log/'.$k.'.php');
						$t='IDS_Log_'.$k;
						$ids_logger->addLogger($t::getInstance($ids_init));
					}
				$ids_logger->execute($ids_result);
			}
		}
	}
	catch(Exepetion $e)
	{
		printf('Fehler: %s', $e->getMessage());
	}

	$in = new Input;
	//include db-connection
	$db = new dbal_mysql;
	$db->sql_connect($dbconf['host'], $dbconf['name'], $dbconf['user'], $dbconf['pwd'], false);
	// Database Table names
		define('T_USER',			$dbconf['pre'].'user');
		define('T_SESSIONS',		$dbconf['pre'].'sessions');
		define('T_RANKS_RIGHTS',	$dbconf['pre'].'ranks_rights');
		define('T_RANKS',			$dbconf['pre'].'ranks');
		define('T_NEWS',			$dbconf['pre'].'news');
		//define('T_EVENTS',			$dbconf['pre'].'events');
		//define('T_MULTIDKP',		$dbconf['pre'].'multidkp');
		//define('T_MULTIDKP2EVENTS',	$dbconf['pre'].'multidkp2event');
		//define('T_MEMBER',			$dbconf['pre'].'member');
		//define('T_ADJUSTMENTS',		$dbconf['pre'].'adjustments');
		//define('T_F_POSTS',			$dbconf['pre'].'forum_posts');
		//define('T_F_TOPICS',		$dbconf['pre'].'forum_topics');
		//define('T_F_FORUMS',		$dbconf['pre'].'forum_forums');
		//define('T_LOOT',			$dbconf['pre'].'loot');
		//define('T_RAIDS',			$dbconf['pre'].'raids');
		//define('T_LOGS',			$dbconf['pre'].'logs');
		//define('T_RAID_ATTENDEES',	$dbconf['pre'].'raid_attendees');
		//define('T_ITEMS',			$dbconf['pre'].'items');
		//define('T_COMMENTS',		$dbconf['pre'].'comments');
		//define('T_UPLOADS',		$dbconf['pre'].'uploads');
	unset($dbconf);
	//include tpl-system
	$tpl = new Smarty;
	$tpl->setTpl($config->get('default_template'));
	$tpl->compile_check = true;
	$tpl->debugging = false;

	$SID = '';
	//include sessions
	//include login
	//include usermanagment


?>