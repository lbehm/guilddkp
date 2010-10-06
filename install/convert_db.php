<?php
	/******
	*Converter from eqdkp 0.6 to guilddkp
	******/

	define('intern', true);
	require(dirname(__file__)."\..\includes\mysql.php");
	$eqdkp = new dbal_mysql;
	$eqdkp->sql_connect("localhost", "eqdkp", "root", "", false);

	$guilddkp = new dbal_mysql;
	$guilddkp->sql_connect("localhost", "guilddkp", "root", "", false);

	$data = array(
		'news' => array(),
		'user' => array()
	);

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
