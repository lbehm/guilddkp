<?php
	/**
	 * user.php
	 * by shiro
	 * edited by devimplode
	 */
	define('loadet', true);
	require_once(dirname(__FILE__).'/common.php');
	$userid = $in->get("id", 0);
	if (!$userid)
		$userid = $user->data['user_id'];
	$sql = 'SELECT * FROM '.T_USER." WHERE user_id = '".$userid."'";
	$query_user = $db->query($sql);
	$result_user = $db->fetch_record($query_user);
	
	if ($result_user) {
		$tpl->assign('title', 'GuildDKP - '.htmlentities($result_user["user_displayname"],ENT_QUOTES,'UTF-8'));
		$sql = 'SELECT * FROM '.T_CHAR." WHERE user_id = '".$userid."' LIMIT 1";
		$query_char = $db->query($sql) or die("Datenbankabfrage ist fehlgeschlagen!");
		$result_char = $db->fetch_record($result_char);
		if ($result_char) {
			$tpl->append('userPage',array(
				'char_detail'=>array(
					'char_name'=>$result_char["char_name"],
					'char_race'=>$result_char["char_race_id"],
					'char_gender'=>$result_char["char_gender"],
					'char_guild'=>$result_char["char_guild"],
					'char_hp'=>$result_char["char_health"],
					'char_bar_k'=>$result_char["char_bar_k"],
					'char_bar_v'=>$result_char["char_bar_v"],
					'char_prof1_value'=>$result_char["char_prof_1_v"],
					'char_prof1_percent'=>(int)($result_char["char_prof_1_v"] /450 *100),
					'char_prof2_value'=>$result_char["char_prof_2_v"],
					'char_prof2_percent'=>(int)($result_char["char_prof_2_v"] /450 *100),
					'char_prof1_image'=>$result_char["char_prof_1_k"],
					'char_prof2_image'=>$result_char["char_prof_2_k"]
				)
			),true);
			
			switch($result_char["char_prof_1_k"])
			{
				case "alchemy":
					$profLang1 = "Alchemie";
					break;
				case "blacksmithing":
					$profLang1 = "Schmiedekunst";
					break;
				case "enchanting":
					$profLang1 = "Verzauberkunst";
					break;
				case "engineering":
					$profLang1 = "Ingenieurskunst";
					break;
				case "herbalism":
					$profLang1 = "Kräuterkunde";
					break;
				case "inscription":
					$profLang1 = "Inschriftenkunde";
					break;
				case "jewelcrafting":
					$profLang1 = "Juwelenschleifen";
					break;
				case "leatherworking":
					$profLang1 = "Lederverarbeitung";
					break;
				case "mining":
					$profLang1 = "Bergbau";
					break;
				case "skinning":
					$profLang1 = "Kürschnerei";
					break;
				case "tailoring":
					$profLang1 = "Schneiderei";
					break;
			}
			
			switch($result_char["char_prof_2_k"])
			{
				case "alchemy":
					$profLang2 = "Alchemie";
					break;
				case "blacksmithing":
					$profLang2 = "Schmiedekunst";
					break;
				case "enchanting":
					$profLang2 = "Verzauberkunst";
					break;
				case "engineering":
					$profLang2 = "Ingenieurskunst";
					break;
				case "herbalism":
					$profLang2 = "Kräuterkunde";
					break;
				case "inscription":
					$profLang2 = "Inschriftenkunde";
					break;
				case "jewelcrafting":
					$profLang2 = "Juwelenschleifen";
					break;
				case "leatherworking":
					$profLang2 = "Lederverarbeitung";
					break;
				case "mining":
					$profLang2 = "Bergbau";
					break;
				case "skinning":
					$profLang2 = "Kürschnerei";
					break;
				case "tailoring":
					$profLang2 = "Schneiderei";
					break;
			}
			$tpl->append('userPage',array(
				'char_detail'=>array(
					'char_prof_1_lang'=>$profLang1,
					'char_prof_2_lang'=>$profLang2,
					'char_talents1_talents'=>$result_char["char_skill_1_1"]." / ".$result_char["char_skill_1_2"]." / ".$result_char["char_skill_1_3"],
					'char_talents2_talents'=>$result_char["char_skill_2_1"]." / ".$result_char["char_skill_2_2"]." / ".$result_char["char_skill_2_3"]
				)
			),true);

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
			$tpl->append('userPage',array(
				'char_detail'=>array(
					'char_talents1_name'=>$talents_name,
					'char_talents1_image'=>$talents_image
				)
			),true);
			
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
			$tpl->append('userPage',array(
				'char_detail'=>array(
					'char_talents2_name'=>$talents_name,
					'char_talents2_image'=>$talents_image,
					'char_2vs2'=>$result_char["char_2vs2_v"],
					'char_3vs3'=>$result_char["char_3vs3_v"],
					'char_5vs5'=>$result_char["char_5vs5_v"]
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