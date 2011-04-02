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
	class raidLog
	{
		function parseRaid($str)
		{
			global $zones, $db;
			$xml = simplexml_load_string($str);

			$data=array();
			$difficulty=array(
				1=>'10',
				2=>'10H',
				3=>'25',
				4=>'25H'
			);

			$data['raid_type']=$db->query_first("SELECT raid_type FROM ".T_RT." where raid_name='".$zones[(string)$xml->zone]['difficulty'][(int)$xml->difficulty]['name']."'");
			$data['zone']=$zones[(string)$xml->zone]['difficulty'][(int)$xml->difficulty]['name'];
			$data['zone_short']=$zones[(string)$xml->zone]['difficulty'][(int)$xml->difficulty]['short'];
			$data['zone_id']=$zones[(string)$xml->zone]['zone_id'];
			$data['difficulty']=$difficulty[(int)$xml->difficulty];
			$data['start']=date("d.m.Y (G:i:s)", self::rt2Timestamp($xml->start));
			$data['start_timestamp']=self::rt2Timestamp($xml->start);
			$data['end']=date("d.m.Y (G:i:s)", self::rt2Timestamp($xml->end));
			$data['end_timestamp']=self::rt2Timestamp($xml->end);
			$data['note']=(string)$xml->note;

			$player=array();
			foreach($xml->PlayerInfos->children() as $k=>$v)
			{
				$player[(string)$v->name]=array();
			}
			foreach($xml->BossKills->children() as $k=>$v)
			{
				$boss=false;
				foreach($zones[(string)$xml->zone]['difficulty'][(int)$xml->difficulty]['creatures'] as $npc_id=>$creature)
				{
					if($creature['name_de']==(string)$v->name)
					{
						$boss=array(
							'name'=>$creature['name_de'],
							'id'=>$npc_id,
							'difficulty'=>($difficulty[(int)$v->difficulty])?$difficulty[(int)$v->difficulty]:$difficulty[(int)$xml->difficulty],
							'time'=>date("d.m.Y (G:i:s)", self::rt2Timestamp($v->time)),
							'timestamp'=>self::rt2Timestamp($v->time),
							'dkp'=>$creature['dkp']
						);
					}
				}
				$data['boss'][]=($boss)?$boss:array(
					'unknown'=>true,
					'name'=>(string)$v->name,
					'difficulty'=>(int)$v->difficulty,
					'time'=>date("d.m.Y (G:i:s)", self::rt2Timestamp($v->time)),
					'timestamp'=>self::rt2Timestamp($v->time),
				);
			}
			foreach($xml->Loot->children() as $k=>$v)
			{
				if((string)$v->ItemName!='Emblem des Frosts')
				{
					list($itemid)=explode(':',(string)$v->ItemID);
					$time=array();
					list($time['first'],$time['secnd'])=explode(' ',(string)$v->Time);
					list($time['month'],$time['day'],$time['year'])=explode('/',$time['first']);
					list($time['hour'],$time['minute'],$time['second'])=explode(':',$time['secnd']);
					$loot=array(
						'itemid'=>$itemid,
						'name'=>(string)$v->ItemName,
						'count'=>(string)$v->Count,
						'looter'=>(string)$v->Player,
						'dkp'=>-(string)$v->Costs,
						'note'=>(string)$v->Note,
						'boss'=>(string)$v->Boss,
						'time'=>date("d.m.Y (G:i:s)", mktime($time['hour'],$time['minute'],$time['second'],$time['month'],$time['day'],'20'.$time['year'])),
						'timestamp'=>mktime($time['hour'],$time['minute'],$time['second'],$time['month'],$time['day'],'20'.$time['year'])
					);
					$data['loot'][]=$loot;
					$player[(string)$v->Player]['loot'][]=$loot;
					foreach($data['boss'] as $i=>$boss)
					{
						if($boss['name']==$loot['boss'])
						$data['boss'][$i]['loot'][]=$loot;
					}
				}
			}
			foreach($xml->Join->children() as $k=>$v)
			{
				$time=array();
				list($time['first'],$time['secnd'])=explode(' ',(string)$v->time);
				list($time['month'],$time['day'],$time['year'])=explode('/',$time['first']);
				list($time['hour'],$time['minute'],$time['second'])=explode(':',$time['secnd']);
				$player[(string)$v->player]['attendees'][mktime($time['hour'],$time['minute'],$time['second'],$time['month'],$time['day'],'20'.$time['year'])]='join';
			}
			foreach($xml->Leave->children() as $k=>$v)
			{
				$time=array();
				list($time['first'],$time['secnd'])=explode(' ',(string)$v->time);
				list($time['month'],$time['day'],$time['year'])=explode('/',$time['first']);
				list($time['hour'],$time['minute'],$time['second'])=explode(':',$time['secnd']);
				$player[(string)$v->player]['attendees'][mktime($time['hour'],$time['minute'],$time['second'],$time['month'],$time['day'],'20'.$time['year'])]='leave';
			}
			foreach($player as $n=>$xp)
			{
				if($n!='bank'&&$n!='disenchanted')
				{
					ksort($player[$n]['attendees'],SORT_NUMERIC);
					foreach($data['boss'] as $k=>$boss_a)
					{
						$a=false;
						foreach($player[$n]['attendees'] as $time=>$w)
						{
							if($w=='join' && $boss_a['timestamp']>$time)
								$a=true;
							if($w=='leav' && $boss_a['timestamp']>$time)
								$a=false;
						}
						if($a)
						{
							$player[$n]['boss'][]=$boss_a;
							$data['boss'][$k]['chars'][]=$n;
						}
					}
				}
			}
			$data['chars']=$player;
			return $data;
		}
		function rt2Timestamp($date)
		{
			$time=array();
			list($time['first'],$time['secnd'])=explode(' ',(string)$date);
			list($time['month'],$time['day'],$time['year'])=explode('/',$time['first']);
			list($time['hour'],$time['minute'],$time['second'])=explode(':',$time['secnd']);
			return(mktime($time['hour'],$time['minute'],$time['second'],$time['month'],$time['day'],'20'.$time['year']));
		}
		function js2Timestamp($date)
		{
			$time=array();
			list($time['first'],$time['secnd'])=explode(' (',(string)$date);
			list($time['secnd'])=explode(')',$time['secnd']);
			list($time['day'],$time['month'],$time['year'])=explode('.',$time['first']);
			list($time['hour'],$time['minute'],$time['second'])=explode(':',$time['secnd']);
			return(mktime($time['hour'],$time['minute'],$time['second'],$time['month'],$time['day'],$time['year']));
		}
		function insertRaid($data)
		{
			global $user, $db;
			if($user->data['auth']['rank_power'] < 255)
				die("access denied");
			echo("--load data: ".(string)$data->name."\n");
			if($raidtype_id=$db->query_first("SELECT raid_type FROM ".T_RT." where raid_name='".$db->sql_escape((string)$data->name)."' AND raid_difficult='".$db->sql_escape((string)$data->difficulty)."'"))
			{
				echo("--create raid\n");
				$db->query("INSERT INTO ".T_RAID." (raid_type, raid_leader, raid_start, raid_end, raid_note) VALUES('".$raidtype_id."', '".$user->data['user_id']."', '".$db->sql_escape(self::js2Timestamp($data->start))."', '".$db->sql_escape(self::js2Timestamp($data->end))."', '".$db->sql_escape((string)$data->note)."')");
				$raid_id=$db->insert_id();
				foreach($data->boss as $i=>$boss)
				{
					echo("--create boss: ".$boss->name."\n");
					$db->query("INSERT INTO ".T_BOSS." (raid_id, npc_id, boss_time, boss_difficult) VALUES('".$raid_id."', '".$db->sql_escape($boss->npc_id)."','".$db->sql_escape(self::js2Timestamp($boss->time))."','".$db->sql_escape($boss->difficulty)."')");
					$boss_id=$db->insert_id();
					foreach($boss->char as $j=>$char)
					{
						echo("  |--assign char: ".$char->name."\n");
						$char_id=funct::checkCharName((string)$char->name);
						$db->query("INSERT INTO ".T_BA." (boss_id, char_id, ba_category) VALUES('".$boss_id."', '".$char_id."', '".$db->sql_escape($char->role)."')");
						$db->query("INSERT INTO ".T_DKP." (char_id, dkp_ref, dkp_ref_id, dkp, dkp_time) VALUES('".$char_id."', 'boss', '".$boss_id."', '".$boss->dkp."', '".self::js2Timestamp($boss->time)."')");
					}
					foreach($boss->loot as $j=>$loot)
					{
						echo("  |--assign loot | ".$loot->looter."\n");
						$char_id=funct::checkCharName((string)$loot->looter);
						$db->query("INSERT INTO ".T_LOOT." (char_id, boss_id, item_id, loot_time) VALUES('".$char_id."', '".$boss_id."', '".$db->sql_escape($loot->itemid)."', '".$db->sql_escape(self::js2Timestamp($boss->time))."')");
						$db->query("INSERT INTO ".T_DKP." (char_id, dkp_ref, dkp_ref_id, dkp, dkp_time) VALUES('".$char_id."', 'loot', '".$db->sql_escape($db->insert_id())."', '".$db->sql_escape($loot->dkp)."', '".$db->sql_escape(self::js2Timestamp($boss->time))."')");
					}
				}
				return $sql;
			}
		}
	}
?>
