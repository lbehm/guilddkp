<?php
	/******
	*Converter from eqdkp 0.6 to guilddkp
	******/

	define('intern', true);
	require(dirname(__file__)."/../includes/filehandler.php");
	$config = new config_handler(dirname(__file__)."/..includes/config.php");
	require(dirname(__file__)."/../includes/mysql.php");
	$eqdkp = new dbal_mysql;
	$eqdkp->sql_connect("localhost", "eqdkp", "root", "", false);

	$guilddkp = new dbal_mysql;
	$guilddkp->sql_connect("localhost", "guilddkp", "root", "", false);

	$data = array(
		'news' => array(),
		'user' => array()
	);
	
	if($_REQUEST['only'] == 'char')
	{
		$guilddkp->query("DELETE FROM dkp_char");
		$guilddkp->query("DELETE FROM dkp_dkp");
		$query=$eqdkp->query("select * from eqdkp_members m JOIN eqdkp_member_additions ma ON m.member_id=ma.member_id");
		while($member=$eqdkp->fetch_record($query))
		{
			$bar_convert=array(
				'm'=>'mana',
				'rp'=>'runepower',
				'r'=>'rage',
				'e'=>'energy'
			);
			$member['second_name']=$bar_convert[$member['second_name']];
			$class_convert=array(
				12=>1,	// warrior
				13=>2,	// paladin
				4=>3,	// hunter
				2=>4,	// rogue
				6=>5,	// priest
				20=>6,	// DK
				9=>7,	// shaman
				11=>8,	// mage
				10=>9,	// warlock
				7=>11,	// druid
			);
			$member['member_class_id']=$class_convert[$member['member_class_id']];
			$race_convert=array(
				2=>1,		// human
				7=>2,		// orc
				3=>3,		// dwarf
				4=>4,		// night elf
				6=>5,		// undead
				8=>6,		// tauren
				1=>7,		// gnome
				5=>8,		// troll
				10=>10,		// blood elf
				9=>11,		// draenei
			);
			$member['member_race_id']=$race_convert[$member['member_race_id']];
			$gender_convert=array(
				'Female'=>1,
				'Male'=>0
			);
			$member['gender']=$gender_convert[$member['gender']];
			$prof_convert=array(
				'enchanting'=>333,
				'tailoring'=>197,
				'jewelcrafting'=>755,
				'mining'=>186,
				'blacksmithing'=>164,
				'inscription'=>773,
				'alchemy'=>171,
				'herbalism'=>182,
				'skinning'=>393,
				'leatherworking'=>165,
				'engineering'=>202
			);
			$member['prof1_name']=$prof_convert[$member['prof1_name']];
			$member['prof2_name']=$prof_convert[$member['prof2_name']];
			$sql="INSERT INTO `dkp_char` (`char_name`, `char_guild`, `char_level`, `char_race_id`, `char_class_id`, `char_gender`, `char_skill_1_1`, `char_skill_1_2`, `char_skill_1_3`, `char_skill_2_1`, `char_skill_2_2`, `char_skill_2_3`, `char_prof_1_k`, `char_prof_1_v`, `char_prof_2_k`, `char_prof_2_v`, `char_health`, `char_bar_k`, `char_bar_v`, `char_update`) VALUES('".$member['member_name']."', '".$member['guild']."', '".$member['member_level']."', '".$member['member_race_id']."', '".$member['member_class_id']."', '".$member['gender']."', '".$member['skill_1']."', '".$member['skill_2']."', '".$member['skill_3']."', '".$member['skill2_1']."', '".$member['skill2_2']."', '".$member['skill2_3']."', '".$member['prof1_name']."', '".$member['prof1_value']."', '".$member['prof2_name']."', '".$member['prof2_value']."', '".$member['health_bar']."', '".$member['second_name']."', '".$member['second_bar']."', '".time()."')";
			$guilddkp->query($sql);
			$guilddkp->query("INSERT INTO dkp_dkp (char_id, dkp_ref, dkp_ref_id, dkp, dkp_note, dkp_time) VALUES('".$guilddkp->insert_id()."', 'other', null, '10', 'Start-DKP', '".time()."')");
		}
	}
			die($sql);

	if( ( ($_REQUEST['only'] == 'news') || (!isset($_REQUEST['news'])) ) && ($_REQUEST['without'] != 'news') )
	{
		$guilddkp->query("DELETE FROM dkp_news;");
	}
	if( ( ($_REQUEST['only'] == 'user') || (!isset($_REQUEST['only'])) ) && ($_REQUEST['without'] != 'user') )
	{
		$guilddkp->query("DELETE FROM dkp_user;");
	}

	//SELECT
	if( ( ($_REQUEST['only'] == 'user') || (!isset($_REQUEST['only'])) ) && ($_REQUEST['without'] != 'user') )
	{
		//User
		$sql = "SELECT * FROM eqdkp_users u ORDER BY user_id";
		$result = $eqdkp->query($sql);
		while($row = $eqdkp->fetch_record($result))
			$data['user'][] = $row;
		$eqdkp->free_result($result);

		//User
		$guilddkp->query("DELETE FROM dkp_user;");
		foreach($data['user'] as $user)
		{
			$sql = "INSERT INTO dkp_user
			(
			user_name,
			user_displayname,
			user_password,
			user_decrypt_password,
			user_email,
			user_rank,
			user_style,
			user_lang,
			user_key,
			user_lastvisit,
			user_lastip,
			user_lastpage,
			user_active,
			user_newpassword,
			first_name,
			last_name,
			country,
			town,
			state,
			ZIP_code,
			phone,
			cellphone,
			address,
			facebook_name,
			game_acc,
			icq,
			skype,
			msn,
			irq,
			gender,
			birthday
			)
			VALUES(
			'".$guilddkp->sql_escape($user['username'])."',
			'".$guilddkp->sql_escape($user['username'])."',
			'".$guilddkp->sql_escape($user['user_password'])."',
			'',
			'".$guilddkp->sql_escape($user['user_email'])."',
			'1',
			'1',
			'de_de',
			'".$guilddkp->sql_escape($user['user_key'])."',
			'".$guilddkp->sql_escape($user['user_lastvisit'])."',
			'',
			'".$guilddkp->sql_escape($user['user_lastpage'])."',
			'".$guilddkp->sql_escape($user['user_active'])."',
			'".$guilddkp->sql_escape($user['user_newpassword'])."',
			'".$guilddkp->sql_escape($user['first_name'])."',
			'".$guilddkp->sql_escape($user['last_name'])."',
			'".$guilddkp->sql_escape($user['country'])."',
			'".$guilddkp->sql_escape($user['town'])."',
			'".$guilddkp->sql_escape($user['state'])."',
			'".$guilddkp->sql_escape($user['ZIP_code'])."',
			'".$guilddkp->sql_escape($user['phone'])."',
			'".$guilddkp->sql_escape($user['cellphone'])."',
			'".$guilddkp->sql_escape($user['address'])."',
			'',
			'',
			'".$guilddkp->sql_escape($user['icq'])."',
			'".$guilddkp->sql_escape($user['skype'])."',
			'".$guilddkp->sql_escape($user['msn'])."',
			'".$guilddkp->sql_escape($user['irq'])."',
			'".$guilddkp->sql_escape($user['gender'])."',
			'".$guilddkp->sql_escape($user['birthday'])."');";
			$guilddkp->query($sql);
		}
	}

	//News
	if( ( ($_REQUEST['only'] == 'news') || (!isset($_REQUEST['news'])) ) && ($_REQUEST['without'] != 'news') )
	{
		//News
		$sql = "SELECT n.*, u.username FROM eqdkp_news n, eqdkp_users u WHERE n.user_id = u.user_id ORDER BY news_id";
		$result = $eqdkp->query($sql);
		while($row = $eqdkp->fetch_record($result))
			$data['news'][] = $row;
		$eqdkp->free_result($result);

		$guilddkp->query("DELETE FROM dkp_news;");
		foreach($data['news'] as $news)
		{
		$sql = "INSERT INTO dkp_news
				(news_headline, news_message, news_date, nocomments, news_permissions, news_flags, user_id)
				VALUES('".$guilddkp->sql_escape($news['news_headline'])."', '".$guilddkp->sql_escape($news['news_message'])."', '".$guilddkp->sql_escape($news['news_date'])."', '".$guilddkp->sql_escape($news['nocomments'])."', '".$guilddkp->sql_escape($news['news_permissions'])."', '".$guilddkp->sql_escape($news['news_flags'])."', (SELECT user_id FROM dkp_user WHERE user_name = '".$guilddkp->sql_escape($news['username'])."'));";
		$guilddkp->query($sql);
		}
	}
?>
