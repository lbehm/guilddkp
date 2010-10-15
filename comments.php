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
			if($comments['comment_respond_to_id']!='' && $comments['comment_respond_to_id'])
			{
				$answ[$comments['comment_id']] = array(
					'comment_id' => $comments['comment_id'],
					'respond_to' => $comments['comment_respond_to_id'],
					'user_name' => ($comments['user_displayname']!='')?$comments['user_displayname'] : (($comments['user_name']) ? $comments['user_name'] : "Anonymous"),
					'comment_text' => $comments['comment_text'],
					'comment_ranking' => $comments['comment_ranking'],
					'comment_date' => date('G:i - d.m.', $comments['comment_date'])
				);
			}
			else
			{
				$comm[$comments['comment_id']] = array(
					'comment_id' => $comments['comment_id'],
					'user_name' => ($comments['user_displayname']!='')?$comments['user_displayname'] : (($comments['user_name']) ? $comments['user_name'] : "Anonymous"),
					'comment_text' => $comments['comment_text'],
					'comment_ranking' => $comments['comment_ranking'],
					'comment_date' => date('G:i - d.m.', $comments['comment_date'])
				);
			}
			$comments_counter ++;
		}
		$db->free_result($comment_result);

		function print_subanwsers($commarr)
		{
			echo('
	<ul class="comment_anw">');
			global $answ;
			foreach($answ as $anwser)
			{
				if($anwser['respond_to']==$commarr['comment_id'])
				{
					echo <<< END
			<li class="comment" style="display:none;" id="$anwser[comment_id]"><a name="co_$anwser[comment_id]" href="#co_$anwser[comment_id]"><span class="comment_head">$anwser[user_name] @ $anwser[comment_date]</span></a> <a class="anwser_link" href="javascript:anwser_comment($anwser[comment_id]);">Antworten</a><br />
				<span class="comment_body">$anwser[comment_text]</span>
END;
					print_subanwsers($anwser);
					echo <<< END
			</li>
END;
				}
			}
			echo('
		</ul>');
		}

		foreach($comm as $comment)
		{
			echo <<< END
	<li class="comment" style="display:none;" id="$comment[comment_id]"><a name="co_$comment[comment_id]" href="#co_$comment[comment_id]"><span class="comment_head">$comment[user_name] @ $comment[comment_date]</span></a> <a class="anwser_link" href="javascript:anwser_comment($comment[comment_id]);">Antworten</a><br />
		<span class="comment_body">$comment[comment_text]</span>
END;
			print_subanwsers($comment);
			echo <<< END
	</li>
END;

			$last_id = $comment['comment_id'];
		}
	}

?>
