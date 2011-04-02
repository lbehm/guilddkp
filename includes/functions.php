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
	class funct
	{
		function generateMenu($c='right_bar')
		{
			switch($c)
			{
				case 'right_bar':
					self::generateMenu('dkp_ranking');
					self::generateMenu('last_raids');
					self::generateMenu('next_raids');
					self::generateMenu('last_items');
					//self::generateMenu('last_activities');
				break;
				case 'dkp_ranking':
					self::generateDkpRanking();
				break;
				case 'last_raids':
					self::generateLastRaids();
				break;
				case 'next_raids':
					self::generateNextRaids();
				break;
				case 'last_items':
					self::generateLastItems();
				break;
				case 'last_activities':

				break;
				default:
					return false;
			}
		}
		function generateQuickDKP()
		{
			global $cache, $user, $db, $tpl, $classes;
			$quickDKP=$cache->get('user_'.$user->data['user_id'], 'quickDKP');
			if($quickDKP===false)
			{
				$quickDKP=array();
				$charQuery=$db->query("SELECT c.char_id, c.char_name, c.char_class_id, sum(d.dkp) as dkp FROM ".T_CHAR." c JOIN ".T_DKP." d ON c.char_id=d.char_id WHERE user_id='".$user->data['user_id']."' GROUP BY c.char_id ORDER BY d.dkp DESC");
				while($char=$db->fetch_record($charQuery))
				{
					$quickDKP[]=array(
						'id'=>$char['char_id'],
						'name'=>$char['char_name'],
						'icon'=>$classes[$char['char_class_id']]['icon'],
						'dkp'=>(isset($char['dkp']))?$char['dkp']:'0',
						'roles'=>$classes[$char['char_class_id']]['roles']
					);

				}
				$db->free_result($charQuery);
				$cache->set('user_'.$user->data['user_id'], 'quickDKP', $quickDKP);
			}
			$tpl->assign('quickDKP', $quickDKP);
		}
		function generateLastItems()
		{
			global $cache, $db, $tpl;
			$lastItems=$cache->get('activities', 'lastItems');
			if($lastItems===false)
			{
				$lastItems=array();
				$query=$db->query("SELECT l.*, i.name_de as item_name, i.icon, i.Quality, c.char_name, d.dkp FROM (".T_LOOT." l JOIN ".T_ITEMS." i JOIN ".T_CHAR." c JOIN ".T_DKP." d ON l.item_id=i.id AND l.char_id=c.char_id AND l.loot_id=d.dkp_ref_id) WHERE d.dkp_ref='loot' ORDER BY l.loot_time DESC LIMIT 5");
				while($item=$db->fetch_record($query))
				{
					$lastItems[]=array(
						'item_id'=>$item['item_id'],
						'item_name'=>$item['item_name'],
						'item_icon'=>$item['icon'],
						'q'=>$item['Quality'],
						'looter_id'=>$item['char_id'],
						'looter'=>$item['char_name'],
						'dkp'=>$item['dkp']
					);
				}
				$db->free_result($query);
				if(!count($lastItems))
					$cache->set('activities', 'lastItems', (int)0);
				else
					$cache->set('activities', 'lastItems', $lastItems);
			}
			$tpl->append('activities', array('lastItems'=>$lastItems), true);
		}
		function generateLastRaids()
		{
			global $cache, $db, $tpl;
			$lastRaids=$cache->get('activities', 'lastRaids');
			if($lastRaids===false)
			{
				$lastRaids=array();
				$query=$db->query("SELECT r.raid_id, rt.raid_name, rt.raid_short_name, rt.raid_difficult, rt.raid_zone, rt.raid_icon, r.raid_start, r.raid_end, r.raid_note FROM ".T_RAID." r JOIN ".T_RT." rt ON r.raid_type = rt.raid_type WHERE r.raid_plan='0' ORDER BY r.raid_end DESC LIMIT 5");
				while($raid=$db->fetch_record($query))
				{
					$lastItems=array();
					$itemQuery=$db->query("SELECT l.*, i.name_de as item_name, i.icon, i.Quality, c.char_name, d.dkp FROM (".T_LOOT." l JOIN ".T_ITEMS." i JOIN ".T_CHAR." c JOIN ".T_DKP." d ON l.item_id=i.id AND l.char_id=c.char_id AND l.loot_id=d.dkp_ref_id) WHERE d.dkp_ref='loot' AND l.boss_id IN (SELECT boss_id FROM ".T_BOSS." WHERE raid_id='".$raid['raid_id']."' ORDER BY boss_time DESC) ORDER BY l.loot_time DESC LIMIT 10");
					while($item=$db->fetch_record($itemQuery))
					{
						$lastItems[]=array(
							'item_id'=>$item['item_id'],
							'item_name'=>$item['item_name'],
							'item_icon'=>$item['icon'],
							'q'=>$item['Quality'],
							'looter_id'=>$item['char_id'],
							'looter'=>$item['char_name'],
							'dkp'=>$item['dkp']
						);
					}
					$lastRaids[]=array(
						'id'=>$raid['raid_id'],
						'name'=>$raid['raid_name'],
						'short_name'=>$raid['raid_short_name'],
						'icon'=>$raid['raid_icon'],
						'difficult'=>$raid['raid_difficult'],
						'date'=>date("d.m. (G:i)", $raid['raid_start']),
						'loot'=>$lastItems
					);
				}
				$db->free_result($query);
				if(!count($lastRaids))
					$cache->set('activities', 'lastRaids', (int)0);
				else
					$cache->set('activities', 'lastRaids', $lastRaids);
			}
			$tpl->append('activities', array('lastRaids'=>$lastRaids), true);
		}
		function generateNextRaids()
		{
			global $cache, $db, $tpl;
			$nextRaids=$cache->get('activities', 'nextRaids');
			if($nextRaids===false)
			{
				$nextRaids=array();
				$query=$db->query("SELECT r.raid_id, rt.raid_name, rt.raid_short_name, rt.raid_difficult, rt.raid_zone, rt.raid_icon, r.raid_start, r.raid_end, r.raid_note FROM ".T_RAID." r JOIN ".T_RT." rt ON r.raid_type = rt.raid_type WHERE r.raid_plan='1' AND r.raid_start > '".time()."' ORDER BY r.raid_start ASC LIMIT 5");
				while($raid=$db->fetch_record($query))
				{
					$ra=array();
					$ra['sign']=$db->query_first("SELECT COUNT(*) FROM ".T_RA." ra WHERE raid_id='".$raid['raid_id']."' AND ra_status='sign'");
					$ra['agree']=$db->query_first("SELECT COUNT(*) FROM ".T_RA." ra WHERE raid_id='".$raid['raid_id']."' AND ra_status='agree'");
					$ra['disagree']=$db->query_first("SELECT COUNT(*) FROM ".T_RA." ra WHERE raid_id='".$raid['raid_id']."' AND ra_status='disagree'");
					$ra['maybe']=$db->query_first("SELECT COUNT(*) FROM ".T_RA." ra WHERE raid_id='".$raid['raid_id']."' AND ra_status='maybe'");
					$nextRaids[]=array(
						'id'=>$raid['raid_id'],
						'name'=>$raid['raid_name'],
						'short_name'=>$raid['raid_short_name'],
						'icon'=>$raid['raid_icon'],
						'difficult'=>$raid['raid_difficult'],
						'date'=>date("d.m. (G:i)", $raid['raid_start']),
						'attendees'=>$ra
					);
				}
				$db->free_result($query);
				if(!count($nextRaids))
					$cache->set('activities', 'nextRaids', (int)0);
				else
					$cache->set('activities', 'nextRaids', $nextRaids, 300);
			}
			$tpl->append('activities', array('nextRaids'=>$nextRaids), true);
		}
		function generateDkpRanking()
		{
			global $cache, $db, $tpl, $classes;
			$dkpRank=$cache->get('activities', 'dkpRank');
			if($dkpRank===false)
			{
				$dkpRank=array();
				$query=$db->query("SELECT c.char_id, c.char_name, c.char_class_id, sum(d.dkp) as dkp FROM ".T_CHAR." c JOIN ".T_DKP." d ON c.char_id=d.char_id GROUP BY c.char_id ORDER BY dkp DESC LIMIT 20");
				while($char=$db->fetch_record($query))
				{
					$dkpRank[]=array(
						'id'=>$char['char_id'],
						'name'=>$char['char_name'],
						'icon'=>$classes[$char['char_class_id']]['icon'],
						'dkp'=>(isset($char['dkp']))?$char['dkp']:'0'
					);
				}
				$db->free_result($query);
				if(!count($dkpRank))
					$cache->set('activities', 'dkpRank', (int)0);
				else
					$cache->set('activities', 'dkpRank', $dkpRank);
			}
			$tpl->append('activities', array('dkpRank'=>$dkpRank), true);
		}
		function getDKP()
		{
			global $config, $db, $classes;
			$out="";
			$out.="--[START]\n";
			$out.="-------------------------------------------------\n";
			$out.="----              dkp_list.lua               ----\n";
			$out.="----  dkp_list.lua is generated from getDKP  ----\n";
			$out.="----  created on: ".date('j.n.Y G:i:s')."\n";
			$out.="----          by: ".$config->get('domain')."\n";
			$out.="-------------------------------------------------\n\n";
			$out.="multiTable = {\n";
			$out.="\t[1] = {\n\t\t[\"dkp\"] = {\n";
			$out.="\t\t\t[\"name\"] = \"dkp\",\n";
			$out.="\t\t\t[\"disc\"] = \"Raid DKP\",\n";
			$out.="\t\t\t[\"events\"] = \"\"\n\t\t},\n\t},\n}\n";
			$out.= "DKPInfo = {\n";
			$out.= "\t[\"date\"] = \"".date('j.n.Y G:i:s')."\",\n";
			$out.= "\t[\"timestamp\"] = \"".time()."\",\n";
			$out.= "\t[\"process_dkp_ver\"] = \"2.65\",\n";
			$out.= "\t[\"total_players\"] = ".$db->query_first("SELECT count(char_id) FROM ".T_CHAR."").",\n";
			$out.= "\t[\"total_items\"] = ".$db->query_first("SELECT count(loot_id) FROM ".T_LOOT."").",\n";
			$out.= "\t[\"total_points\"] = ".$db->query_first("SELECT sum(dkp) FROM ".T_DKP."").",\n";
			$out.= "}\n";
			$out.="gdkp = {\n";
			$out.="\t[\"players\"] = {\n";
			$query=$db->query("SELECT sum(d.dkp) as dkp_current, c.* FROM ".T_CHAR." c JOIN ".T_DKP." d ON c.char_id=d.char_id group by c.char_id");
			while($char=$db->fetch_record($query))
			{
				$out.="\t\t[\"".self::strto_wowutf($char['char_name'])."\"] = {\n";
				$out.="\t\t\t[\"dkp_current\"] = ".$char['dkp_current'].",\n";
				$out.="\t\t\t[\"class\"] = \"".self::strto_wowutf(html_entity_decode($classes[$char['char_class_id']]['name'],ENT_COMPAT,'UTF-8'))."\",\n";
				$out.="\t\t\t[\"rcount\"] = ".$db->query_first("SELECT count(DISTINCT b.raid_id) FROM ".T_BOSS." b JOIN ".T_BA." ba ON b.boss_id=ba.boss_id where ba.char_id='".$char['char_id']."'").",\n";
				$out.="\t\t},\n";
			}
			$db->free_result($query);
			$out.="\t}\n";
			$out.="}\n";
			$out.="DKP_ITEMS = {\n";
			$items=array();
			$query=$db->query("SELECT c.char_name, d.dkp, i.name_de as name FROM ".T_CHAR." c JOIN ".T_DKP." d JOIN ".T_LOOT." l JOIN ".T_ITEMS." i ON  c.char_id=d.char_id AND c.char_id=l.char_id AND l.loot_id=d.dkp_ref_id AND l.item_id=i.id WHERE d.dkp_ref='loot'");
			while($data=$db->fetch_record($query))
			{
				$items[$data['char_name']][]=array(
					'name'=>$data['name'],
					'dkp'=>$data['dkp']
				);
			}
			foreach($items as $char=>$loot)
			{
				$out.="\t[\"".self::strto_wowutf($char)."\"] = {\n";
				$out.="\t\t[\"Items\"] = {\n";
				foreach($loot as $i=>$item)
				{
					$out.="\t\t\t[".$i."] = {\n";
					$out.="\t\t\t\t[\"name\"] = \"".self::strto_wowutf($item['name'])."\",\n";
					$out.="\t\t\t\t[\"dkp\"] = ".$item['dkp']."\n";
					$out.="\t\t\t},\n";
				}
				$out.="\t\t},\n";
				$out.="\t},\n";
			}
			$out.="}\n";
			$out.="-- ItemId deactive\n\n-- No Alliases --\n\n-- RaidPlaner Data deactive\n\n--[END]\n";

			return $out;
		}
		function strto_wowutf($s)
		{
			$f[]='À';
			$f[]='Á';
			$f[]='Â';
			$f[]='Ã';
			$f[]='Ä';
			$f[]='Å';
			$f[]='Æ';
			$f[]='Ç';
			$f[]='È';
			$f[]='É';
			$f[]='Ê';
			$f[]='Ë';
			$f[]='Ì';
			$f[]='Í';
			$f[]='Î';
			$f[]='Ï';
			$f[]='Ð';
			$f[]='Ñ';
			$f[]='Ò';
			$f[]='Ó';
			$f[]='Ô';
			$f[]='Õ';
			$f[]='Ö';
			$f[]='×';
			$f[]='Ø';
			$f[]='Ù';
			$f[]='Ú';
			$f[]='Û';
			$f[]='Ü';
			$f[]='Ý';
			$f[]='Þ';
			$f[]='ß';
			$f[]='à';
			$f[]='á';
			$f[]='â';
			$f[]='ã';
			$f[]='ä';
			$f[]='å';
			$f[]='æ';
			$f[]='ç';
			$f[]='è';
			$f[]='é';
			$f[]='ê';
			$f[]='ë';
			$f[]='ì';
			$f[]='í';
			$f[]='î';
			$f[]='ï';
			$f[]='ð';
			$f[]='ñ';
			$f[]='ò';
			$f[]='ó';
			$f[]='ô';
			$f[]='õ';
			$f[]='ö';
			$f[]='÷';
			$f[]='ø';
			$f[]='ù';
			$f[]='ú';
			$f[]='û';
			$f[]='ü';
			$f[]='ý';
			$f[]='þ';
			$f[]='ÿ';
			$f[]='"';
			$r[]='\195\128';
			$r[]='\195\129';
			$r[]='\195\130';
			$r[]='\195\131';
			$r[]='\195\132';
			$r[]='\195\133';
			$r[]='\195\134';
			$r[]='\195\135';
			$r[]='\195\136';
			$r[]='\195\137';
			$r[]='\195\138';
			$r[]='\195\139';
			$r[]='\195\140';
			$r[]='\195\141';
			$r[]='\195\142';
			$r[]='\195\143';
			$r[]='\195\144';
			$r[]='\195\145';
			$r[]='\195\146';
			$r[]='\195\147';
			$r[]='\195\148';
			$r[]='\195\149';
			$r[]='\195\150';
			$r[]='\195\151';
			$r[]='\195\152';
			$r[]='\195\153';
			$r[]='\195\154';
			$r[]='\195\155';
			$r[]='\195\156';
			$r[]='\195\157';
			$r[]='\195\158';
			$r[]='\195\159';
			$r[]='\195\160';
			$r[]='\195\161';
			$r[]='\195\162';
			$r[]='\195\163';
			$r[]='\195\164';
			$r[]='\195\165';
			$r[]='\195\166';
			$r[]='\195\167';
			$r[]='\195\168';
			$r[]='\195\169';
			$r[]='\195\170';
			$r[]='\195\171';
			$r[]='\195\172';
			$r[]='\195\173';
			$r[]='\195\174';
			$r[]='\195\175';
			$r[]='\195\176';
			$r[]='\195\177';
			$r[]='\195\178';
			$r[]='\195\179';
			$r[]='\195\180';
			$r[]='\195\181';
			$r[]='\195\182';
			$r[]='\195\183';
			$r[]='\195\184';
			$r[]='\195\185';
			$r[]='\195\186';
			$r[]='\195\187';
			$r[]='\195\188';
			$r[]='\195\189';
			$r[]='\195\190';
			$r[]='\195\191';
			$r[]='';
			return str_replace($f,$r,$s);
		}
		function importArsenalChar($charname, $realmname)
		{
			require_once("armory_importer.php");
			return armory_importer::importChar($charname, $realmname);
		}
		function importRaidTracker($str)
		{
			require_once("raidlog_importer.php");
			return(raidLog::parseRaid($str));
		}
		function insertRaid($data)
		{
			require_once("raidlog_importer.php");
			return(raidLog::insertRaid($data));
		}
		function checkCharName($charname)
		{
			global $db;
			$id=$db->query_first("SELECT char_id FROM ".T_CHAR." WHERE char_name='".$db->sql_escape($charname)."'");
			if($id)
				return $id;
			else
				return self::importArsenalChar($charname, 'Acheron');
		}
		function attendRaid($raid_id, $char_id, $status, $role=false)
		{
			global $user, $db;
			if($status=='sign')
			{
				if($user->data['auth']['rank_raidplan'] >= 255)
				{
					$id=$db->query_first("SELECT char_id FROM ".T_CHAR." WHERE char_name='".$db->sql_escape($char_id)."'");
					if($id)
					{
						if($db->query("UPDATE ".T_RA." SET ra_status='".$db->sql_escape($status)."' WHERE raid_id='".$db->sql_escape($raid_id)."' AND char_id='".$id."'"))
						{
							return(array('url'=>'raid-'.$raid_id));
						}
					}
				}
			}
			else
			{
				$id=$db->query_first("SELECT user_id FROM ".T_CHAR." WHERE char_id='".$db->sql_escape($char_id)."'");
				if($id==$user->data['user_id'])
				{
					$db->query("DELETE FROM ".T_RA." WHERE char_id IN (SELECT char_id FROM ".T_CHAR." WHERE user_id='".$id."') AND raid_id='".$db->sql_escape($raid_id)."'");
					if($db->query("INSERT INTO ".T_RA." (raid_id, char_id, ra_status, ra_category, ra_time) VALUES('".$db->sql_escape($raid_id)."', '".$db->sql_escape($char_id)."', '".$db->sql_escape($status)."', '".$db->sql_escape($role)."', '".time()."')"))
						return(array('url'=>'raid-'.$raid_id));
				}
			}
			return 0;
		}
		function modRaidattend($raid_id, $char_id, $status, $role=false)
		{
			global $user, $db;
			if($user->data['auth']['rank_raidplan'] >= 255)
			{
				$id=$db->query_first("SELECT char_id FROM ".T_CHAR." WHERE char_name='".$db->sql_escape($char_id)."'");
				if($id)
				{
					$role_str=($role)?", ra_category='".$db->sql_escape($role)."'":"";
					if($db->query("UPDATE ".T_RA." SET ra_status='".$db->sql_escape($status)."'".$role_str." WHERE raid_id='".$db->sql_escape($raid_id)."' AND char_id='".$id."'"))
					{
						return(array('url'=>'raid-'.$raid_id));
					}
				}
			}
		}
	}
?>
