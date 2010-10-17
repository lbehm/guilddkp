<?php
	define('loadet', true);
	require_once(dirname(__FILE__).'/common.php');

	/* **********
	 * login.php
	 * **********
	 */

	// Make our _GET and _POST vars into normal variables
	// so we can process a login request through get or post
	extract($_GET, EXTR_SKIP);
	extract($_POST, EXTR_SKIP);

	// If there's an external login without showing template stuff and so on...
	if($external == 'yes')
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

	// Normal Output
	if ( (isset($login)) || (isset($logout)) )
	{
		if ( isset($login) && ($user->data['user_id'] <= 0) )
		{
			$redirect = ( isset($redirect) ) ? $redirect : 'index.php';

			$auto_login = ( !empty($auto_login) ) ? true : false;

			if ( !$user->login($username, $password, $auto_login) )
			{
				$tpl->assign_var('META', '<meta http-equiv="refresh" content="3;url=login.php' . $SID . '&amp;redirect=' . $redirect . '">');

				message_die($user->lang['invalid_login'], $user->lang['error']);
			}
		}
		elseif ( $user->data['user_id'] != ANONYMOUS )
		{
			$user->destroy();
		}

		$redirect_url = ( isset($redirect) ) ? preg_replace('#^.*?redirect=(.+?)&(.+?)$#', '\\1' . $SID . '&\\2', $redirect) : 'index.php';
		header("Location: ".$redirect_url);
	}

	//
	// Lost Password Form
	//
	if ( isset($lost_password) )
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
	//
	// Login form
	//
	elseif ( $user->data['user_id'] <= 0 )
	{
		$tpl->append('msg',
			array(
				'S_LOGIN' => true,

				'L_LOGIN'             => ($conf_plus['pk_bridge_cms_active'] ==1) ? $user->lang['login_bridge_notice'] : $user->lang['login']  ,
				'L_USERNAME'          => $user->lang['username'],
				'L_PASSWORD'          => $user->lang['password'],
				'L_REMEMBER_PASSWORD' => $user->lang['remember_password'],

				'L_LOST_PASSWORD'     => $user->lang['lost_password'],

				'ONLOAD' => ' onload="javascript:document.post.username.focus()"'
			)
		);
		$tpl->assign('title', $config->get('title').' - Login');
		$tpl->display('login.tpl');
	}
	else
	{
		header("Location: index.php".$SID);
	}
?>
