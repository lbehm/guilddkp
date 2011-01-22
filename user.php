<?php
	define('loadet', true);
	require_once(dirname(__FILE__).'/common.php');
	$userid = mysql_real_escape_string($in->get("id", ''));
	$sql = 'SELECT * FROM '.T_USER." WHERE user_id = '".$userid."'";
	$result_user = mysql_query($sql) or die("Datenbankabfrage ist fehlgeschlagen!");
	$result_user = mysql_fetch_array($result_user);
	
	if ($result_user) {
		$tpl->assign('username', $result_user["user_displayname"]);
		$tpl->assign('user_icon', $result_user["user_icon"]);
		$sql = 'SELECT * FROM '.T_CHAR." WHERE user_id = '".$userid."' LIMIT 1";
		$result_char = mysql_query($sql) or die("Datenbankabfrage ist fehlgeschlagen!");
		$result_char = mysql_fetch_array($result_char);
		
		if ($result_char) {
			
			
			$tpl->assign('char_race', $result_char["char_race_id"]);
			$tpl->assign('char_gender',$result_char["char_gender"]);
			
			$tpl->assign('char_name', $result_char["char_name"]);
			$tpl->assign('char_guild', $result_char["char_guild"]);
		
			$tpl->assign('char_hp', $result_char["char_health"]);
			$tpl->assign('char_bar_k',$result_char["char_bar_k"]);
			$tpl->assign('char_bar_v',$result_char["char_bar_v"]);
			
			$tpl->assign('char_prof1_value', $result_char["char_prof_1_v"]);
			$tpl->assign('char_prof1_percent',(number_format($result_char["char_prof_1_v"] / 450, 2)) *100);
			
			$tpl->assign('char_prof2_value', $result_char["char_prof_2_v"]);
			$tpl->assign('char_prof2_percent',(number_format($result_char["char_prof_2_v"] / 450, 2)) *100);
			
			$tpl->assign('char_prof1_image', $result_char["char_prof_1_k"]);
			$tpl->assign('char_prof2_image', $result_char["char_prof_2_k"]);
			
			switch($result_char["char_prof_1_k"])
			{
				case "alchemy":
				$tpl->assign('char_prof1_name', "Alchemie");
				break;
				case "blacksmithing":
				$tpl->assign('char_prof1_name', "Schmiedekunst");
				break;
				case "enchanting":
				$tpl->assign('char_prof1_name', "Verzauberkunst");
				break;
				case "engineering":
				$tpl->assign('char_prof1_name', "Ingenieurskunst");
				break;
				case "herbalism":
				$tpl->assign('char_prof1_name', "Kräuterkunde");
				break;
				case "inscription":
				$tpl->assign('char_prof1_name', "Inschriftenkunde");
				break;
				case "jewelcrafting":
				$tpl->assign('char_prof1_name', "Juwelenschleifen");
				break;
				case "leatherworking":
				$tpl->assign('char_prof1_name', "Lederverarbeitung");
				break;
				case "mining":
				$tpl->assign('char_prof1_name', "Bergbau");
				break;
				case "skinning":
				$tpl->assign('char_prof1_name', "Kürschnerei");
				break;
				case "tailoring":
				$tpl->assign('char_prof1_name', "Schneiderei");
				break;
			}
			
			switch($result_char["char_prof_2_k"])
			{
				case "alchemy":
				$tpl->assign('char_prof2_name', "Alchemie");
				break;
				case "blacksmithing":
				$tpl->assign('char_prof2_name', "Schmiedekunst");
				break;
				case "enchanting":
				$tpl->assign('char_prof2_name', "Verzauberkunst");
				break;
				case "engineering":
				$tpl->assign('char_prof2_name', "Ingenieurskunst");
				break;
				case "herbalism":
				$tpl->assign('char_prof2_name', "Kräuterkunde");
				break;
				case "inscription":
				$tpl->assign('char_prof2_name', "Inschriftenkunde");
				break;
				case "jewelcrafting":
				$tpl->assign('char_prof2_name', "Juwelenschleifen");
				break;
				case "leatherworking":
				$tpl->assign('char_prof2_name', "Lederverarbeitung");
				break;
				case "mining":
				$tpl->assign('char_prof2_name', "Bergbau");
				break;
				case "skinning":
				$tpl->assign('char_prof2_name', "Kürschnerei");
				break;
				case "tailoring":
				$tpl->assign('char_prof2_name', "Schneiderei");
				break;
			}
			
			$tpl->assign('char_talents1_talents', $result_char["char_skill_1_1"]." / ".$result_char["char_skill_1_2"]." / ".$result_char["char_skill_1_3"]);
			$tpl->assign('char_talents2_talents', $result_char["char_skill_2_1"]." / ".$result_char["char_skill_2_2"]." / ".$result_char["char_skill_2_3"]);
			
			switch($result_char["char_class_id"])
			{
				case 1:
				if (($result_char["char_skill_1_1"] > $result_char["char_skill_1_2"]) && ($result_char["char_skill_1_1"] > $result_char["char_skill_1_3"])) {
					$talents_name = "Waffen";
					$talents_image = "warrior_arms";
				}
				if (($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"])) {
					$talents_name = "Furor";
					$talents_image = "warrior_fury";
				}
				if (($result_char["char_skill_1_3"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_3"] > $result_char["char_skill_1_2"])) {
					$talents_name = "Schutz";
					$talents_image = "warrior_protection";
				}
				break;
				
				case 2:
				if (($result_char["char_skill_1_1"] > $result_char["char_skill_1_2"]) && ($result_char["char_skill_1_1"] > $result_char["char_skill_1_3"])) {
					$talents_name = "Heilig";
					$talents_image = "paladin_holy";
				}
				if (($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"])) {
					$talents_name = "Schutz";
					$talents_image = "paladin_protection";
				}
				if (($result_char["char_skill_1_3"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_3"] > $result_char["char_skill_1_2"])) {
					$talents_name = "Vergelter";
					$talents_image = "paladin_retribution";
				}
				break;
				
				case 3:
				if (($result_char["char_skill_1_1"] > $result_char["char_skill_1_2"]) && ($result_char["char_skill_1_1"] > $result_char["char_skill_1_3"])) {
					$talents_name = "Tierherrschaft";
					$talents_image = "hunter_beast";
				}
				if (($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"])) {
					$talents_name = "Treffsicherheit";
					$talents_image = "hunter_marksmanship";
				}
				if (($result_char["char_skill_1_3"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_3"] > $result_char["char_skill_1_2"])) {
					$talents_name = "Überleben";
					$talents_image = "hunter_survival";
				}
				break;
				
				case 4:
				if (($result_char["char_skill_1_1"] > $result_char["char_skill_1_2"]) && ($result_char["char_skill_1_1"] > $result_char["char_skill_1_3"])) {
					$talents_name = "Meucheln";
					$talents_image = "rogue_assassination";
				}
				if (($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"])) {
					$talents_name = "Kampf";
					$talents_image = "rogue_combat";
				}
				if (($result_char["char_skill_1_3"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_3"] > $result_char["char_skill_1_2"])) {
					$talents_name = "Täuschung";
					$talents_image = "rogue_subtlety";
				}
				break;
				
				case 5:
				if (($result_char["char_skill_1_1"] > $result_char["char_skill_1_2"]) && ($result_char["char_skill_1_1"] > $result_char["char_skill_1_3"])) {
					$talents_name = "Disziplin";
					$talents_image = "priest_discipline";
				}
				if (($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"])) {
					$talents_name = "Heilig";
					$talents_image = "priest_holy";
				}
				if (($result_char["char_skill_1_3"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_3"] > $result_char["char_skill_1_2"])) {
					$talents_name = "Schatten";
					$talents_image = "priest_shadow";
				}
				break;
				
				case 6:
				if (($result_char["char_skill_1_1"] > $result_char["char_skill_1_2"]) && ($result_char["char_skill_1_1"] > $result_char["char_skill_1_3"])) {
					$talents_name = "Blut";
					$talents_image = "dk_blood";
				}
				if (($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"])) {
					$talents_name = "Frost";
					$talents_image = "dk_frost";
				}
				if (($result_char["char_skill_1_3"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_3"] > $result_char["char_skill_1_2"])) {
					$talents_name = "Unheilig";
					$talents_image = "dk_unholy";
				}
				break;
				
				case 7:
				if (($result_char["char_skill_1_1"] > $result_char["char_skill_1_2"]) && ($result_char["char_skill_1_1"] > $result_char["char_skill_1_3"])) {
					$talents_name = "Elementar";
					$talents_image = "shaman_elemental";
				}
				if (($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"])) {
					$talents_name = "Verstärker";
					$talents_image = "shaman_enhancement";
				}
				if (($result_char["char_skill_1_3"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_3"] > $result_char["char_skill_1_2"])) {
					$talents_name = "Wiederherstellung";
					$talents_image = "shaman_restoration";
				}
				break;
				
				case 8:
				if (($result_char["char_skill_1_1"] > $result_char["char_skill_1_2"]) && ($result_char["char_skill_1_1"] > $result_char["char_skill_1_3"])) {
					$talents_name = "Arkan";
					$talents_image = "mage_arcane";
				}
				if (($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"])) {
					$talents_name = "Feuer";
					$talents_image = "mage_fire";
				}
				if (($result_char["char_skill_1_3"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_3"] > $result_char["char_skill_1_2"])) {
					$talents_name = "Frost";
					$talents_image = "mage_frost";
				}
				break;
				
				case 9:
				if (($result_char["char_skill_1_1"] > $result_char["char_skill_1_2"]) && ($result_char["char_skill_1_1"] > $result_char["char_skill_1_3"])) {
					$talents_name = "Gebrechen";
					$talents_image = "warlock_affliction";
				}
				if (($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"])) {
					$talents_name = "Dämonologie";
					$talents_image = "warlock_demonology";
				}
				if (($result_char["char_skill_1_3"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_3"] > $result_char["char_skill_1_2"])) {
					$talents_name = "Zerstörung";
					$talents_image = "warlock_destruction";
				}
				break;
				
				case 11:
				if (($result_char["char_skill_1_1"] > $result_char["char_skill_1_2"]) && ($result_char["char_skill_1_1"] > $result_char["char_skill_1_3"])) {
					$talents_name = "Gleichgewicht";
					$talents_image = "druid_balance";
				}
				if (($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_2"] > $result_char["char_skill_1_1"])) {
					$talents_name = "Wilder Kampf";
					$talents_image = "druid_feral";
				}
				if (($result_char["char_skill_1_3"] > $result_char["char_skill_1_1"]) && ($result_char["char_skill_1_3"] > $result_char["char_skill_1_2"])) {
					$talents_name = "Wiederherstellung";
					$talents_image = "druid_restoration";
				}
				break;
			}
			$tpl->assign('char_talents1_name', $talents_name);
			$tpl->assign('char_talents1_image', $talents_image);
			
			switch($result_char["char_class_id"])
			{
				case 1:
				if (($result_char["char_skill_2_1"] > $result_char["char_skill_2_2"]) && ($result_char["char_skill_2_1"] > $result_char["char_skill_2_3"])) {
					$talents_name = "Waffen";
					$talents_image = "warrior_arms";
				}
				if (($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"])) {
					$talents_name = "Furor";
					$talents_image = "warrior_fury";
				}
				if (($result_char["char_skill_2_3"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_3"] > $result_char["char_skill_2_2"])) {
					$talents_name = "Schutz";
					$talents_image = "warrior_protection";
				}
				break;
				
				case 2:
				if (($result_char["char_skill_2_1"] > $result_char["char_skill_2_2"]) && ($result_char["char_skill_2_1"] > $result_char["char_skill_2_3"])) {
					$talents_name = "Heilig";
					$talents_image = "paladin_holy";
				}
				if (($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"])) {
					$talents_name = "Schutz";
					$talents_image = "paladin_protection";
				}
				if (($result_char["char_skill_2_3"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_3"] > $result_char["char_skill_2_2"])) {
					$talents_name = "Vergelter";
					$talents_image = "paladin_retribution";
				}
				break;
				
				case 3:
				if (($result_char["char_skill_2_1"] > $result_char["char_skill_2_2"]) && ($result_char["char_skill_2_1"] > $result_char["char_skill_2_3"])) {
					$talents_name = "Tierherrschaft";
					$talents_image = "hunter_beast";
				}
				if (($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"])) {
					$talents_name = "Treffsicherheit";
					$talents_image = "hunter_marksmanship";
				}
				if (($result_char["char_skill_2_3"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_3"] > $result_char["char_skill_2_2"])) {
					$talents_name = "Überleben";
					$talents_image = "hunter_survival";
				}
				break;
				
				case 4:
				if (($result_char["char_skill_2_1"] > $result_char["char_skill_2_2"]) && ($result_char["char_skill_2_1"] > $result_char["char_skill_2_3"])) {
					$talents_name = "Meucheln";
					$talents_image = "rogue_assassination";
				}
				if (($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"])) {
					$talents_name = "Kampf";
					$talents_image = "rogue_combat";
				}
				if (($result_char["char_skill_2_3"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_3"] > $result_char["char_skill_2_2"])) {
					$talents_name = "Täuschung";
					$talents_image = "rogue_subtlety";
				}
				break;
				
				case 5:
				if (($result_char["char_skill_2_1"] > $result_char["char_skill_2_2"]) && ($result_char["char_skill_2_1"] > $result_char["char_skill_2_3"])) {
					$talents_name = "Disziplin";
					$talents_image = "priest_discipline";
				}
				if (($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"])) {
					$talents_name = "Heilig";
					$talents_image = "priest_holy";
				}
				if (($result_char["char_skill_2_3"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_3"] > $result_char["char_skill_2_2"])) {
					$talents_name = "Schatten";
					$talents_image = "priest_shadow";
				}
				break;
				
				case 6:
				if (($result_char["char_skill_2_1"] > $result_char["char_skill_2_2"]) && ($result_char["char_skill_2_1"] > $result_char["char_skill_2_3"])) {
					$talents_name = "Blut";
					$talents_image = "dk_blood";
				}
				if (($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"])) {
					$talents_name = "Frost";
					$talents_image = "dk_frost";
				}
				if (($result_char["char_skill_2_3"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_3"] > $result_char["char_skill_2_2"])) {
					$talents_name = "Unheilig";
					$talents_image = "dk_unholy";
				}
				break;
				
				case 7:
				if (($result_char["char_skill_2_1"] > $result_char["char_skill_2_2"]) && ($result_char["char_skill_2_1"] > $result_char["char_skill_2_3"])) {
					$talents_name = "Elementar";
					$talents_image = "shaman_elemental";
				}
				if (($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"])) {
					$talents_name = "Verstärker";
					$talents_image = "shaman_enhancement";
				}
				if (($result_char["char_skill_2_3"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_3"] > $result_char["char_skill_2_2"])) {
					$talents_name = "Wiederherstellung";
					$talents_image = "shaman_restoration";
				}
				break;
				
				case 8:
				if (($result_char["char_skill_2_1"] > $result_char["char_skill_2_2"]) && ($result_char["char_skill_2_1"] > $result_char["char_skill_2_3"])) {
					$talents_name = "Arkan";
					$talents_image = "mage_arcane";
				}
				if (($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"])) {
					$talents_name = "Feuer";
					$talents_image = "mage_fire";
				}
				if (($result_char["char_skill_2_3"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_3"] > $result_char["char_skill_2_2"])) {
					$talents_name = "Frost";
					$talents_image = "mage_frost";
				}
				break;
				
				case 9:
				if (($result_char["char_skill_2_1"] > $result_char["char_skill_2_2"]) && ($result_char["char_skill_2_1"] > $result_char["char_skill_2_3"])) {
					$talents_name = "Gebrechen";
					$talents_image = "warlock_affliction";
				}
				if (($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"])) {
					$talents_name = "Dämonologie";
					$talents_image = "warlock_demonology";
				}
				if (($result_char["char_skill_2_3"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_3"] > $result_char["char_skill_2_2"])) {
					$talents_name = "Zerstörung";
					$talents_image = "warlock_destruction";
				}
				break;
				
				case 11:
				if (($result_char["char_skill_2_1"] > $result_char["char_skill_2_2"]) && ($result_char["char_skill_2_1"] > $result_char["char_skill_2_3"])) {
					$talents_name = "Gleichgewicht";
					$talents_image = "druid_balance";
				}
				if (($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_2"] > $result_char["char_skill_2_1"])) {
					$talents_name = "Wilder Kampf";
					$talents_image = "druid_feral";
				}
				if (($result_char["char_skill_2_3"] > $result_char["char_skill_2_1"]) && ($result_char["char_skill_2_3"] > $result_char["char_skill_2_2"])) {
					$talents_name = "Wiederherstellung";
					$talents_image = "druid_restoration";
				}
				break;
			}
			$tpl->assign('char_talents2_name', $talents_name);
			$tpl->assign('char_talents2_image', $talents_image);
			
			$tpl->assign('char_2vs2', $result_char["char_2vs2_v"]);
			$tpl->assign('char_3vs3', $result_char["char_3vs3_v"]);
			$tpl->assign('char_5vs5', $result_char["char_5vs5_v"]);
		}else{
			$tpl->assign('error', "char");
		}
		//User-Daten
		$tpl->assign('user_email', $result_user["user_email"]);
		$tpl->assign('user_gender', $result_user["gender"]);
		$tpl->assign('user_bday', $result_user["birthday"]);
		$tpl->assign('user_firstname', $result_user["first_name"]);
		$tpl->assign('user_lastname', $result_user["last_name"]);
		$tpl->assign('user_town', $result_user["town"]);
		$tpl->assign('user_country', $result_user["country"]);
		$tpl->assign('user_state', $result_user["state"]);
		$tpl->assign('user_facebook', $result_user["facebook_name"]);
		$tpl->assign('user_icq', $result_user["icq"]);
		$tpl->assign('user_skype', $result_user["skype"]);
		$tpl->assign('user_msn', $result_user["msn"]);
											  
		
	}else{
		$tpl->assign('error', "user");
	}
		
		
	$tpl->display('user.tpl');
?>