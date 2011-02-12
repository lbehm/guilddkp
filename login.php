<?php
	define('loadet', true);
	require_once(dirname(__FILE__).'/common.php');

	/* **********
	 * login.php
	 * **********
	 */

	$login = $in->get('login', '');
	$logout = $in->get('logout', '');
	$username = $in->get('username', '');
	$password = $in->get('password', '');
	$redirect = $in->get('re', '');
	$auto_login = $in->get('auto_login', '');
	$lost_password = $in->get('lost_password', '');

	// If there's an external login without showing template stuff and so on...
	if($login == true)
	{
		$auto_login = ( !empty($auto_login) ) ? true : false;
		if($user->login($username, $password, $auto_login))
		{
			die('OK');
		}
		else
		{
			die('Error');
		}
	}
	elseif($logout == true && $user->data['user_id'] != ANONYMOUS)
	{
		$user->destroy($user->sid);
		die();
	}

	//
	// Lost Password Form
	//
	if ($lost_password != '')
	{
		$tpl->append('msg',
			array(
				'S_LOGIN' => false,

				'L_GET_NEW_PASSWORD' => $user->lang['get_new_password'],
				'L_USERNAME'         => $user->lang['username'],
				'L_EMAIL'            => $user->lang['email'],
				'L_SUBMIT'           => $user->lang['submit'],
				'L_RESET'            => $user->lang['reset']
			)
		);
		$tpl->assign('title', $config->get('title').' - Login');
		$tpl->display('lost_pwd.tpl');
	}
?>
