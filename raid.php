<?php
	/**
	 * raid.php
	 * by devimplode
	 */
	define('loadet', true);
	require_once(dirname(__FILE__).'/common.php');
	require_once('wow_convert.php');
	
	$raidId = $in->get('id',0);
	$query = $db->query("SELECT r.raid_id, rt.raid_name, rt.raid_short_name, r.raid_leader, u.user_displayname as raid_leader_name, rt.raid_difficult, rt.raid_zone, rt.raid_icon, r.raid_start, r.raid_end, r.raid_note FROM (".T_RAID." r JOIN ".T_RT." rt JOIN ".T_USER." u ON r.raid_type = rt.raid_type AND r.raid_leader = u.user_id) WHERE r.raid_id = '".$db->escape($raidId)."';");
	$raid = $db->fetch_record($query);
	if($raid)
	{
		$r_info = array();
		$r_info['id']=$raid['raid_id'];
		$r_info['title']=$raid['raid_name'];
		$r_info['short']=$raid['raid_short_name'];
		$r_info['leader']=$raid['raid_leader'];
		$r_info['leader_name']=$raid['raid_leader_name'];
		$r_info['icon']=$raid['raid_icon'];
		$r_info['difficult']=$raid['raid_difficult'];
		$r_info['start']=date("G:i (d.m.)", $raid['raid_start']);
		$r_info['end']=date("G:i (d.m.)", $raid['raid_end']);
		$r_info['note']=$raid['raid_note'];
		$tpl->append('raidPage',array(
			'info'=>$r_info
		),true);
		$raid_planer=true;
		$kills=array();
		$b_query=$db->query("SELECT b.*, n.name FROM ( ".T_BOSS." b JOIN ".T_NPC." n ON b.npc_id = n.id) WHERE b.raid_id = '".$raid['raid_id']."'");
		while($boss=$db->fetch_record($b_query))
			if($boss['npc_id'])
			{
				$tpl->append('raidPage',array(
					'past'=>true
				),true);
				$raid_planer=false;
				$loot=array();
				$attendees_out=array(
					'dd'=>array(),
					'tank'=>array(),
					'heal'=>array()
				);
				$d_query=$db->query("SELECT AVG(d.dkp) as dkp FROM ".T_DKP." d WHERE dkp_ref='boss' AND dkp_ref_id='".$boss['boss_id']."'");
				$b_dkp=$db->fetch_record($d_query);
				$l_query=$db->query("SELECT l.*, i.name_de as item_name, i.icon, i.Quality, c.char_name, d.dkp FROM (".T_LOOT." l JOIN ".T_ITEMS." i JOIN ".T_CHAR." c JOIN ".T_DKP." d ON l.item_id=i.id AND l.char_id=c.char_id AND l.loot_id=d.dkp_ref_id) WHERE l.boss_id='".$boss['boss_id']."' AND d.dkp_ref='loot'");
				while($items=$db->fetch_record($l_query))
				{
					$loot[]=array(
						'id'=>$items['loot_id'],
						'looter_id'=>$items['char_id'],
						'looter'=>$items['char_name'],
						'item_id'=>$items['item_id'],
						'item_name'=>$items['item_name'],
						'item_quality'=>$items['Quality'],
						'item_icon'=>$items['icon'],
						'dkp'=>$items['dkp']
					);
				}
				$a_query=$db->query("SELECT ba.*, c.char_name, c.char_class_id FROM (".T_BA." ba JOIN ".T_CHAR." c ON ba.char_id=c.char_id) WHERE ba.boss_id='".$boss['boss_id']."'");
				while($attendees=$db->fetch_record($a_query))
				{
					$attendees_out[$attendees['ba_category']][]=array(
						'id'=>$attendees['char_id'],
						'name'=>$attendees['char_name'],
						'note'=>$attendees['ba_note'],
						'icon'=>$classes[$attendees['char_class_id']]['icon'],
					);
				}
				$kills[]=array(
					'id'=>$boss['boss_id'],
					'name'=>$boss['name'],
					'date'=>$boss['boss_time'],
					'difficult'=>$boss['boss_difficult'],
					'dkp'=>round($b_dkp['dkp']),
					'loot'=>$loot,
					'attendees'=>$attendees_out,
				);
			}
		$db->free_result($b_query);

		$tpl->append('raidPage',array(
			'kills'=>$kills
		),true);
		if($raid_planer)
		{
			$ra=array();
			$ra_query=$db->query("SELECT ra.*, c.char_name, c.char_class_id FROM ".T_RA." ra JOIN ".T_CHAR." c ON ra.char_id=c.char_id WHERE raid_id='".$raid['raid_id']."'");
			while($char=$db->fetch_record($ra_query))
			{
				$ra[$char['ra_status']][$char['ra_category']][]=array(
					'id'=>$char['char_id'],
					'name'=>$char['char_name'],
					'note'=>$char['ra_note'],
					'icon'=>$classes[$char['char_class_id']]['icon'],
				);
			}
			$tpl->append('raidPage',array(
				'plan'=>$ra,
				'admin'=>($user->data['auth']['rank_raidplan'] >= 255)?true:false
			),true);

			$db->free_result($ra_query);
		}
		
		$tpl->assign('title',$config->get('title').' - Raid: '.$raid['raid_name']);
	}
	else
	{
		$tpl->assign('title',$config->get('title').' - Raids');
	}
	$tpl->display('raid.tpl');
?>
