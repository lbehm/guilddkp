<?php
	define('loadet', true);
	require_once(dirname(__FILE__).'/common.php');
	$oldpw = mysql_real_escape_string($in->get("oldpw", ''));
	$newpw = mysql_real_escape_string($in->get("newpw", ''));
	$newpwwdh = mysql_real_escape_string($in->get("newpwwdh", ''));
	if ($in->get("info", '') == 'pw') {
		if (($oldpw == '') or ($newpw == '') or ($newpwwdh == '')) {
			die('empty');
		}
		if ($newpw != $newpwwdh){
			die('different');
		}
		$sql = "SELECT user_password, user_decrypt_password FROM ".T_USER." WHERE user_id = '".$user->data['user_id']."';";
		$result = mysql_query($sql) or die("Datenbankabfrage ist fehlgeschlagen!");
		$result = mysql_fetch_array($result);
		if (($result["user_password"] != md5(sha1(str_rot13($oldpw))) or ($result["user_decrypt_password"] != base64_encode(str_rot13($oldpw))))){
			die('wrong');
		}
		$sql = "UPDATE ".T_USER." SET user_password = '".md5(sha1(str_rot13($newpw)))."', user_decrypt_password = '".base64_encode(str_rot13($newpw))."' WHERE user_id = '".$user->data['user_id']."';";
		$result = mysql_query($sql) or die("Datenbankabfrage ist fehlgeschlagen!");
		die("ok");
	}
?>