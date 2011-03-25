<?php
	/**
	 * raid.php
	 * by devimplode
	 */
	define('loadet', true);
	define('api', true);
	require_once(dirname(__FILE__).'/common.php');

	switch ($in->get('p', ''))
	{
		case 'profil':
			if($in->get('c', '') == 'accdata')
			{
				$accdata = array(
					'user_displayname'=>$in->get('username', ''),
					'user_icon'=>$in->get('icon', ''),
					'first_name'=>$in->get('firstname', ''),
					'last_name'=>$in->get('lastname', ''),
					'birthday'=>$in->get('bday', ''),
					'icq'=>$in->get('icq', ''),
					'skype'=>$in->get('skype', ''),
					'msn'=>$in->get('msn', '')
				);
				$modify = array();
				foreach($accdata as $k=>$v)
				{
					if($user->data[$k] != $v)
					{
						$modify[$k]=$v;
					}
				}
				if(count($modify))
				{
					$fields = array();
					foreach($modify as $k=>$v)
					{
						$fields[] = "`".$k."` = '".$v."'";
					}
					$sql = "UPDATE ".T_USER." SET ".implode(', ', $fields)." WHERE user_id='".$user->data['user_id']."'";
					die(($db->query($sql))?'1':'2');
				}
				die('0');
			}
			break;
		case 'getDKP':
			die(funct::getDKP());
			break;
		default :
			die();
	}
?>
