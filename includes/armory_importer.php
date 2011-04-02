<?php
	if(!defined('intern'))
	{
		header('HTTP/1.0 404 Not Found');
		exit;
	}
	/**
	 * includes/functions.php
	 * by devimplode
	 */
	require_once('wow_convert.php');

	class armory_importer
	{
		function importChar($char=false, $realm="Acheron")
		{
			if(!$char)
				return false;
			global $db, $config;
			$xml=self::GetCharXML($char);
			$charData=self::GetCharData($xml);
			//print_r($charData);
			$bar_convert=array(
				'r'=>'rage',
				'e'=>'energy',
				'm'=>'mana',
				'p'=>'runepower'
			);
			$charData['bar_type']=$bar_convert[$charData['bar_type']];
			if(!$charData)
				return false;
			if($char_id=$db->query_first("SELECT char_id FROM ".T_CHAR." WHERE char_name='".$charData['name']."'"))
			{
				return($db->query("UPDATE ".T_CHAR." SET char_name='".$charData['name']."', char_guild='".$charData['guildname']."', char_level='".$charData['level']."', char_race_id='".$charData['raceid']."', char_class_id='".$charData['classid']."', char_gender='".$charData['genderid']."', char_achievments='".$charData['points']."', char_skill_1_1='".$charData['talents'][0]['treeone']."', char_skill_1_2='".$charData['talents'][0]['treetwo']."', char_skill_1_3='".$charData['talents'][0]['treethree']."', char_skill_2_1='".$charData['talents'][1]['treeone']."', char_skill_2_2='".$charData['talents'][1]['treetwo']."', char_skill_2_3='".$charData['talents'][1]['treethree']."', char_prof_1_k='".$charData['skills'][0]['id']."', char_prof_1_v='".$charData['skills'][0]['value']."', char_prof_2_k='".$charData['skills'][1]['id']."', char_prof_2_v='".$charData['skills'][1]['value']."', char_health='".$charData['health']."', char_bar_k='".$charData['bar_type']."', char_bar_v='".$charData['bar_value']."', char_update='".time()."' WHERE char_id='".$char_id."'"));
			}
			else
			{
				if($db->query("INSERT INTO `".T_CHAR."` (`char_name`, `char_guild`, `char_level`, `char_race_id`, `char_class_id`, `char_gender`, `char_achievments`, `char_skill_1_1`, `char_skill_1_2`, `char_skill_1_3`, `char_skill_2_1`, `char_skill_2_2`, `char_skill_2_3`, `char_prof_1_k`, `char_prof_1_v`, `char_prof_2_k`, `char_prof_2_v`, `char_health`, `char_bar_k`, `char_bar_v`, `char_update`) VALUES('".$charData['name']."', '".$charData['guildname']."', '".$charData['level']."', '".$charData['raceid']."', '".$charData['classid']."', '".$charData['genderid']."', '".$charData['points']."', '".$charData['talents'][0]['treeone']."', '".$charData['talents'][0]['treetwo']."', '".$charData['talents'][0]['treethree']."', '".$charData['talents'][1]['treeone']."', '".$charData['talents'][1]['treetwo']."', '".$charData['talents'][1]['treethree']."', '".$charData['skills'][0]['id']."', '".$charData['skills'][0]['value']."', '".$charData['skills'][1]['id']."', '".$charData['skills'][1]['value']."', '".$charData['health']."', '".$charData['bar_type']."', '".$charData['bar_value']."', '".time()."')"))
				{
					if($config->get('startDKP'))
					{
						$char_id=$db->insert_id();
						$db->query("INSERT INTO ".T_DKP." (char_id, dkp_ref, dkp_ref_id, dkp, dkp_note, dkp_time) VALUES('".$char_id."', 'other', null, '".$config->get('startDKP')."', 'Start-DKP', '".time()."')");
					}
					return $char_id;
				}
			}

			return false;
		}
		function GetCharData($xml)
		{
			if($xml[0]->attributes()->errCode=='noCharacter')
				return false;
			$char=array();
			//Basis-Werte
			foreach($xml[0]->character->attributes() as $k=>$v)
				$char[strtolower($k)]=(string)$v;
			//Talente
			foreach($xml[0]->characterTab->talentSpecs->talentSpec as $s)
			{
				$talents=array();
				foreach($s->attributes() as $k=>$v)
				{
					$talents[strtolower($k)]=(string)$v;
				}
				$char['talents'][]=$talents;
			}
			//Berufe
			foreach($xml[0]->characterTab->professions->skill as $s)
			{
				$skills=array();
				foreach($s->attributes() as $k=>$v)
				{
					$skills[strtolower($k)]=(string)$v;
				}
				$char['skills'][]=$skills;
			}
			//hp and secondary bar
			//foreach($xml[0]->characterTab->characterBars->health->attributes() as $s)
			$char['health']=(string)$xml[0]->characterTab->characterBars->health->attributes()->effective;
			$char['bar_type']=(string)$xml[0]->characterTab->characterBars->secondBar->attributes()->type;
			$char['bar_value']=(string)$xml[0]->characterTab->characterBars->secondBar->attributes()->effective;
			return($char);
		}
		function GetCharXML($char, $realm="Acheron", $loc='de', $lang='de_de')
		{
			global $config;
			$url = $config->get('armory_URL').'character-sheet.xml?r='.$realm.'&cn='.$char;
			$xml = simplexml_load_string(self::read_url($url, $lang));
			if(is_object($xml))
				return $xml->xpath("/page/characterInfo");
			else
				return 0;
		}
		function read_url($url, $lang='de_de')
		{
			if(function_exists('curl_init'))
			{
				$curl = @curl_init($url);
				$cookie = "cookieLangId=".$lang.";";

				@curl_setopt($curl, CURLOPT_COOKIE, $cookie);
				@curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.2) Gecko/20070220 Firefox/2.0.0.2');
				@curl_setopt($curl, CURLOPT_TIMEOUT, 20);
				if (!(@ini_get("safe_mode") || @ini_get("open_basedir"))) {
					@curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
				}
				@curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

				$xml_data = @curl_exec($curl);
				curl_close($curl);
			}
			else
			{
				$cheader   = array("http" => array ("header" => "Cookie: cookieLangId=".$lang.";\r\n"));
				$context   = @stream_context_create($cheader);
				$xml_data  = @file_get_contents($url, false, $context);
			}
			return $xml_data;
		}
	}
?>
