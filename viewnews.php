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
	$sql = 'SELECT n.*, u.user_displayname as user_name FROM '.T_NEWS.' n, '.T_USER.' u WHERE n.user_id = u.user_id AND news_id='.$db->sql_escape($newsid);

	$result = $db->query($sql);
}
else
{
	$sql = 'SELECT n.*, u.user_displayname as user_name FROM '.T_NEWS.' n, '.T_USER.' u WHERE n.user_id = u.user_id ORDER BY n.news_flags DESC, n.news_date DESC LIMIT '.$start.', '.intval($newsPerPage).';';
	$result = $db->query($sql);
	if ( $db->num_rows($result) == 0 )
	{
		die("Keine News");
	}
}
$cur_news_number = 0;
$sticky_news = 0;
$news_array = array();
while( $news = $db->fetch_record($result) )
{
	$message = $news['news_message'];

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
	$t = ($t<60) ? "Vor weniger als einer Minute" : (($t<3600)?"Vor ".date("i",$t)." Minuten":date("G:i:s - d.m.Y", $news['news_date']));
	$news_array[] = array(
		'HEADLINE' => stripslashes($news['news_headline']),
		'AUTHOR' => $news['user_name'],
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


$tpl->assign('title', $config->get('title').' - News');
$tpl->display('viewnews.tpl');
?>