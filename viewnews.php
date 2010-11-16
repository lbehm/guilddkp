<?php
define('loadet', true);
require_once(dirname(__FILE__).'/common.php');

/* **********
 * viewnews.php
 * **********
 */

if(defined('CACHING'))
{
$expires = 60*15;
header("Pragma: private");
header("Cache-Control: maxage=".$expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
}
$newsPerPage = 5;

$start = $in->get('start', 0);
$newsid = $in->get('id', 0);

$total_news = $db->query_first('SELECT count(*) FROM ' . T_NEWS);

if ($newsid)
{
	$sql = "SELECT n.*, u.user_displayname as user_name FROM ".T_NEWS." n, ".T_USER." u WHERE n.user_id = u.user_id AND n.news_id='".$db->sql_escape($newsid)."' AND n.news_permissions <= '".$user->get_auth('rank_read_news')."'";

	$result = $db->query($sql);
}
else
{
	$sql = "SELECT n.*, u.user_displayname as user_name FROM ".T_NEWS." n, ".T_USER." u WHERE n.user_id = u.user_id AND n.news_permissions <= '".$user->get_auth('rank_read_news')."' ORDER BY n.news_flags DESC, n.news_date DESC LIMIT ".$start.", ".intval($newsPerPage).";";
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
	if ($news['nocomments']==0 && $newsid)
	{
		if($user->check_auth('rank_read_comment'))
		{
			$sql="SELECT c.*, u.user_displayname FROM ".T_COMMENTS." c, ".T_USER." u WHERE c.user_id = u.user_id AND c.comment_page = 'news' AND c.comment_attach_id = '".$news['news_id']."';";
			$comment_result = $db->query($sql);
			$comments_counter = 0;
			while($comments = $db->fetch_record($comment_result))
			{
				$tpl->append('comments_obj', 
					array(
						'news_id' => $news['news_id'],
						'comment_id' => $comments['comment_id'],
						'user_name' => ($comments['user_displayname']!='')?$comments['user_displayname'] : $comments['user_name'],
						'comment_text' => $comments['comment_text'],
						'comment_ranking' => $comments['comment_ranking'],
						'comment_date' => date('G:i - d.m.', $comments['comment_date'])
					)
				);
				$comments_counter ++;
			}
			$db->free_result($comment_result);
			$tpl->assign('SHOW_COMMENTS', true);
		}
	}

	$t = time()-$news['news_date'];
	$t = ($t<60) ? "Vor weniger als einer Minute" : (($t<3600)?"Vor ".date("i",$t)." Minuten":date("G:i - d.m.Y", $news['news_date']));
	$tpl->append('news_obj', 
		array(
			'STICKY' => ($news['news_flags']) ? true : false,
			'HEADLINE' => stripslashes($news['news_headline']),
			'CLEANTITLE' => str_replace(array('|',' ','-'),array('','_','_'),$news['news_headline']),
			'AUTHOR' => $news['user_name'],
			'TIME' => $t,
			'ID' => $news['news_id'],
			'MESSAGE' => nl2br($news['news_message'])
		)
	);
}
$db->free_result($result);	

$tpl->assign('title', $config->get('title').' - News');
$tpl->display('viewnews.tpl');
?>