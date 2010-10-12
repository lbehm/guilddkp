<?php
	define('loadet', true);
	require_once(dirname(__FILE__).'/common.php');

	if($_POST)
	{
	$comment=$_POST['comment'];

	}

	if($user->check_auth('rank_read_comment'))
	{
		$q_last_id = ($in->get('last_id', 0)) ? " AND comment_id > ".$last_id : "";
		$sql="SELECT c.*, u.user_displayname FROM ".T_COMMENTS." c, ".T_USER." u WHERE c.user_id = u.user_id AND c.comment_page = '".$in->get('page')."' AND c.comment_attach_id = '".$in->get('attach', 0)."'".$q_last_id." ORDER BY c.comment_date DESC;";
		$comment_result = $db->query($sql);
		$comments_counter = 0;
		$arr=array();
		while($comments = $db->fetch_record($comment_result))
		{
			$arr[] = array(
				'news_id' => $news['news_id'],
				'comment_id' => $comments['comment_id'],
				'user_name' => ($comments['user_displayname']!='')?$comments['user_displayname'] : $comments['user_name'],
				'comment_text' => $comments['comment_text'],
				'comment_ranking' => $comments['comment_ranking'],
				'comment_date' => date('G:i - d.m.', $comments['comment_date'])
			);
			$comments_counter ++;
		}
		$db->free_result($comment_result);
		foreach($arr as $comment)
		{
			echo <<< END
	<li class="comment" style="display:none;"><a name="co_$comment[comment_id]" href="#co_$comment[comment_id]"><span class="comment_head">$comment[user_name] @ $comment[comment_date]</span></a><br />
		<span class="comment_body">$comment[comment_text]</span>
	</li>
END;
		}
		echo '<li style="display:none;" class="last_id">'.$comment['comment_id'].'</li>';
	}

?>
