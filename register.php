<?php
	if($_GET['a'])
	{
		define('loadet', true);
		define('api', true);
		require_once(dirname(__FILE__).'/common.php');
	}
	if( ( $_GET['a']=='r' ) && ( $_GET['fb']==true ) && ( $_POST['signed_request'] ) )
	{
		$request = $_POST['signed_request'];
		list($encoded_sig, $payload) = explode('.', $request, 2);
		$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
		if(strtoupper($data['algorithm']) !== 'HMAC-SHA256')
		{
			header("Location:news");
			die();
		}
		$expected_sig = hash_hmac('sha256', $payload, $config->get('facebook_secret'), $raw = true);
		if((base64_decode(strtr($encoded_sig, '-_', '+/'))) !== $expected_sig)
		{
			header("Location:news");
			die();
		}
		
		$b_day = explode('/', $data['registration']['birthday']);
		$insert_data=array(
			'user_name'=>$data['registration']['accname'],
			'display_name'=>$data['registration']['name'],
			'password'=>$data['registration']['password'],
			'email'=>$data['registration']['email'],
			'birthday'=>mktime(0,0,0,$b_day[0],$b_day[1],$b_day[2]),
			'pic'=>($data['user_id'])?"https://graph.facebook.com/".$data['user_id']."/picture":'',
			'first_name'=>$data['registration']["first_name"],
			'last_name'=>$data['registration']["last_name"],
			'facebook_name'=>($data['user_id'])?$data['user_id']:''
		);
		$name_query = $db->query("SELECT user_name FROM ".T_USER." WHERE user_name ='".$db->sql_escape(mb_strtolower($insert_data['user_name'],'UTF-8'))."'");
		if($db->fetch_record($name_query))
		{
			header("Location:news");
			die();
		}

		// LOG-Eintrag erstellen
		// user anlegen (active = 0)
		$sql = "INSERT INTO dkp_user 
		(
		user_name,
		user_displayname,
		user_password,
		user_decrypt_password,
		user_email,
		user_icon,
		user_rank,
		user_key,
		user_active,
		birthday,
		first_name,
		last_name,
		facebook_name
		)
		VALUES(
		'".$db->sql_escape(mb_strtolower($insert_data['user_name'],'UTF-8'))."',
		'".$db->sql_escape($insert_data['display_name'])."',
		'".md5(sha1(str_rot13($insert_data['password'])))."',
		'".base64_encode(str_rot13($insert_data['password']))."',
		'".$db->sql_escape($insert_data['email'])."',
		'".$db->sql_escape($insert_data['pic'])."',
		2,
		'".md5(uniqid($insert_data['user_name']))."',
		1,
		'".date('j.n.Y',$insert_data['birthday'])."',
		'".$db->sql_escape($insert_data['first_name'])."',
		'".$db->sql_escape($insert_data['last_name'])."',
		'".$db->sql_escape($insert_data['facebook_name'])."'
		);";
		
		$insert_query=(($db->query($sql))?1:0);
		header("Location:news");
		die();
	}
	else if($_GET['a']=='c')
	{
		if($_GET['w']=='un')
		{
			$query = $db->query("SELECT * FROM ".T_USER." WHERE user_name ='".$db->sql_escape(mb_strtolower($in->get('un'),"UTF-8"))."'");
			die(json_encode(array('r'=>(($db->fetch_record($query))?1:0))));
		}
	}
?>
