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
	require_once('mysql.php');
	require_once('session.php');
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
	$conn = new config_handler($root_dir."/includes/db_conf.php");
	$db->sql_connect($conn->get('host'), $conn->get('name'), $conn->get('user'), $conn->get('pwd'), false);
	// Database Table names
	unset($conn);
	//include tpl-system
	$tpl = new Smarty;
	$tpl->setTpl($config->get('default_template'));
	$tpl->compile_check = true;
	$tpl->debugging = true;

	$SID = '';
	$user = new User;
	$user->start();
	$user->setup();

	//include sessions
	//include login
	//include usermanagment


?>