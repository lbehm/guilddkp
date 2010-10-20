<?php
	define('loadet', true);
	require_once(dirname(__FILE__).'/common.php');

	if($_POST)
	{
		if($user->data['user_id'] != ANONYMOUS)
		{
			$comment_text=$db->sql_escape($in->get('comment', ''));
			$comment_page=$db->sql_escape($in->get('p_page', ''));
			$comment_attach=$db->sql_escape($in->get('p_attach', ''));
			$comment_respond=$db->sql_escape($in->get('p_respond', 0));
			if($comment_text && $comment_text != '')
			{
				$sql = "INSERT INTO `".T_COMMENTS."` (user_id, user_name, comment_date, comment_text, comment_ranking, comment_page, comment_attach_id".(($comment_respond)?", comment_respond_to_id":"").") VALUES ('".$user->data['user_id']."', '".(($user->data['user_displayname']!='')?$user->data['user_displayname'] : $user->data['user_name'])."', '".time()."', '".$comment_text."', 0, '".$comment_page."', '".$comment_attach."'".(($comment_respond)?", '".$comment_respond."'":"").")";
				$db->query($sql);
			}
		}
		die();
	}

	if($user->check_auth('rank_read_comment'))
	{
		$last_id = $in->get('last_id', 0);
		$q_last_id = ($last_id) ? " AND comment_id > ".$last_id : "";
		
		$sql="SELECT c.*, u.user_displayname FROM ".T_COMMENTS." c, ".T_USER." u WHERE c.user_id = u.user_id AND ( c.comment_page = '".$in->get('page')."' OR c.comment_page = 'comment') AND c.comment_attach_id = '".$in->get('attach', 0)."'".$q_last_id." ORDER BY c.comment_date ASC;";
		$comment_result = $db->query($sql);
		$comments_counter = 0;
		$comm=array();
		$answ=array();
		while($comments = $db->fetch_record($comment_result))
		{
			$comm['last_id'] = ($comments['comment_id'] > $comm['last_id'])?$comments['comment_id']:$comm['last_id'];
			
			$tmp_comment = array(
				'id' => $comments['comment_id'],
				'autor' => ($comments['user_displayname']!='')?$comments['user_displayname']:(($comments['user_name']) ? $comments['user_name'] : "Anonymous"),
				'msg' => $comments['comment_text'],
				'rank' => $comments['comment_ranking'],
				'date' => date('G:i - d.m.', $comments['comment_date'])
			);
			$comm['data'][$tmp_comment['id']]=$tmp_comment;
			if($comments['comment_respond_to_id']!='')
			{
				$comm['data'][$tmp_comment['id']]['respond_to']=$comments['comment_respond_to_id'];
				$comm['data'][$comments['comment_respond_to_id']]['childs'][]=$tmp_comment['id'];
			}
		}
		$db->free_result($comment_result);

		$json = array();
		$json['comm'] = $comm['data'];
		$json['last_id'] = $comm['last_id'];
		if($_GET['debug'])
			print_r($json);
		else
			print(json_encode($json));

	}
?>
