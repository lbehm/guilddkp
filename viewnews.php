<?php
define('loadet', true);
require_once(dirname(__FILE__).'/common.php');

/* **********
 * viewnews.php
 * **********
 */
$newsPerPage = 5;

$start = $in->get('start', 0);
$newsid = $in->get('id', 0);

$total_news = $db->query_first('SELECT count(*) FROM ' . T_NEWS);

if ($newsid)
{
	$sql = 'SELECT n.*, u.user_displayname
			FROM ' . T_NEWS . ' n, ' . T_USER . ' u
			WHERE n.user_id = u.user_id
			AND news_id='.$db->sql_escape($newsid);

	$result = $db->query($sql);
}
else
{
	$previous_date = null;
	$sql = 'SELECT n.*, u.user_displayname
			FROM ' . T_NEWS . ' n, ' . T_USER . ' u
			WHERE (n.user_id = u.user_id)
			ORDER BY news_flags DESC, news_date DESC LIMIT ' . $start . ', '.$newsPerPage;
	$result = $db->query($sql);
	

	if ( $db->num_rows($result) == 0 )
	{
		$sql = 'SELECT n.news_id, n.news_date, n.news_headline, n.news_message, n.user_name, u.user_displayname
				FROM ' . T_NEWS . ' n, ' . T_USER . ' u
				WHERE (n.user_id = u.user_id)
				ORDER BY news_date DESC LIMIT ' . $start . ', '.$newsPerPage;
		$result = $db->query($sql);
		if ( $db->num_rows($result) == 0 )
		{
			die("Keine News");
		}
	}
}
$cur_news_number = 0;
$sticky_news = 0;
$news_array = array();
	   
while( $news = $db->fetch_record($result) )
{	
	$message = $news['news_message'];

	//Details views
	if(($newsid) and (strlen($news['extended_message'])>1))
	{
		$message .= "[br][br]".$news['extended_message'];
	}

	$SHOWCOMMENT = false;
	/*
	if (!$news['nocomments']==1)
	{
		// get the count of comments per news:
		$comm_settings = array('attach_id'=>$news['news_id'], 'page'=>'news');
		$pcomments->SetVars($comm_settings);
		$comcount = $pcomments->Count();
		$COMMENTS_COUNTER = ($comcount == 1 ) ? $comcount.' '.$user->lang['news_comment'] : $comcount.' '.$user->lang['news_comments'] ;
		$COMMENT = $pcomments->Show() ;
		$SHOWCOMMENT = true;
	}
	*/
	$t = time()-$news['news_date'];
	$t = ($t<60000) ? "Vor weniger als einer Minute" : (($t<3600000)?"Vor ".date("i",$t)." Minuten":date("G:i:s - d.m.Y"));
	$news_array[] = array(
		'HEADLINE' => stripslashes($news['news_headline']),
		'AUTHOR' => $news['user_displayname'],
		'TIME' => $t,
		'ID' => $news['news_id'],
		'DETAIL' => ($newsid > 0 ) ? true : false,
		'SHOWCOMMENT' => $SHOWCOMMENT,
		'COMMENTS_COUNTER' => $COMMENTS_COUNTER,
		'COMMENT'  => $COMMENT,
		'MESSAGE' => $message,
		'news' => $news);
		
}			

$db->free_result($result);	
//ok now lets check the cached array
foreach ($news_array as $news)
{
#	if($user->data['user_rank'] < $news['news']['news_permissions'])
#		continue;
	
	$tpl->append('news_obj', 
		array(
			'STICKY' => ($news['news']['news_flags']) ? true : false,
			'HEADLINE' => $news['news']['news_headline'],
			'AUTHOR' => $news['AUTHOR'],
			'TIME' => $news['TIME'],
			'SUBMITTER' => $news['SUBMITTER'] ,
			'SUBMITAT' => $news['SUBMITAT'] ,
			'ID' => $news['ID'],
			'DETAIL' => $news['DETAIL'],
			'SHOWCOMMENT' => $news['SHOWCOMMENT'],
			'COMMENTS_COUNTER' => $news['COMMENTS_COUNTER'],
			'COMMENT'  => $news['COMMENT'],
			'MESSAGE' => $news['MESSAGE']
		)
	);
}


$tpl->assign('title', $config['title'].' - News');
$tpl->display('viewnews.tpl');
?>