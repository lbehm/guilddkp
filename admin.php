<?php
	/**
	 * admin.php
	 * by devimplode
	 */
	define('loadet', true);
	require_once(dirname(__FILE__).'/common.php');
	if($user->data['auth']['rank_power'] < 255)
		die();

	switch($in->get('p', ''))
	{
		case 'importRaid':
			
			$tpl->display('raidImport.html');
		break;
	}
?>