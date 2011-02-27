<?php
	/**
	 * user.php
	 * by shiro
	 * edited by devimplode
	 */
	define('loadet', true);
	require_once(dirname(__FILE__).'/common.php');
	require_once('wow_convert.php');
	$username = mb_strtolower($in->get("n", ''),'UTF-8');
	if($username=='')
		$username = $user->data['user_name'];
	$sql = 'SELECT * FROM '.T_USER." WHERE user_name = '".$username."' OR user_displayname = '".$username."'";
	$query_user = $db->query($sql);
	$result_user = $db->fetch_record($query_user);
	
	if ($result_user)
	{
		$tpl->assign('title', $config->get('title').' - '.htmlentities($result_user["user_displayname"],ENT_QUOTES,'UTF-8'));
		$sql = 'SELECT * FROM '.T_CHAR." WHERE user_id = '".$result_user['user_id']."' LIMIT 1";
		$query_char = $db->query($sql) or die("Datenbankabfrage ist fehlgeschlagen!");
		$result_char = $db->fetch_record($query_char);
		if ($result_char) {
			$skill_1_1 = intval($result_char["char_skill_1_1"]);
			$skill_1_2 = intval($result_char["char_skill_1_2"]);
			$skill_1_3 = intval($result_char["char_skill_1_3"]);
			if ($skill_1_1 == 0 && $skill_1_2 == 0 && $skill_1_3 == 0)
			{
				$talents_1_name = 'Nicht verteilt';// untalentiert
				$talents_1_image = 'inv_misc_questionmark';
			}
			elseif (($skill_1_1 > $skill_1_2) && ($skill_1_1 > $skill_1_3))
			{
				$talents_1_name = $classes[$result_char["char_class_id"]][1]['name'];
				$talents_1_image = $classes[$result_char["char_class_id"]][1]['icon'];
			}
			elseif (($skill_1_2 > $skill_1_1) && ($skill_1_2 > $skill_1_3))
			{
				$talents_1_name = $classes[$result_char["char_class_id"]][2]['name'];
				$talents_1_image = $classes[$result_char["char_class_id"]][2]['icon'];
			}
			elseif (($skill_1_3 > $skill_1_1) && ($skill_1_3 > $skill_1_2))
			{
				$talents_1_name = $classes[$result_char["char_class_id"]][3]['name'];
				$talents_1_image = $classes[$result_char["char_class_id"]][3]['icon'];
			}
			else // hybrid
			{
				$talents_1_name = 'Hybrid';
				$talents_1_image = 'ability_dualwieldspecialization';
			}
			
			$skill_2_1 = intval($result_char["char_skill_2_1"]);
			$skill_2_2 = intval($result_char["char_skill_2_2"]);
			$skill_2_3 = intval($result_char["char_skill_2_3"]);
			if ($skill_2_1 == 0 && $skill_2_2 == 0 && $skill_2_3 == 0)
			{
				$talents_2_name = 'Nicht verteilt';// untalentiert
				$talents_2_image = 'inv_misc_questionmark';
			}
			elseif (($skill_2_1 > $skill_2_2) && ($skill_2_1 > $skill_2_3))
			{
				$talents_2_name = $classes[$result_char["char_class_id"]][1]['name'];
				$talents_2_image = $classes[$result_char["char_class_id"]][1]['icon'];
			}
			elseif (($skill_2_2 > $skill_2_1) && ($skill_2_2 > $skill_2_3))
			{
				$talents_2_name = $classes[$result_char["char_class_id"]][2]['name'];
				$talents_2_image = $classes[$result_char["char_class_id"]][2]['icon'];
			}
			elseif (($skill_2_3 > $skill_2_1) && ($skill_2_3 > $skill_2_2))
			{
				$talents_2_name = $classes[$result_char["char_class_id"]][3]['name'];
				$talents_2_image = $classes[$result_char["char_class_id"]][3]['icon'];
			}
			else // hybrid
			{
				$talents_2_name = 'Hybrid';
				$talents_2_image = 'ability_dualwieldspecialization';
			}
			

			$tpl->append('userPage',array(
				'char_detail'=>array(
					'char_name'=>$result_char["char_name"],
					'char_race'=>$result_char["char_race_id"],
					'char_class_name'=>$classes[$result_char["char_class_id"]]['name'],
					'char_class_icon'=>$classes[$result_char["char_class_id"]]['icon'],
					'char_gender'=>$result_char["char_gender"],
					'char_guild'=>$result_char["char_guild"],
					'char_achievments'=>$result_char["char_achievments"],
					'char_hp'=>$result_char["char_health"],
					'char_bar_k'=>$result_char["char_bar_k"],
					'char_bar_v'=>$result_char["char_bar_v"],
					'char_prof1_lang'=>(array_key_exists($result_char["char_prof_1_k"], $skill))?$skill[$result_char["char_prof_1_k"]]['lang']:"Kein Beruf",
					'char_prof1_image'=>(array_key_exists($result_char["char_prof_1_k"], $skill))?$skill[$result_char["char_prof_1_k"]]['icon']:"",
					'char_prof1_value'=>$result_char["char_prof_1_v"],
					'char_prof1_percent'=>(int)($result_char["char_prof_1_v"] /450 *100),
					'char_prof2_lang'=>(array_key_exists($result_char["char_prof_2_k"], $skill))?$skill[$result_char["char_prof_2_k"]]['lang']:"Kein Beruf",
					'char_prof2_image'=>(array_key_exists($result_char["char_prof_2_k"], $skill))?$skill[$result_char["char_prof_2_k"]]['icon']:"",
					'char_prof2_value'=>$result_char["char_prof_2_v"],
					'char_prof2_percent'=>(int)($result_char["char_prof_2_v"] /450 *100),
					'char_talents1_name'=>$talents_1_name,
					'char_talents1_image'=>$talents_1_image,
					'char_talents2_name'=>$talents_2_name,
					'char_talents2_image'=>$talents_2_image,
					'char_2vs2'=>$result_char["char_2vs2_v"],
					'char_3vs3'=>$result_char["char_3vs3_v"],
					'char_5vs5'=>$result_char["char_5vs5_v"],
					'char_talents1_talents'=>$result_char["char_skill_1_1"]." / ".$result_char["char_skill_1_2"]." / ".$result_char["char_skill_1_3"],
					'char_talents2_talents'=>$result_char["char_skill_2_1"]." / ".$result_char["char_skill_2_2"]." / ".$result_char["char_skill_2_3"]
				)
			),true);
		}
		else
			$tpl->append('userPage',array(
				'error'=>"char"
			),true);
		
		//User-Daten
		$tpl->append('userPage',array(
			'info'=>array(
				'id'=>$result_user["user_id"],
				'username'=>htmlentities($result_user["user_displayname"],ENT_QUOTES,'UTF-8'),
				'user_icon'=>($result_user['user_icon'] != '')?$result_user['user_icon']:"http://www.gravatar.com/avatar/".md5($result_user['user_email'])."?d=identicon",
				'email'=>$result_user["user_email"],
				'gender'=>$result_user["gender"],
				'bday'=>$result_user["birthday"],
				'firstname'=>$result_user["first_name"],
				'lastname'=>$result_user["last_name"],
				'town'=>$result_user["town"],
				'country'=>$result_user["country"],
				'state'=>$result_user["state"],
				'facebook'=>$result_user["facebook_name"],
				'icq'=>$result_user["icq"],
				'skype'=>$result_user["skype"],
				'msn'=>$result_user["msn"]
			)
		),true);
		
	}
	else
		$tpl->append('userPage',array(
				'error'=>"user"
			),true);


	$tpl->display('user.tpl');
?>