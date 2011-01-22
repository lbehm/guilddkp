<?php
	if (!defined('loadet'))
	{
		header('HTTP/1.0 404 Not Found');
		exit;
	}

	$usernames = $cache->get('forum', 'usernames');
	if(!$usernames)
	{
		$usernames = array();
		$sql = "SELECT user_id, user_displayname FROM ".T_USER;
		$usernamesquery = $db->query($sql);
		while($record = $db->fetch_record($usernamesquery))
			$usernames[$record['user_id']] = $record['user_displayname'];
		$cache->set('forum', 'usernames', $usernames);
	}
	
	class forum
	{
		private $cache = array(
			'config'	=> array(),
			'forums'	=> array(),
			'topics'	=> array(),
			'posts'		=> array(),
			'struct'	=> array()
		);
		
		function __construct()
		{
			//platzhalter
		}
//ANSICHT von allen FOREN
		function show_all_main()
		{
			//platzhalter
		}
//ANSICHT von einem FORUM
		function show_forum_main($forum_id)
		{
			global $db, $user, $tpl, $usernames, $config;
			
			$fsql="SELECT * FROM ".T_FORUM." f WHERE f.forum_id = '".$forum_id."' AND f.forum_hidden <= '".$user->get_auth('rank_read_forum')."' AND f.forum_delete = '0'";
			$fquery=$db->query($fsql);
			$forum=$db->fetch_record($fquery);
			if(!$forum)
			{
				$tpl->assign('title', $config->get('title').' - Forum - Access denied');
				$tpl->assign('access',false);
				$tpl->display('forum.tpl');
				die();
				// Ende der Fahnenstange
			}
			$tsql="SELECT t.*, u.user_displayname, MD5(u.user_email) as hash, u.user_icon FROM ".T_TOPIC." t, ".T_USER." u WHERE t.forum_id = '".$db->sql_escape($forum_id)."' AND t.topic_hidden <= '".$user->get_auth('rank_read_topic')."' AND t.topic_delete = '0' AND u.user_id = t.topic_last_poster ORDER BY t.topic_edit_timestamp DESC";
			$tquery=$db->query($tsql);
			while($topic=$db->fetch_record($tquery))
			{
				$tpl->append('forum_topics', array(
					'id'=>$topic['topic_id'],
					'title'=>$topic['topic_title'],
					'cleantitle'=>str_replace(array('|',' ','-'),array('','_','_'),$topic['topic_title']),
					'closed'=>$topic['topic_closed'],
					'last_poster'=>$topic['user_displayname'],
					'icon'=>($topic['user_icon']!='')?$topic['user_icon']:"http://www.gravatar.com/avatar/".$topic['hash']."?d=identicon",
					'time'=>date("H:i j.n.",$topic['topic_edit_timestamp'])
				));
			}
			
			$tpl->assign('forum_info', array(
				'forum_id' => $forum['forum_id'],
				'forum_name' => $forum['forum_name'],
				'forum_desc' => $forum['forum_desc'],
				'closed'=>$forum['closed']
			));

			$tpl->assign('title', $config->get('title').' - Forum - '.$topic['topic_title']);
		}
//ANSICHT Thema
		function show_topic_main($topic_id)
		{
			global $db, $user, $tpl, $usernames, $config;
			$tsql="SELECT * FROM ".T_TOPIC." t WHERE t.topic_id = '".$topic_id."' AND t.topic_hidden <= '".$user->get_auth('rank_read_topic')."' AND t.topic_delete = '0'";
			$tquery=$db->query($tsql);
			$topic=$db->fetch_record($tquery);
			if($topic['topic_id']!=$topic_id)
				die();
				//ToDo: Fehlermeldung: Dieses Thema existiert nicht!
			$fsql="SELECT * FROM ".T_FORUM." f WHERE f.forum_id = '".$topic['forum_id']."' AND f.forum_hidden <= '".$user->get_auth('rank_read_forum')."' AND f.forum_delete = '0'";
			$fquery=$db->query($fsql);
			$forum=$db->fetch_record($fquery);
			if(!$forum)
			{
				$tpl->assign('title', $config->get('title').' - Forum - Access denied');
				$tpl->assign('access',false);
				$tpl->display('forum.tpl');
				die();
				// Ende der Fahnenstange
			}
			$psql="SELECT p.*, MD5(u.user_email) as hash, u.user_icon FROM ".T_POST." p, ".T_USER." u WHERE p.post_user_id = u.user_id AND p.topic_id = '".$topic_id."' AND p.post_delete = '0' ORDER BY p.post_timestamp ASC";
			$pquery=$db->query($psql);
			$last_post_id = 0;
			while($post=$db->fetch_record($pquery))
			{
					$tpl->append('forum_posts', array(
						'POST_ID' => $post['post_id'],
						'POST_AUTOR' => $post['post_user_name'],
						'POST_ICON' => ($post['user_icon'])?$post['user_icon']:"http://www.gravatar.com/avatar/".$post['hash']."?d=identicon",
						'POST_DATE' => ' am '.date("d.m.y", $post['post_timestamp']).' um '.date("H:i", $post['post_timestamp']),
						'POST_EDIT_STATUS' => ($post['post_edit_count'] > 0) ? ' - Bearbeitet von '.$post['post_edit_user_name'] : '',
						'POST_TEXT' => bbDeCode(nl2br(stripslashes($post['post_text']))),
						'DELETE_POST' => ($user->check_auth('rank_rm_post'))?true:false,
						'EDIT_POST' => ( ($user->get_auth('rank_edit_post') >= $user->get_auth('rank_edit_post',$post['post_user_id'])) && ($forum['forum_closed'] == '0') && ($topic['topic_closed'] == '0') )?true:false
					));
					$last_post_id = $post['post_id'];
			}

			$tpl->assign('forum_info', array(
				'topic_id' => $topic_id,
				'forum_id' => $forum['forum_id'],
				'forum_cleantitle' => str_replace(array('|',' ','-'),array('','_','_'),$forum['forum_name']),
				'forum_name' => $forum['forum_name'],
				'forum_desc' => $forum['forum_desc'],
				'topic_title' => $topic['topic_title'],
				'topic_cleantitle' => str_replace(array('|',' ','-'),array('','_','_'),$topic['topic_title']),
				'last_post_id' => $last_post_id,
				'TABLE_FOOTER_STATUS' => 'Es wurden '.$topic['post_count'].' Beiträge gefunden.'
			));

			$tpl->assign('title', $config->get('title').' - Forum - '.$topic['topic_title']);
		
		}
		
		function show_topic_api($topic_id, $last_id)
		{
			global $db, $user, $usernames, $config;
			$psql="SELECT p.*, MD5(u.user_email) as hash, u.user_icon FROM ".T_POST." p, ".T_USER." u WHERE p.post_user_id = u.user_id AND p.topic_id = '".$topic_id."' AND p.post_delete = '0' AND p.post_timestamp > (SELECT post_timestamp FROM ".T_POST." WHERE post_id = '".$last_id."') ORDER BY p.post_timestamp ASC";
			$pquery=$db->query($psql);
			$last_post_id = 0;
			while($post=$db->fetch_record($pquery))
			{
				$posts[$post['post_id']] = array(
					'ID' => $post['post_id'],
					'AUTOR' => $post['post_user_name'],
					'ICON' => ($post['user_icon'])?$post['user_icon']:"http://www.gravatar.com/avatar/".$post['hash']."?d=identicon",
					'DATE' => ' am '.date("d.m.y", $post['post_timestamp']).' um '.date("H:i", $post['post_timestamp']),
					'EDITS' => ($post['post_edit_count'] > 0) ? ' - Bearbeitet von '.$post['post_edit_user_name'] : '',
					'TEXT' => bbDeCode(nl2br(stripslashes($post['post_text']))),
					'RM' => ($user->check_auth('rank_rm_post'))?true:false,
					'EDIT' => ( ($user->get_auth('rank_edit_post') >= $user->get_auth('rank_edit_post',$post['post_user_id'])) && ($forum['forum_closed'] == '0') && ($topic['topic_closed'] == '0') )?true:false
				);
				$last_post_id = $post['post_id'];
			}
			header('Content-Type: application/json; charset=utf8');
			print(json_encode(($posts!=null)?array(
				'e'=>0,
				'd'=>$posts,
				'li'=>$last_post_id
			):array('e'=>1)));
		}
//Themenspalte Links
		function generate_menu()
		{
			global $user, $tpl, $db, $usernames;
			$rank_read_forum=$user->get_auth('rank_read_forum');
			if($rank_read_forum===false)
				return('401');
			$rank_read_topic=$user->get_auth('rank_read_topic');
			if($rank_read_topic===false)
				return('401');
			
			//$forum_request = $this->get_forums($rank_read_forum);
			$fsql="SELECT forum_id as id, forum_name as title, forum_desc as `desc`, forum_closed as closed FROM ".T_FORUM." WHERE forum_hidden <= '".$rank_read_forum."' ORDER BY forum_sort ASC";
			$fquery=$db->query($fsql);
			while($forum = $db->fetch_record($fquery))
			{
				$topics_arr=array();
				$topic_counter = 0;
				//$topic_request = $this->get_topics($forum['forum_id'], $rank_read_topic);
				$tsql="SELECT topic_id as id, topic_title as title, forum_id as forum, topic_sticky as sticky, topic_closed as closed, topic_last_poster as last_poster, topic_edit_timestamp as timestamp FROM ".T_TOPIC." WHERE forum_id = '".$forum['id']."' AND topic_hidden <= '".$rank_read_forum."' ORDER BY topic_edit_timestamp DESC";
				$tquery=$db->query($tsql);
				while($topic = $db->fetch_record($tquery))
				{
					$topics_arr[]= array(
						'id'=>$topic['id'],
						'title'=>$topic['title'],
						'cleantitle'=>str_replace(array('|',' ','-'),array('','_','_'),$topic['title']),
						'forum'=>$topic['forum'],
						'sticky'=>$topic['sticky'],
						'closed'=>$topic['closed'],
						'last_poster'=>$usernames[$topic['last_poster']],
						'time'=>date("H:i j.n.",$topic['timestamp'])
					);
				}
				$tpl->append('FORUM_SEC', array(
					'id'=>$forum['id'],
					'title'=>$forum['title'],
					'forum_cleantitle' => str_replace(array('|',' ','-'),array('','_','_'),$forum['title']),
					'desc'=>$forum['desc'],
					'closed'=>$forum['closed'],
					'topics'=>$topics_arr
				));
			}
		}
//Löschen (nur mit "delete" tagn)
		function delete_post($forum_id, $topic_id, $post_id)
		{
			if($this->check_post($forum_id, $topic_id, $post_id))
			{
				global $db, $user;
				if($user->check_auth('a_forum_delete_post'))
				{
					$sql = "UPDATE `eqdkp_fmod_posts` SET `post_delete` = 'Y' WHERE (`topic_id` = '".$topic_id."' AND `post_id` = '".$post_id."') LIMIT 1";
					$db->query($sql);
					$this->log_insert('DELETE_POST', $post_id);
				}
			}
		}

		function delete_topic($forum_id, $topic_id)
		{
			global $db, $user;
			if($this->check_topic_id($forum_id, $topic_id))
			{
				if( $user->check_auth('a_forum_delete_topic') )
				{
					$sql = "UPDATE `eqdkp_fmod_topics` SET `topic_delete` = 'Y' WHERE (`topic_id` = '".$topic_id."' AND `forum_id` = '".$forum_id."') LIMIT 1";
					$db->query($sql);
					// Logging
					$this->log_insert('DELETE_TOPIC', $topic_id);
				}
			}
		}
//schliessen
		function close_toogle_topic($forum_id, $topic_id)
		{
			global $db, $user;
			if($this->check_topic_id($forum_id, $topic_id))
			{
				if( $user->check_auth('a_forum_close_topic') )
				{
					$sql 			= "SELECT `topic_closed` FROM `eqdkp_fmod_topics` WHERE (`topic_id` = '".$topic_id."' AND `forum_id` = '".$forum_id."')";
					$topic_query 	= $db->query($sql);
					$topic 			= $db->fetch_record($topic_query);
					if($topic['topic_closed'] == 'N')
					{
						$sql 					= "UPDATE `eqdkp_fmod_topics` SET `topic_closed` = 'Y' WHERE (`topic_id` = '".$topic_id."' AND `forum_id` = '".$forum_id."') LIMIT 1";
						$this->log_insert('TOPIC_CLOSED', $topic_id);
					}
					elseif($topic['topic_closed'] == 'Y')
					{
						$sql 					= "UPDATE `eqdkp_fmod_topics` SET `topic_closed` = 'N' WHERE (`topic_id` = '".$topic_id."' AND `forum_id` = '".$forum_id."') LIMIT 1";
						$this->log_insert('TOPIC_OPENED', $topic_id);
					}
					$return = ($db->query($sql)) ? true : false;
				}
			}
			return ($return) ? true : false;
		}
//verstecken - hide
		function hide_toogle_topic($forum_id, $topic_id)
		{
			global $db, $user;
			if($this->check_topic_id($forum_id, $topic_id))
			{
				if( $user->check_auth('a_forum_hide_topics') )
				{
					$sql = "SELECT `topic_hidden` FROM `eqdkp_fmod_topics` WHERE (`topic_id` = '".$topic_id."' AND `forum_id` = '".$forum_id."')";
					$topic_query = $db->query($sql);
					$topic = $db->fetch_record($topic_query);
					if($topic['topic_hidden'] == 'N')
					{
						$sql = "UPDATE `eqdkp_fmod_topics` SET `topic_hidden` = 'Y' WHERE (`topic_id` = '".$topic_id."' AND `forum_id` = '".$forum_id."') LIMIT 1";
						$db->query($sql);
						// Logging
						$this->log_insert('TOPIC_HIDE', $topic_id);
						return true;
					}
					elseif($topic['topic_hidden'] == 'Y')
					{
						$sql = "UPDATE `eqdkp_fmod_topics` SET `topic_hidden` = 'N' WHERE (`topic_id` = '".$topic_id."' AND `forum_id` = '".$forum_id."') LIMIT 1";
						$db->query($sql);
						// Logging
						$this->log_insert('TOPIC_SHOW', $topic_id);
						return true;
					}
				}
			}
			return false;
		}

		function hide_toogle_forum($forum_id)
		{
			global $db, $user;
			if($this->check_forum_id($forum_id))
			{
				if( $user->check_auth('a_forum_hide_forum') )
				{
					$sql = "SELECT `forum_hidden` FROM `eqdkp_fmod_forums` WHERE (`forum_id` = '".$forum_id."')";
					$forum_query = $db->query($sql);
					$forum = $this->get_val('forum_dump', $forum_id);
					if($forum['forum']['forum_hidden'] == 'N')
					{
						$sql = "UPDATE `eqdkp_fmod_forums` SET `forum_hidden` = 'Y' WHERE (`forum_id` = '".$forum_id."') LIMIT 1";
						if($db->query($sql))
						{
							$this->log_insert('FORUM_HIDE', $forum_id);
							return true;
						}
					}
					elseif($forum['forum']['forum_hidden'] == 'Y')
					{
						$sql = "UPDATE `eqdkp_fmod_forums` SET `forum_hidden` = 'N' WHERE (`forum_id` = '".$forum_id."') LIMIT 1";
						if($db->query($sql))
						{
							$this->log_insert('FORUM_SHOW', $forum_id);
							return true;
						}
					}
				}
			}
			return false;
		}
//erstellen
		function create_forum($forum_name, $forum_desc, $forum_sort=0)
		{
			global $db, $user;
			if ( $user->check_auth('rank_add_forum') && ($forum_name!='') && ($forum_desc!='') )
			{
				$sql = "SELECT * FROM ".T_FORUM." WHERE forum_name = '".$db->sql_escape($forum_name)."'";
				$forum_query = $db->query($sql);
				$forum = $db->fetch_record($forum_query);
				if(!$forum)
				{
					$timestamp = time();
					$sql = "INSERT INTO `".T_FORUM."` (`forum_name`, `forum_desc`, `forum_sort`) VALUES ('".$db->sql_escape(htmlentities($forum_name))."', '".$db->sql_escape(htmlentities($forum_desc))."', '".$forum_sort."');";
					$db->query($sql);
					return true;
				}
			}
			return false;
		}

		function create_post($topic_id, $text, $sticky)
		{
			global $db, $user;
			if ( $user->check_auth('rank_add_post') && is_numeric($topic_id) )
			{
				$sql = "SELECT * FROM ".T_TOPIC." WHERE topic_id = '".$db->sql_escape($topic_id)."'";
				$topic_query = $db->query($sql);
				$topic = $db->fetch_record($topic_query);
				if($topic['topic_hidden'] <= $user->get_auth('rank_read_topic'))
				{
					$timestamp = time();
					$sql = "INSERT INTO `".T_POST."` (`topic_id`, `post_text`, `post_user_id`, `post_user_name`, `post_sticky`, `post_delete`, `post_timestamp`) VALUES ('".$topic_id."', '".htmlentities($db->sql_escape($text))."', '".$user->data['user_id']."', '".$user->data['user_displayname']."', '".htmlentities($db->sql_escape($sticky))."', 0, '".$timestamp."');";
					$db->query($sql);
					
					$sql = "UPDATE `".T_TOPIC."` SET `topic_last_post_id`='".$db->insert_id()."', `topic_last_poster`='".$user->data['user_id']."', `topic_edit_timestamp`='".$timestamp."' WHERE (`topic_id`='".$topic_id."') LIMIT 1";
					$db->query($sql);
					return true;
				}
			}
			return false;
		}

		function create_topic($forum_id, $topic_name, $text, $sticky = 0, $hidden = 'N', $closed = 'N')
		{
			global $db, $user;
			if ( $user->check_auth('u_forum_create_topic') )
			{
				if($this->check_forum_id($forum_id))
				{
					$sql 			= "SELECT forum_closed FROM eqdkp_fmod_forums WHERE forum_id = ".$forum_id;
					$forum_query 	= $db->query($sql);
					$forum 			= $db->fetch_record($forum_query);

					if($forum['forum_closed'] == 'N')
					{
						$timestamp = time();
						$sql = "INSERT INTO `eqdkp_fmod_topics` (`topic_title`, `forum_id`, `topic_edit_timestamp`, `topic_hidden`, `topic_closed`) VALUES('".$db->sql_escape($topic_name)."', '".$db->sql_escape($forum_id)."', '".$db->sql_escape($timestamp)."', '".$db->sql_escape($hidden)."', '".$db->sql_escape($closed)."')";
						$db->query($sql);
						
						$sql = "SELECT `topic_id` FROM `eqdkp_fmod_topics` WHERE `topic_title` = '".$db->sql_escape($topic_name)."' AND `forum_id` = '".$db->sql_escape($forum_id)."' AND `topic_edit_timestamp` = '".$db->sql_escape($timestamp)."' AND `topic_hidden` = '".$db->sql_escape($hidden)."' AND `topic_closed` = '".$db->sql_escape($closed)."'";
						$topic_id_query = $db->query($sql);
						$topic_id_fetch = $db->fetch_record($topic_id_query);
						$this->build_cache();// sonst kickt der check_post von create_post() uns raus
						$this->create_post($forum_id, $topic_id_fetch['topic_id'], $text, $sticky);
						$this->log_insert('TOPIC_CREATE', $forum_id, array(
							'topic_id'		=> $topic_id_fetch['topic_id'],
							'topic_name'	=> $topic_name,
							'text'			=> $text,
							'sticky'		=> $sticky
						));
						return true;
					}
				}
			}
			return false;
		}
//Bearbeiten
		function edit_post($forum_id, $topic_id, $post_id, $post_text)
		{
			if(! $this->check_post($forum_id, $topic_id, $post_id))
				return false;

			global $db, $user;
			$sql 			= "SELECT * FROM `eqdkp_fmod_posts` WHERE post_id = '".$db->sql_escape($post_id)."' AND post_delete = 'N'";
			$posts_query 	= $db->query($sql);
			$post = $db->fetch_record($posts_query);
			if( ($user->check_auth('a_forum_edit_post')) || ( ($user->check_auth('u_forum_edit_own_post')) && ($post['post_user_id'] == $user->data['user_id'] ) ) )
			{
				$timestamp = time();
				$sql = "UPDATE `eqdkp_fmod_posts` SET `post_text` = '".htmlentities($db->sql_escape($post_text))."', `post_edit_timestamp`='".$timestamp."', `post_edit_user_id`='".$user->data['user_id']."', `post_edit_count`=`post_edit_count`+1 WHERE (`post_id` = '".$db->sql_escape($post_id)."' AND `topic_id` = '".$db->sql_escape($topic_id)."') LIMIT 1";
				$db->query($sql);
				
				$this->log_insert('POST_EDIT', $post_id, array(
					'post_text'		=> htmlentities($db->sql_escape($post_text)),
				));
				return true;
			}
			return false;
		}
//Verschieben
		function move_topic($topic_id, $forum_id)
		{
			if($user->check_auth('a_move_topic'))
			{
				if($this->check_topic_id($forum_id, $topic_id))
				{
					global $db;
					$sql_update_topic = "
						UPDATE `eqdkp_fmod_topics` 
						SET 
							`forum_id` = '".$forum_id."' 
						WHERE
							`topic_id` = '".$topic_id."' 
						LIMIT 
							1;";
					$db->query($sql_update_topic);
					$this->log_insert('TOPIC_MOVE', $topic_id, array(
						'forum_id'		=> $forum_id,
					));
					return true;
				}
			}
			return false;
		}
//diverses
		function error($errorlevel = 500)
		{
			/*
				NEW FEATURE 10/03/18
			*/
			if($errorlevel == 400) $why = "Bad Request";
			elseif($errorlevel == 401) $why = "Unauthorized";
			elseif($errorlevel == 403) $why = "Forbidden";
			elseif($errorlevel == 404) $why = "Not Found";
			elseif($errorlevel == 405) $why = "Method Not Allowed";
			elseif($errorlevel == 406) $why = "Not Acceptable";
			elseif($errorlevel == 409) $why = "Conflict";
			elseif($errorlevel == 500) $why = "Internal Server Error";
			elseif($errorlevel == 501) $why = "Not Implemented";
			elseif($errorlevel == 503) $why = "Service Unavailable";
			
			header("HTTP/1.0 ".$errorlevel." ".$why);
			die($errorlevel." - ".$why);
			exit;
		}
	} // CLASS:FMOD ende
	function short_str($str, $length)
	{
		if(strlen($str) > $length)
			$str = substr($str, 0, $length -3)."...";
		return $str;
	}
?>
