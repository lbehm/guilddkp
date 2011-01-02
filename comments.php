<?php
	define('loadet', true);
	define('api', true);
	require_once(dirname(__FILE__).'/common.php');

	if($_POST)
	{
		if(($user->data['user_id'] != ANONYMOUS) && $user->check_auth('rank_add_comment'))
		{
			if($_POST['s']=='pc')
			{
				$comment_text=$db->sql_escape(htmlentities($in->get('m', '')));
				$comment_page=$db->sql_escape($in->get('p', ''));
				$comment_original_page=$db->sql_escape($in->get('op', ''));
				$comment_attach=$db->sql_escape($in->get('a', ''));
				$comment_respond=$db->sql_escape($in->get('r', 0));
				if($comment_text && $comment_text != '' && str_replace(" ", "", $comment_text) != '')
				{
					$sql = "INSERT INTO `".T_COMMENTS."` (user_id, user_name, comment_date, comment_text, comment_ranking, comment_page, comment_attach_id".(($comment_respond)?", comment_respond_to_id":"").") VALUES ('".$user->data['user_id']."', '".(($user->data['user_displayname']!='')?$user->data['user_displayname'] : $user->data['user_name'])."', '".time()."', '".$comment_text."', 0, '".$comment_page."', '".$comment_attach."'".(($comment_respond)?", '".$comment_respond."'":"").")";
					echo (($db->query($sql))? "Success":"Error");
					if($comment_page != 'comment')
						$cache->set('comment', 'last_id_'.$comment_page.'_'.$comment_attach, (int)$db->sql_lastid());
					else
						$cache->set('comment', 'last_id_'.$comment_original_page.'_'.$comment_attach, (int)$db->sql_lastid());
				}
			}
			elseif($_POST['s']=='v')
			{
				$comment_id = $in->get('i', 0);
				$comment_vote = $in->get('v', 0);
				$votes = unserialize($_COOKIE[$config->get('cookie_name').'_comment_votes']);
				if(isset($votes[$comment_id]))
					die("e.JUST_VOTED");
				$votes[$comment_id] = $comment_vote;
				$user->set_cookie('comment_votes', serialize($votes), 0);
				$db->query("UPDATE `".T_COMMENTS."` SET `comment_ranking` ".(($comment_vote)?'+':'-')." 1 WHERE comment_id = '".$comment_id."';");
				$query = $db->query("SELECT COUNT(`comment_ranking`) as ranking FROM ".T_COMMENTS." WHERE comment_id = '".$comment_id."';");
				$result = $db->fetch_record($query);
				die($result['ranking']);
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
			$limit=$config->get($comment_page.'_limit');
			if(((($cache->get('comment', 'last_id_'.$comment_page.'_'.$comment_attach)) == $last_id) || (($cache->get('comment', 'last_id_comment_'.$comment_attach)) == $last_id)) && ($last_id != false))
			{
				$json = array('e'=>1);
				header('Content-Type: application/json; charset=utf8');
				print(json_encode($json));
				die();
			}
			$q_last_id = ($last_id) ? " AND c.comment_id > ".$last_id : "";
			$sql="SELECT c.*, u.user_displayname, u.user_icon, MD5(u.user_email) as emailHash FROM ".T_COMMENTS." c, ".T_USER." u WHERE c.user_id = u.user_id AND ( c.comment_page = '".$comment_page."' OR c.comment_page = 'comment') AND c.comment_attach_id = '".$comment_attach."'".$q_last_id." ORDER BY c.comment_date ASC LIMIT ".(($limit)?$limit:25).";";
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
					'i' => ($comments['user_icon'] != '')? $comments['user_icon']:"http://www.gravatar.com/avatar/".$comments['emailHash']."?d=identicon",
					'm' => html_entity_decode($comments['comment_text'], ENT_COMPAT, "UTF-8"),
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
			$cache->set('comment', 'last_id_'.$comment_page.'_'.$comment_attach, (int)$last_id);

			if($comm['data'])
				$json=array('li'=>$last_id, 'e'=>0,'d'=>$comm['data']);
			else
				$json = array('e'=>1);
			header('Content-Type: application/json; charset=utf8');
			print(json_encode($json));
		}
	}
?>
