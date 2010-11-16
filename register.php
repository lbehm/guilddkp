<?php
	if($_GET['a'])
	{
		define('loadet', true);
		define('api', true);
		require_once(dirname(__FILE__).'/common.php');
	}
	if($_GET['a']=='r')
	{
		require_once('recaptchalib.php');
		$privatekey = $config->get('gc_privkey');
		$resp = recaptcha_check_answer ($privatekey,$_SERVER["REMOTE_ADDR"],$_POST["recaptcha_challenge_field"],$_POST["recaptcha_response_field"]);
		if (!$resp->is_valid)
		{
			print('captcha');
			die();
		}


		//GET data to create acc and valide this data
		// var data - needed data to create acc
		if(strlen($in->get('birthday', '')) < 8)
		{
			print('bday');
			die();
		}
		$b_day = explode('.', $in->get('birthday', ''));
		$data=array(
			'user_name'=>$in->get('user_name', ''),
			'display_name'=>$in->get('display_name', ''),
			'password'=>$in->get('password', ''),
			'password_b'=>$in->get('password_b', ''),
			'email'=>$in->get('email', ''),
			'birthday'=>mktime(0,0,0,$b_day[1],$b_day[0],$b_day[2])
		);
		$name_query = $db->query("SELECT * FROM ".T_USER." WHERE user_name ='".strtolower($data['user_name'])."'");
		if($db->fetch_record($name_query))
		{
			print('given_usr');
			die();
		}
		elseif(strlen($data['user_name']) < 3)
		{
			print('short_usr');
			die();
		}
		elseif(strlen($data['password']) < 5)
		{
			print('short_pwd');
			die();
		}
		elseif($data['password_b']!=$data['password'])
		{
			print('re_pwd');
			die();
		}
		// var additional_data - data not neccesary but nice to have
		$additional_data=array(
			'pic'=>$in->get('pic', '')
		);
		foreach($data as $field)
			if($field==''||!$field)
			{
				//header('Content-Type: application/json; charset=utf8');
				print('fail_data');
				die();
			}
		// LOG-Eintrag erstellen
		// user anlegen (active = 0)
		$sql = "INSERT INTO dkp_user 
		(user_name,
		user_displayname,
		user_password,
		user_decrypt_password,
		user_email,
		user_rank,
		user_key,
		user_active,
		birthday
		)
		VALUES(
		'".strtolower($data['user_name'])."',
		'".$data['display_name']."',
		'".md5(sha1(str_rot13($data['password'])))."',
		'".base64_encode(str_rot13($data['password']))."',
		'".$data['email']."',
		'2',
		'".md5($data['user_name'].rand(1111,9999))."',
		'0',
		'".date($data['birthday'])."'
		);";
		echo( ($db->query($sql))?"OK":"E");
		die();
	}
	elseif($_GET['a']=='c')
	{
		if($_GET['w']=='un')
		{
			$query = $db->query("SELECT * FROM ".T_USER." WHERE user_name ='".strtolower($in->get('un'))."'");
			header('Content-Type: application/json; charset=utf8');
			print(($db->fetch_record($query))?'0':'1');
			die();
		}
	}
	elseif($_GET['a']=='g')
	{
		if($_GET['w']=='captcha')
		{
			require_once('recaptchalib.php');
			$publickey = $config->get('gc_pubkey');
			//print(recaptcha_get_html($publickey));
			print('<div id="recaptcha_widget" style="display:none">

   <div id="recaptcha_image"></div>
   <div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div>

   <span class="recaptcha_only_if_image">Enter the words above:</span>
   <span class="recaptcha_only_if_audio">Enter the numbers you hear:</span>

   <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />

   <div><a href="javascript:Recaptcha.reload()">Get another CAPTCHA</a></div>
   <div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type(\'audio\')">Get an audio CAPTCHA</a></div>
   <div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type(\'image\')">Get an image CAPTCHA</a></div>

   <div><a href="javascript:Recaptcha.showhelp()">Help</a></div>

 </div>

 <script type="text/javascript"
    src="http://www.google.com/recaptcha/api/challenge?k='.$publickey.'">
 </script>
 <noscript>
   <iframe src="http://www.google.com/recaptcha/api/noscript?k='.$publickey.'"
        height="300" width="500" frameborder="0"></iframe><br>
   <textarea name="recaptcha_challenge_field" rows="3" cols="40">
   </textarea>
   <input type="hidden" name="recaptcha_response_field"
        value="manual_challenge">
 </noscript>');
			die();
		}
	}
	elseif($_GET['a']=='w')
	{
		if(!$config->get('allow_reg'))
		{
			print('503');
			die();
		}
		require_once('recaptchalib.php');
echo <<< EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<link href="templates/clean/templates/main.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="templates/clean/script/js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="templates/clean/script/js/jquery-ui-1.8.5.custom.min.js"></script>
		<meta content="text/html" http-equiv="content-type">
		<meta charset="utf-8">
		<script type="text/javascript">
			$(document).ready(
				function()
				{
					$("#birthday_c").datepicker({showOtherMonths: true,selectOtherMonths: true,dateFormat: 'dd.mm.yy'});
					$("form.reg_form #user_name").blur(function(){
						if(this.value != '')
						{
							$.ajax({
								type: "GET",
								url: "register.php",
								data: "a=c&w=un&un="+this.value,
								cache: false,
								success: function(html)
								{
									if(html==false)
									{
											alert("Dieser Benutzername ist leider bereits vergeben!");
									}
								}
							});
						}
					});
					$("form.reg_form #password").blur(function(){
						if(this.value.length < 5)
						{
							alert("Das Passwort, das Sie verwenden wollen ist zu kurtz!");
						}
					});
					$("form.reg_form #password_b").blur(function(){
						if((this.value != $("form.reg_form #password")[0].value) && $("form.reg_form #password")[0].value != '')
						{
							alert("Das wiederholte Passwort ist nicht mit dem ersten identisch!");
						}
					});
					$("form.reg_form").submit(
						function()
						{
							$("table.reg_tbl").hide('slide', {}, 200, function(){
								$("div.login_loading").html("Loading...");
								$("div.login_loading").show('slide', {}, 200, function(){
									$.ajax({
										type: "GET",
										url: "register.php",
										data: "a=r&user_name="+$("form.reg_form #user_name")[0].value+"&display_name="+$("form.reg_form #display_name")[0].value+"&password="+$("form.reg_form #password")[0].value+"&password_b="+$("form.reg_form #password_b")[0].value+"&email="+$("form.reg_form #email")[0].value+"&birthday="+$("form.reg_form #birthday_c")[0].value,
										cache: false,
										success: function(html)
										{
											if(html=="OK")
											{
												$("div.login_loading").hide('slide', {}, 200, function()
												{
													$("div.login_loading").text("Registrierung erfolgreich erstellt!");
													$("div.login_loading").show('slide', {}, 200, function()
													{
														window.location = window.location.href;
													});
												});
											}
											else
											{
												$("div.login_loading").hide('slide', {}, 200, function()
												{
													$("div.login_loading").html('Registrierung fehlgeschlagen!<br />Bitte &uuml;berprüfen Sie Ihre Angaben!<br /><div><button id="btn_back">Zurück</button></div>');
													$("div.login_loading > div button").button();
													$("div.login_loading > div button#btn_back").click(function(){
														$("div.login_loading").hide('slide', {}, 200, function()
														{
															$("table.reg_tbl").show('slide', {}, 200, function(){});
														});
													});
													$("div.login_loading").show('slide', {}, 200, function(){});
												});
											}
										}
									});
								});
							});
							return false;
						}
					);
				}
			);
		</script>
	</head>
	<body> 	
		<form class="reg_form" action="register.php" methode="Post">
			<table class="reg_tbl">
				<tr><td>Loginname:</td><td><input type="text" name="username" id="user_name" class="text ui-widget-content ui-corner-all" value="" /></td></tr>
				<tr><td>Benutzername:</td><td><input type="text" name="display_name" id="display_name" class="text ui-widget-content ui-corner-all" value="" /></td></tr>
				<tr><td>Passwort:</td><td><input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all" /></td></tr>
				<tr><td>Passwort bestätigen:</td><td><input type="password" name="password_b" id="password_b" value="" class="text ui-widget-content ui-corner-all" /></td></tr>
				<tr><td>E-Mail:</td><td><input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all" /></td></tr>
				<tr><td>Geburtsdatum:</td><td><input type="text" name="date" id="birthday_c" value="" class="text ui-widget-content ui-corner-all" /></td></tr>
				<tr><td>Profilbild:</td><td><input type="text" name="pic_url" id="pic_url" value="" class="text ui-widget-content ui-corner-all" /></td></tr>
				<tr><td>Captcha:</td><td>

EOF;
		print(recaptcha_get_html($config->get('gc_pubkey')));
echo <<< EOF

					</td></tr>
				<tr><td><input type="submit" value="Registrieren"/></td><td><input type="reset" value="Abbrechen"/></td></tr>
			</table>
		</form>
		<div class="login_loading" />
</body>
</html>

EOF;
	}
?>
