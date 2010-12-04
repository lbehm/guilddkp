<?php
	define('loadet', true);
	define('api', true);
	require_once(dirname(__FILE__).'/common.php');

	if($_POST)
	{
		if($user->data['user_id'] != ANONYMOUS)
		{
			$comment_text=$db->sql_escape($in->get('m', ''));
			$comment_page=$db->sql_escape($in->get('p', ''));
			$comment_attach=$db->sql_escape($in->get('a', ''));
			$comment_respond=$db->sql_escape($in->get('r', 0));
			if($comment_text && $comment_text != '')
			{
				$sql = "INSERT INTO `".T_COMMENTS."` (user_id, user_name, comment_date, comment_text, comment_ranking, comment_page, comment_attach_id".(($comment_respond)?", comment_respond_to_id":"").") VALUES ('".$user->data['user_id']."', '".(($user->data['user_displayname']!='')?$user->data['user_displayname'] : $user->data['user_name'])."', '".time()."', '".$comment_text."', 0, '".$comment_page."', '".$comment_attach."'".(($comment_respond)?", '".$comment_respond."'":"").")";
				echo (($db->query($sql))? "Success":"Error");
				$cache->set('comment_last_id_'.$comment_page.'_'.$comment_attach, $db->sql_lastid());
			}
		}
	}
	else
	{
		if($user->check_auth('rank_read_comment'))
		{
			$last_id = $in->get('li', 0);
			$comment_page=$db->sql_escape($in->get('p', ''));
			$comment_attach=$db->sql_escape($in->get('a', ''));
			if($cache->get('comment_last_id_'.$comment_page.'_'.$comment_attach)==$last_id && $last_id)
			{
				$json = array('e'=>1);
				header('Content-Type: application/json; charset=utf8');
				print(json_encode($json));
				die();
			}
			$q_last_id = ($last_id) ? " AND c.comment_id > ".$last_id : "";
			$sql="SELECT c.*, u.user_displayname FROM ".T_COMMENTS." c, ".T_USER." u WHERE c.user_id = u.user_id AND ( c.comment_page = '".$in->get('p')."' OR c.comment_page = 'comment') AND c.comment_attach_id = '".$in->get('a', 0)."'".$q_last_id." ORDER BY c.comment_date ASC;";
			$comment_result = $db->query($sql);
			$comments_counter = 0;
			$comm=array();
			$answ=array();
			while($comments = $db->fetch_record($comment_result))
			{
				$last_id = ($comments['comment_id'] > $last_id)?$comments['comment_id']:$last_id;
				$tmp_comment = array(
					'id' => $comments['comment_id'],
					'u' => ($comments['user_displayname']!='')?$comments['user_displayname']:(($comments['user_name']) ? $comments['user_name'] : "Anonymous"),
					'm' => $comments['comment_text'],
					'r' => $comments['comment_ranking'],
					'd' => date('G:i - d.m.', $comments['comment_date'])
				);
				if($comments['comment_respond_to_id']!='')
				{
					$tmp_comment['re']=$comments['comment_respond_to_id'];
				}
				$comm['data'][$tmp_comment['id']]=$tmp_comment;
			}
			$db->free_result($comment_result);
			$cache->set('comment_last_id_'.$comment_page.'_'.$comment_attach, $last_id);

			if($comm['data'])
				$json=array('li'=>$last_id, 'e'=>0,'d'=>$comm['data']);
			else
				$json = array('e'=>1);
			header('Content-Type: application/json; charset=utf8');
			print(json_encode($json));
		}
	}
?>
