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
	require_once('bbcode.php');
	if(!defined('api'))
		require_once('tpl/Smarty.class.php');

	$config = new config_handler($root_dir."/includes/config.php");
	$cache = new cache_handler($root_dir."/includes/cache/");
	// IDS
	if($config->get('ids_enabled'))
	{
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
							// For php < 5.3 (i hate my hoster...)
							if($k=="File")
								$ids_logger->addLogger(IDS_Log_File::getInstance($ids_init));
							elseif($k=="Database")
								$ids_logger->addLogger(IDS_Log_Database::getInstance($ids_init));
							elseif($k=="Email")
								$ids_logger->addLogger(IDS_Log_Email::getInstance($ids_init));
						}
					$ids_logger->execute($ids_result);
				}
			}
		}
		catch(Exepetion $e)
		{
			printf('Fehler: %s', $e->getMessage());
		}
	}

	$in = new Input;
	//include db-connection
	$db = new dbal_mysql;
	$conn = new config_handler($root_dir."/includes/db_conf.php");
	$db->sql_connect($conn->get('host'), $conn->get('name'), $conn->get('user'), $conn->get('pwd'), false);
	// Database Table names
	unset($conn);
	//include tpl-system
	if(!defined('api'))
	{
		$tpl = new Smarty;
		$tpl->template_dir = $root_dir.'/templates';
		$tpl->setTpl($config->get('default_template'));
		$tpl->compile_check = ($config->get('template_compile_check'))?true:false;
		$tpl->debugging = ($config->get('template_debug'))?true:false;
		$tpl->assign('base_page', $config->get('main_page'));
		$tpl->assign('domain', $config->get('domain'));
		$tpl->assign('icon_repo', $config->get('icon_repo'));
		$tpl->assign('icon_repo_tiny', $config->get('icon_repo_tiny'));
		$tpl->assign('icon_repo_small', $config->get('icon_repo_small'));
		$tpl->assign('icon_repo_medium', $config->get('icon_repo_medium'));
		$tpl->assign('icon_repo_large', $config->get('icon_repo_large'));
		$tpl->assign('FB', ($config->get('facebook'))?true:false);
		$tpl->assign('FB_APPID', $config->get('facebook_appid'));
	}

	$SID = '';
	$user = new User;
	$user->start();
	$user->setup();
	//$cache = new cache_handler($root_dir."/includes/cache/user_".$user->data['user_id'].".php");
	if(!defined('api'))
	{
		$tpl->assign('SID', ($SID!='?s=')?$SID:'');
		$tpl->assign('user_icon', ($user->data['user_icon'] != '')? $user->data['user_icon']:"http://www.gravatar.com/avatar/".md5($user->data['user_email'])."?d=identicon");
		
		$active_user = array();
		$query = $db->query("SELECT u.user_name as name, u.user_displayname as displayname, u.user_icon as icon, MD5(u.user_email) as hash FROM (".T_SESSIONS." s JOIN ".T_USER." u ON s.session_user_id = u.user_id) WHERE u.user_rank IN (SELECT rank_id FROM ".T_RANKS." WHERE rank_hide = 0) AND s.session_current > '".(time()-300)."';");
		while($k = $db->fetch_record($query))
			$active_user[] = $k;
		$currentUser="";
		foreach($active_user as $k=>$v)
			$currentUser.='<a href="user-'.$v['name'].'"><img class="user" alt="'.$v['displayname'].'" title="'.$v['displayname'].'" src="'.(($v['icon']!='')?$v['icon']:"http://www.gravatar.com/avatar/".$v['hash']."?d=identicon").'" /></a>';
		$tpl->assign('ACTIVE_USER', $currentUser);
		if($user->check_auth('rank_read_forum'))
		{
			require_once('forum.class.php');
			$forum=new forum;
			$forum->generate_menu();
		}
	}
	

	//include sessions
	//include login
	//include usermanagment


?>