<?php
	/**
	 * item.php
	 * by devimplode
	 */
	define('loadet', true);
	require_once(dirname(__FILE__).'/common.php');
	require_once('wow_convert.php');
	
	$itemId = $in->get('id',0);
	$query = $db->query("SELECT * FROM ".T_ITEMS." WHERE id = '".$db->escape($itemId)."';");
	$item = $db->fetch_record($query);
	if($item)
	{
		if($item['class']==2 || $item['class']==4)
		{
			if($item['class']==2 || $item['InventoryType']==14 || $item['InventoryType']==23)
			{
				$model = array(
					'flash'=>array(
						'model'=>$item['displayid'],
						'type'=>1,
						'contentPath'=>'http://static.wowhead.com/modelviewer/&amp;blur=1,'
					)
				);
			}
			elseif($item['InventoryType']!=2 && $item['InventoryType']!=11 && $item['InventoryType']!=12 && $item['InventoryType']!=28)
			{
				$model = array(
					'flash'=>array(
						'model'=>'humanmale',
						'type'=>16,
						'contentPath'=>'http://static.wowhead.com/modelviewer/&amp;blur=1&equipList='.$item['InventoryType'].','.$item['displayid']
					)
				);
			}
			else
				$model = false;
		}
		/*
		wrong displayid
		elseif($item['class']==15)
			if($item['subclass']==2 || $item['subclass']==5)
				$model = array(
					'flash'=>array(
						'model'=>$item['displayid'],
						'type'=>8,
						'contentPath'=>'http://static.wowhead.com/modelviewer/&amp;blur=1,'
					)
				);*/
		else
			$model = false;

			$bonding = array(
			1 => "Wird beim Aufheben gebunden",
			2 => "Wird beim Anlegen gebunden",
			3 => "Wird beim Benutzen gebunden",
			4 => "Questgegenstand"
		);
		$places = array(
			0=>false,
			1=>'Kopf',
			2=>'Hals',
			3=>'Schulter',
			4=>'Hemd',
			5=>'Brust',
			6=>'Taille',
			7=>'Beine',
			8=>'F&uuml;ße',
			9=>'Handgelenke',
			10=>'H&auml;nde',
			11=>'Finger',
			12=>'Schmuck',
			13=>'Einh&auml;ndig',
			14=>'Schild',
			15=>'Distanz',
			16=>'R&uuml;cken',
			17=>'Zweih&auml;ndig',
			18=>false,
			19=>'Wappenrock',
			20=>'Brust',
			21=>'Waffenhand',
			22=>'Schildhand',
			23=>'In Schildhand gef&uuml;hrt',
			24=>'Projektil',
			25=>'Wurfwaffe',
			26=>'Distanz',
			27=>false,
			28=>'Relikt'
		);
		$dmgType = array(
			0=>'Schaden',
			1=>'Heiligschaden',
			2=>'Feuerschaden',
			3=>'Naturschaden',
			4=>'Frostschaden',
			5=>'Schattenschaden',
			6=>'Arkanschaden'
		);
		$races = array();
		if(($item['AllowableRace'] > 0) && ($item['AllowableRace'] < 8388607))
		{
			if($item['AllowableRace'] & 1)
				$races[] = "Mensch";
			if($item['AllowableRace'] & 2)
				$races[] = "Orc";
			if($item['AllowableRace'] & 4)
				$races[] = "Zwerg";
			if($item['AllowableRace'] & 8)
				$races[] = "Nachtelf";
			if($item['AllowableRace'] & 16)
				$races[] = "Untoter";
			if($item['AllowableRace'] & 32)
				$races[] = "Tauren";
			if($item['AllowableRace'] & 64)
				$races[] = "Gnom";
			if($item['AllowableRace'] & 128)
				$races[] = "Troll";
			if($item['AllowableRace'] & 512)
				$races[] = "Blutelf";
			if($item['AllowableRace'] & 1024)
				$races[] = "Draenei";
		}
		if($races)
			$races_str = implode(", ", $races);
		else
			$races_str = false;

			$classes = array();
		if(($item['AllowableClass'] > -1) && ($item['AllowableClass'] < 2047))
		{
			if($item['AllowableClass'] & 1)
				$classes[] = "Krieger";
			if($item['AllowableClass'] & 2)
				$classes[] = "Paladin";
			if($item['AllowableClass'] & 4)
				$classes[] = "J&auml;ger";
			if($item['AllowableClass'] & 8)
				$classes[] = "Schurke";
			if($item['AllowableClass'] & 16)
				$classes[] = "Priester";
			if($item['AllowableClass'] & 32)
				$classes[] = "Todesritter";
			if($item['AllowableClass'] & 64)
				$classes[] = "Schamane";
			if($item['AllowableClass'] & 128)
				$classes[] = "Magier";
			if($item['AllowableClass'] & 256)
				$classes[] = "Hexenmeister";
			if($item['AllowableClass'] & 512)
				$classes[] = "";
			if($item['AllowableClass'] & 1024)
				$classes[] = "Druide";

		}
		if($classes)
			$class_str = implode(", ", $classes);
		else
			$class_str = false;

		// Read Stats
		$stats = array(
			'name'=>$item['name_de'],
			'quality'=>$item['Quality'],
			'itemlevel'=>$item['ItemLevel'],
			'gearscore'=>round($item['gearscore']),
			'heroic'=>$item['heroic'],
			'cat'=>array(
				$item['class']=>array(
					$item['subclass']=>true
				)
			),
			'slots'=>$item['ContainerSlots'],
			'dmg'=>($item['dmg_max1'])?array(
				'min'=>$item['dmg_min1'],
				'max'=>$item['dmg_max1'],
				'type'=>$dmgType[$item['dmg_type1']],
			):false,
			'speed'=>round(floatval(floatval($item['delay']) / 1000), 2),
			'dps'=>($item['dmg_min1']>0 && $item['dmg_max1']>0 && $item['delay']>0)?(round(floatval((floatval($item['dmg_min1']) + floatval($item['dmg_max1'])) / 2 / floatval($item['delay']) * 1000),1)):false,
			'place'=>$places[$item['InventoryType']],
			'bind'=>$bonding[$item['bonding']],
			'durability'=>$item['MaxDurability'],
			'level'=>$item['RequiredLevel'],
			'skill'=>(isset($skill[$item['RequiredSkill']]['lang']))?$skill[$item['RequiredSkill']]['lang']:false,
			'skillrank'=>$item['RequiredSkillRank'],
			'skillspec'=>(isset($skill[$item['RequiredSkill']]['special'][$item['requiredspell']]))?$skill[$item['RequiredSkill']]['special'][$item['requiredspell']]:false,
			'faction'=>(isset($factions[$item['RequiredReputationFaction']]))?$factions[$item['RequiredReputationFaction']]:false,
			'reputation'=>$reputation[$item['RequiredReputationRank']],
			'races'=>$races_str,
			'classes'=>$class_str,
			'desc'=>$item['description'],
			'price'=>(intval($item['SellPrice']))?array(
				'c'=>intval(substr($item['SellPrice'],-2)),
				's'=>intval(substr($item['SellPrice'],-4,2)),
				'g'=>intval(substr($item['SellPrice'],0,-4))
			):false,
			'amor'=>$item['armor'],
			'block'=>$item['block']
		);

		if($item['StatsCount'])
		{
			for($i=1;$i<=$item['StatsCount'];$i++)
			{
				$stats['stats'][$item['stat_type'.$i]] = $item['stat_value'.$i];
			}
		}
		if($item['Flags'] & 134221824)
			$stats['accbind'] = true;

		if($item['holy_res']>0)
			$stats['res']['holy'] = $item['holy_res'];
		if($item['fire_res']>0)
			$stats['res']['fire'] = $item['fire_res'];
		if($item['nature_res']>0)
			$stats['res']['nature'] = $item['nature_res'];
		if($item['frost_res']>0)
			$stats['res']['frost'] = $item['frost_res'];
		if($item['shadow_res']>0)
			$stats['res']['shadow'] = $item['shadow_res'];
		if($item['arcane_res']>0)
			$stats['res']['arcan'] = $item['arcane_res'];

		$spell_trigger = array(
			0=>"Benutzen",
			1=>"Anlegen",
			2=>"Trefferchance"
		);
		for($i=1;$i<=5;$i++)
			if($item['spellid_'.$i] > 0)
			{
				$query = $db->query("SELECT description FROM ".T_SPELLS." WHERE id = ".$item['spellid_'.$i]);
				$spell = $db->fetch_record($query);
				$stats['spells'][$i]['t'] = $spell_trigger[$item['spelltrigger_'.$i]];
				$stats['spells'][$i]['v'] = $spell['description'];
			}

		$tpl->append('itemPage',array(
			'info'=>array(
				'name'=>$item['name_de'],
				'icon'=>$item['icon'],
				'displayID'=>$item['displayid'],
				'model'=>$model
			)
		),true);
		$tpl->append('item',array(
			'stats'=>$stats
		),true);
		$tpl->assign('title',$config->get('title').' - Gegenstand: '.$item['name_de']);
	}
	$tpl->display('item.tpl');
?>
