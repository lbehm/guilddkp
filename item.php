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
	if($item = $db->fetch_record($query))
	{
		if($item['category']==2 || $item['category']==4)
		{
			if($item['category']==2 || $item['place']==14 || $item['place']==23)
				$model = array(
					'flash'=>array(
						'model'=>$item['displayid'],
						'type'=>1,
						'contentPath'=>'http://static.wowhead.com/modelviewer/&amp;blur=1,'
					)
				);
			elseif($item['place']!=2 && $item['place']!=11 && $item['place']!=12 && $item['place']!=28)
				$model = array(
					'flash'=>array(
						'model'=>'humanmale',
						'type'=>16,
						'contentPath'=>'http://static.wowhead.com/modelviewer/&amp;blur=1&equipList='.$item['place'].','.$item['displayid']
					)
				);
		}
		else
			$model = false;
		$tpl->append('itemPage',array(
			'info'=>array(
				'name'=>$item['name'],
				'quality'=>$item['quality'],
				'displayID'=>$item['displayid'],
				'model'=>$model
			)
		),true);
		$tpl->assign('title',$config->get('title').' - Gegenstand: '.$item['name']);
	}
	$tpl->display('item.tpl');
?>