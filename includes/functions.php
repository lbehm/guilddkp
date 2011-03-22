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
					self::generateMenu('last_items');
					//self::generateMenu('last_activities');
				break;
				case 'dkp_ranking':
					self::generateDkpRanking();
				break;
				case 'last_raids':
					self::generateLastRaids();
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
			$quickDKP=$cache->get('user__'.$user->data['user_id'], 'quickDKP');
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
						'dkp'=>(isset($char['dkp']))?$char['dkp']:'0'
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
				$query=$db->query("SELECT r.raid_id, rt.raid_name, rt.raid_short_name, rt.raid_difficult, rt.raid_zone, rt.raid_icon, r.raid_start, r.raid_end, r.raid_note FROM ".T_RAID." r JOIN ".T_RT." rt ON r.raid_type = rt.raid_type ORDER BY r.raid_end DESC LIMIT 5");
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
		function generateDkpRanking()
		{
			global $cache, $db, $tpl, $classes;
			$dkpRank=$cache->get('activities', 'dkpRank');
			if($dkpRank===false)
			{
				$dkpRank=array();
				$query=$db->query("SELECT c.char_id, c.char_name, c.char_class_id, sum(d.dkp) as dkp FROM ".T_CHAR." c JOIN ".T_DKP." d ON c.char_id=d.char_id GROUP BY c.char_id ORDER BY d.dkp DESC");
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
	}
?>
