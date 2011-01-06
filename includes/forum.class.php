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
			//$this->build_cache();
		}
//CONSTRUCT-CACHE
		function build_cache()
		{
		// NEW FEATURE 10/04/19
			global $db, $usernames, $user, $cache;
			/*
			$this->cache = $cache->get('forum', 'cache');
			//var_dump($this->cache);
			if($this->cache)
				return true;
			*/
			$this->cache = array(
				'config'	=> array(),
				'forums'	=> array(),
				'topics'	=> array(),
				'posts'		=> array(),
				'struct'	=> array()
			);
			
		// -------------------------------------------------------------------------------------
		// Foren-cache füllen
		// -------------------------------------------------------------------------------------
		// SQL-Query
			$sql = "SELECT `f`.* FROM `".T_FORUM."` `f`";
			unset($sql_where);
			$sql_where = "";
			if (! $user->check_auth('a_forum_show_hidden_forum') )
				$sql_where .= ($sql_where) ? " AND `f`.`forum_hidden` = 'N'" : " WHERE `f`.`forum_hidden` = 'N'";
			if (! $user->check_auth('a_forum_show_deleted') )
				$sql_where .= ($sql_where) ? " AND `f`.`forum_delete` = 'N'" : " WHERE `f`.`forum_delete` = 'N'";
			$sql = ($sql_where) ? $sql.$sql_where : $sql;
			//$sql .= " ORDER BY forum_sort ASC";
		// Cache-Array generieren
			$forums_query = $db->query($sql);
			while($forums_record = $db->fetch_record($forums_query))
			{
				foreach($forums_record as $key=>$value)
					$this->cache['forums'][$forums_record['forum_id']][$key] = $value;
			}
			
		// -------------------------------------------------------------------------------------
		// Themen-cache
		// -------------------------------------------------------------------------------------
		// SQL-Query
			$sql = "SELECT `t`.* FROM `".T_TOPIC."` `t` join `".T_FORUM."` `f` WHERE `f`.`forum_id` = `t`.`forum_id`";
			unset($sql_where);
			$sql_where = "";
			if (! $user->check_auth('a_forum_show_hidden_forum') )
				$sql_where .= " AND `f`.`forum_hidden` = 'N'";
			if (! $user->check_auth('a_forum_show_hidden_topic') )
				$sql_where .= " AND `t`.`topic_hidden` = 'N'";
			if (! $user->check_auth('a_forum_show_deleted') )
			{
				$sql_where .= " AND `f`.`forum_delete` = 'N'";
				$sql_where .= " AND `t`.`topic_delete` = 'N'";
			}
			$sql = ($sql_where) ? $sql.$sql_where : $sql;
			$sql .= " ORDER BY `t`.`topic_edit_timestamp` DESC";
		// Cache-Array generieren
			$topics_query = $db->query($sql);
			while($topics_record = $db->fetch_record($topics_query))
			{
				foreach($topics_record as $key=>$value)
					$this->cache['topics'][$topics_record['topic_id']][$key] = $value;
			}
			
		// -------------------------------------------------------------------------------------
		// Post-cache
		// -------------------------------------------------------------------------------------
		// SQL-Query
			$sql = "SELECT `p`.* FROM ( `".T_POST."` `p` join `".T_TOPIC."` `t` join `".T_FORUM."` `f` ) WHERE `f`.`forum_id` = `t`.`forum_id` AND `t`.`topic_id` = `p`.`topic_id`";
			unset($sql_where);
			$sql_where = "";
			if (! $user->check_auth('a_forum_show_hidden_forum') )
				$sql_where .= " AND `f`.`forum_hidden` = 'N'";
			if (! $user->check_auth('a_forum_show_hidden_topic') )
				$sql_where .= " AND `t`.`topic_hidden` = 'N'";
			if (! $user->check_auth('a_forum_show_deleted') )
			{
				$sql_where .= " AND `f`.`forum_delete` = 'N'";
				$sql_where .= " AND `t`.`topic_delete` = 'N'";
				$sql_where .= " AND `p`.`post_delete` = 'N'";
			}
			$sql = ($sql_where) ? $sql.$sql_where : $sql;
			$sql .= " ORDER BY `p`.`post_timestamp` ASC";
		// Cache-Array generieren
			$posts_query = $db->query($sql);
			while($posts_record = $db->fetch_record($posts_query))
			{
				foreach($posts_record as $key=>$value)
					$this->cache['posts'][$posts_record['post_id']][$key] = $value;
				$this->cache['posts'][$posts_record['post_id']]['post_username']		= $usernames[$this->cache['posts'][$posts_record['post_id']]['post_user_id']];
				$this->cache['posts'][$posts_record['post_id']]['post_edit_username']	= $usernames[$this->cache['posts'][$posts_record['post_id']]['post_edit_user_id']];
			}
			
		// -------------------------------------------------------------------------------------
		// Struct-cache
		// -------------------------------------------------------------------------------------
		// Cache-Array auswerten und struct aufbauen
			foreach($this->cache['forums'] as $forum_id=>$forum_key)
			{
				if($forum_id != 'config')
				{
					// Forum
					$this->cache['struct'][$forum_id] = array();
					
					foreach($this->cache['topics'] as $topic_id=>$topic_key)
					{
						if($topic_id != 'config' && $this->cache['topics'][$topic_id]['forum_id'] == $forum_id)
						{
							// Topic
							$this->cache['struct'][$forum_id][$topic_id] = array();
							
							$gt_post = array('post_id' => 0,'post_timestamp' => 0);
							$lt_post = array('post_id' => 0,'post_timestamp' => 0);
							foreach($this->cache['posts'] as $post_id=>$post_key)
							{
								if($post_id != 'config' && $this->cache['posts'][$post_id]['topic_id'] == $topic_id)
								{
									// Post
									$this->cache['struct'][$forum_id][$topic_id][$post_id] = $this->cache['posts'][$post_id]['post_user_id'];
									
									if(($this->cache['posts'][$post_id]['post_timestamp'] < $gt_post['post_timestamp']) || ($gt_post['post_timestamp'] == 0))
										$gt_post = $this->cache['posts'][$post_id];
									if($this->cache['posts'][$post_id]['post_timestamp'] > $lt_post['post_timestamp'])
										$lt_post = $this->cache['posts'][$post_id];
								}
							}
							$this->cache['topics'][$topic_id]['first_post'] = $gt_post;
							$this->cache['topics'][$topic_id]['last_post'] = $lt_post;
						}
					}
				}
			}
			//$cache->set('forum', 'cache', $this->cache);
		}
//GET-CACHE-functions
		function get_forums($read, $delete=false)
		{
			/*
			Array
			(
				[1] => Array
				(
					[forum_id] => 1
					[forum_name] => <TITLE>
					[forum_hidden] => N/Y
					[forum_closed] => N/Y
					[forum_delete] => N/Y
				)
				[config] => Array
				(
					[count] => 6
				)
			)
			*/
			$output = array();
			$count = 0;
			foreach($this->cache['struct'] as $forum_id=>$topic_id)
			{
				if(
					($this->cache['forums'][$forum_id]['forum_hidden'] <= $read) &&
					($this->cache['forums'][$forum_id]['forum_delete'] == $delete || $this->cache['forums'][$forum_id]['forum_delete'] == false)
				)
				{
					$output[$forum_id] = $this->cache['forums'][$forum_id];
					$count ++;
				}
			}
			print_r($this->cache['struct']);
			$output['config']['count'] = $count;
			return $output;
		}
		
		function get_topics($forum_id, $read, $delete=false)
		{
			/* RETURN:
			Array
			(
				[4] => Array
				(
					[topic_id] => 4
					[topic_title] => Arbeit f
					[forum_id] => 1
					[topic_edit_timestamp] => 1271060599
					[topic_hidden] => N
					[topic_closed] => N
					[topic_sticky] => N
					[topic_delete] => N
					[first_post] => Array
					(
						[post_id] => 5
						[topic_id] => 4
						[post_user_id] => 1
						[post_timestamp] => 1268579160
						[post_text] => <TEXT>
						[post_sticky] => 0
						[post_delete] => N
						[post_edit_user_id] => 
						[post_edit_count] => 0
						[post_edit_timestamp] => 
						[post_username] => Shiro
						[post_edit_username] => 
					)
					[last_post] => Array
					(
						[post_id] => 179
						[topic_id] => 4
						[post_user_id] => 11
						[post_timestamp] => 1271060599
						[post_text] => <TEXT>
						[post_sticky] => 0
						[post_delete] => N
						[post_edit_user_id] => 
						[post_edit_count] => 0
						[post_edit_timestamp] => 
						[post_username] => Behmo
						[post_edit_username] => 
					)
					[posts_count] => 36
				)
				[config] => Array
				(
					[count] => 3
					[post_count] => 27
				)
			)
			*/
			$output = array();
			$count = 0;
			$forum_posts_count = 0;
			foreach($this->cache['struct'][$forum_id] as $topic_id=>$post)
			{
				if(
					($this->cache['topics'][$topic_id]['topic_hidden'] <= $read) &&
					($this->cache['topics'][$topic_id]['topic_delete'] == $delete || $this->cache['topics'][$topic_id]['topic_delete'] == false)
				)
				{
					$output[$topic_id] = $this->cache['topics'][$topic_id];
					$count ++;
					$topic_post_count = 0;
					foreach($post as $curr_post)
						$topic_post_count ++;
					$output[$topic_id]['posts_count'] = $topic_post_count;
					$forum_posts_count += $topic_post_count;
				}
			}
			$output['config']['count'] = $count;
			$output['config']['post_count'] = $forum_posts_count;
			return $output;
		}
		
		function get_posts($topic_id, $delete)
		{
			/*
			Array
			(
				[<eqdkp_fmod_posts.post_id>] => Array
				(
					[post_id] => <eqdkp_fmod_posts.post_id>
					[topic_id] => <eqdkp_fmod_posts.topic_id>
					[post_user_id] => 11
					[post_timestamp] => 1269712457
					[post_text] => <TEXT>
					[post_sticky] => 0
					[post_delete] => N
					[post_edit_user_id] => 11
					[post_edit_count] => 4
					[post_edit_timestamp] => 1269712945
					[post_username] => Behmo
					[post_edit_username] => Behmo
				)
				[config] => Array
				(
					[count] => 1
					[forum] => Array
					(
						[forum_id] => 4
						[forum_name] => Add-Ons
						[forum_hidden] => N
						[forum_closed] => N
						[forum_delete] => N
					)

					[topic] => Array
					(
						[topic_id] => 3
						[topic_title] => Macros
						[forum_id] => 4
						[topic_edit_timestamp] => 1268576879
						[topic_hidden] => N
						[topic_closed] => N
						[topic_sticky] => N
						[topic_delete] => N
						[first_post] => Array
						(
							[post_id] => 3
							[topic_id] => 3
							[post_user_id] => 11
							[post_timestamp] => 1268576879
							[post_text] => Auch hiervon haben die meisten schon geh
							[post_sticky] => 0
							[post_delete] => N
							[post_edit_user_id] => 
							[post_edit_count] => 0
							[post_edit_timestamp] => 
							[post_username] => Behmo
							[post_edit_username] => 
						)
						[last_post] => Array
						(
							[post_id] => 3
							[topic_id] => 3
							[post_user_id] => 11
							[post_timestamp] => 1268576879
							[post_text] => Auch hiervon haben die meisten schon geh
							[post_sticky] => 0
							[post_delete] => N
							[post_edit_user_id] => 
							[post_edit_count] => 0
							[post_edit_timestamp] => 
							[post_username] => Behmo
							[post_edit_username] => 
						)
					)
				)
			)
			*/
			$output = array();
			$count = 0;
			foreach($this->cache['posts'] as $post_id=>$username)
			{
				if(($this->cache['posts'][$post_id]['topic_id'] == $topic_id) && (($this->cache['posts'][$post_id]['post_delete'] == $delete) || ($delete == '' && $this->cache['posts'][$post_id]['post_delete'] == 'N')))
				{
					$output[$post_id] = $this->cache['posts'][$post_id];
					$count ++;
				}
			}
			$output['config']['count'] = $count;
			$output['config']['forum'] = $this->cache['forums'][$this->cache['topics'][$topic_id]['forum_id']];
			$output['config']['topic'] = $this->cache['topics'][$topic_id];
			return $output;
		}

		function get_val($what, $id)
		{
			if(!$what)
				return false;
			elseif($what == 'forum_dump')
			{
				return array(
					'forum'	=> $this->cache['forums'][$id]
				);
			}
			elseif($what == 'topic_dump')
			{
				$return = $this->cache['topics'][$id];
				return $return;
			}
			elseif($what == 'post_dump')
			{
				return array(
					'post'	=> $this->cache['posts'][$id]
				);
			}
			/*Erweiterbar*/
		}
//CHECK-functions
		function check_post($forum_id, $topic_id, $post_id)
		{
			// UPDATE 10/04/19
			return ((array_key_exists($forum_id, $this->cache['forums'])) && (array_key_exists($topic_id, $this->cache['topics'])) && (array_key_exists($post_id, $this->cache['posts'])) && (array_key_exists($post_id, $this->cache['struct'][$forum_id][$topic_id])));
		}

		function check_topic_id($forum_id, $topic_id)
		{
			global $user;
			return ((array_key_exists($forum_id, $this->cache['forums'])) && (array_key_exists($topic_id, $this->cache['topics'])) && (array_key_exists($topic_id, $this->cache['struct'][$forum_id])) && $this->check_forum_id($forum_id) && ( $user->check_auth('a_forum_show_hidden_topic') || ($this->cache['topics'][$topic_id]['topic_hidden'] == 'N') ) );
		}

		function check_forum_id($forum_id)
		{
			global $user;
			return ( (array_key_exists($forum_id, $this->cache['forums'])) && ($this->cache['forums'][$forum_id]['forum_delete'] == 'N') && (($user->check_auth('a_forum_show_hidden_forum')) || ($this->cache['forums'][$forum_id]['forum_hidden'] == 'N')) );
		}
//Logging
		function make_log_action($action = array())
		{
			$str_action = "\$log_action = array(";
			foreach ( $action as $k => $v )
			{
				$str_action .= "'" . $k . "' => '" . addslashes($v) . "',";
			}
			$action = substr($str_action, 0, strlen($str_action)- 1) . ");";

			// Take the newlines and tabs (or spaces > 1) out of the action
			$action = preg_replace("/[[:space:]]{2,}/", '', $action);
			$action = str_replace("\t", '', $action);
			$action = str_replace("\n", '', $action);
			$action = preg_replace("#(\\\){1,}#", "\\", $action);

			return $action;
		}

		function log_insert($action, $id, $ad = array())
		{
			global $db, $user;
			$log_fields = array('log_id', 'log_date', 'log_type', 'log_action', 'log_ipaddress', 'log_sid', 'log_result', 'admin_id');
			$log_values = array(
				'log_id'        => 'NULL',
				'log_date'      => time(),
				'log_type'      => NULL,
				'log_action'    => NULL,
				'log_ipaddress' => $user->ip_address,
				'log_sid'       => $user->sid,
				'log_result'    => '{L_SUCCESS}',
				'admin_id'      => $user->data['user_id']
			);
			
			if(isset($action))
			{
				$values = array();
				if($action == 'DELETE_POST')
				{
					$values['log_type'] = '{L_ACTION_FORUM_POST_DELETED}';
					$values['log_action']['header'] = '{L_ACTION_FORUM_POST_DELETED}';
					foreach($this->cache['posts'][$id] as $k => $v)
					{
						$values['log_action']['{L_FMOD_POST_'.$k.'}'] = $v;
					}
					foreach($this->cache['topics'][$this->cache['posts'][$id]['topic_id']] as $k => $v)
					{
						$values['log_action']['{L_FMOD_TOPIC_'.$k.'}'] = $v;
					}
					foreach($this->cache['forums'][$this->cache['topics'][$this->cache['posts'][$id]['topic_id']]['forum_id']] as $k => $v)
					{
						$values['log_action']['{L_FMOD_FORUM_'.$k.'}'] = $v;
					}
				}
				elseif($action == 'DELETE_TOPIC')
				{
					$values['log_type'] = '{L_ACTION_FORUM_TOPIC_DELETED}';
					$values['log_action']['header'] = '{L_ACTION_FORUM_TOPIC_DELETED}';
					foreach($this->cache['topics'][$id] as $k => $v)
					{
						$values['log_action']['{L_FMOD_TOPIC_'.$k.'}'] = $v;
					}
					foreach($this->cache['forums'][$this->cache['topics'][$id]['forum_id']] as $k => $v)
					{
						$values['log_action']['{L_FMOD_FORUM_'.$k.'}'] = $v;
					}
				}
				elseif($action == 'DELETE_FORUM')
				{
					$values['log_type'] = '{L_ACTION_FORUM_FORUM_DELETED}';
					$values['log_action']['header'] = '{L_ACTION_FORUM_FORUM_DELETED}';
					foreach($this->cache['forums'][$id] as $k => $v)
					{
						$values['log_action']['{L_FMOD_FORUM_'.$k.'}'] = $v;
					}
				}
				elseif($action == 'TOPIC_CLOSED')
				{
					$values['log_type'] = '{L_ACTION_FORUM_TOPIC_CLOSED}';
					$values['log_action']['header'] = '{L_ACTION_FORUM_TOPIC_CLOSED}';
					foreach($this->cache['topics'][$id] as $k => $v)
					{
						$values['log_action']['{L_FMOD_TOPIC_'.$k.'}'] = $v;
					}
					foreach($this->cache['forums'][$this->cache['topics'][$id]['forum_id']] as $k => $v)
					{
						$values['log_action']['{L_FMOD_FORUM_'.$k.'}'] = $v;
					}
				}
				elseif($action == 'TOPIC_OPENED')
				{
					$values['log_type'] = '{L_ACTION_FORUM_TOPIC_OPENED}';
					$values['log_action']['header'] = '{L_ACTION_FORUM_TOPIC_OPENED}';
					foreach($this->cache['topics'][$id] as $k => $v)
					{
						$values['log_action']['{L_FMOD_TOPIC_'.$k.'}'] = $v;
					}
					foreach($this->cache['forums'][$this->cache['topics'][$id]['forum_id']] as $k => $v)
					{
						$values['log_action']['{L_FMOD_FORUM_'.$k.'}'] = $v;
					}
				}
				elseif($action == 'FORUM_CLOSED')
				{
					$values['log_type'] = '{L_ACTION_FORUM_FORUM_CLOSED}';
					$values['log_action']['header'] = '{L_ACTION_FORUM_FORUM_CLOSED}';
					foreach($this->cache['forums'][$id] as $k => $v)
					{
						$values['log_action']['{L_FMOD_FORUM_'.$k.'}'] = $v;
					}
				}
				elseif($action == 'FORUM_OPENED')
				{
					$values['log_type'] = '{L_ACTION_FORUM_FORUM_OPENED}';
					$values['log_action']['header'] = '{L_ACTION_FORUM_FORUM_OPENED}';
					foreach($this->cache['forums'][$id] as $k => $v)
					{
						$values['log_action']['{L_FMOD_FORUM_'.$k.'}'] = $v;
					}
				}
				elseif($action == 'TOPIC_SHOW')
				{
					$values['log_type'] = '{L_ACTION_FORUM_TOPIC_SHOW}';
					$values['log_action']['header'] = '{L_ACTION_FORUM_TOPIC_SHOW}';
					foreach($this->cache['topics'][$id] as $k => $v)
					{
						$values['log_action']['{L_FMOD_TOPIC_'.$k.'}'] = $v;
					}
					foreach($this->cache['forums'][$this->cache['topics'][$id]['forum_id']] as $k => $v)
					{
						$values['log_action']['{L_FMOD_FORUM_'.$k.'}'] = $v;
					}
				}
				elseif($action == 'TOPIC_HIDE')
				{
					$values['log_type'] = '{L_ACTION_FORUM_TOPIC_HIDE}';
					$values['log_action']['header'] = '{L_ACTION_FORUM_TOPIC_HIDE}';
					foreach($this->cache['topics'][$id] as $k => $v)
					{
						$values['log_action']['{L_FMOD_TOPIC_'.$k.'}'] = $v;
					}
					foreach($this->cache['forums'][$this->cache['topics'][$id]['forum_id']] as $k => $v)
					{
						$values['log_action']['{L_FMOD_FORUM_'.$k.'}'] = $v;
					}
				}
				elseif($action == 'FORUM_SHOW')
				{
					$values['log_type'] = '{L_ACTION_FORUM_FORUM_SHOW}';
					$values['log_action']['header'] = '{L_ACTION_FORUM_FORUM_SHOW}';
					foreach($this->cache['forums'][$id] as $k => $v)
					{
						$values['log_action']['{L_FMOD_FORUM_'.$k.'}'] = $v;
					}
				}
				elseif($action == 'FORUM_HIDE')
				{
					$values['log_type'] = '{L_ACTION_FORUM_FORUM_HIDE}';
					$values['log_action']['header'] = '{L_ACTION_FORUM_FORUM_HIDE}';
					foreach($this->cache['forums'][$id] as $k => $v)
					{
						$values['log_action']['{L_FMOD_FORUM_'.$k.'}'] = $v;
					}
				}
				elseif($action == 'POST_CREATE')
				{
					$values['log_type'] = '{L_ACTION_FORUM_POST_CREATED}';
					$values['log_action']['header'] = '{L_ACTION_FORUM_POST_CREATED}';
					$values['log_action']['{L_FMOD_POST_text}']	= $ad['text'];
					$values['log_action']['{L_FMOD_POST_sticky}']	= $ad['sticky'];
					foreach($this->cache['topics'][$id] as $k => $v)
					{
						$values['log_action']['{L_FMOD_TOPIC_'.$k.'}'] = $v;
					}
					foreach($this->cache['forums'][$this->cache['topics'][$id]['forum_id']] as $k => $v)
					{
						$values['log_action']['{L_FMOD_FORUM_'.$k.'}'] = $v;
					}
				}
				elseif($action == 'TOPIC_CREATE')
				{
					$values['log_type'] = '{L_ACTION_FORUM_TOPIC_CREATED}';
					$values['log_action']['header'] = '{L_ACTION_FORUM_TOPIC_CREATED}';
					$values['log_action']['{L_FMOD_TOPIC_id}']		= $ad['topic_id'];
					$values['log_action']['{L_FMOD_TOPIC_title}']		= $ad['topic_name'];
					$values['log_action']['{L_FMOD_TOPIC_text}']	= $ad['text'];
					$values['log_action']['{L_FMOD_TOPIC_sticky}']	= $ad['sticky'];
					foreach($this->cache['forums'][$id] as $k => $v)
					{
						$values['log_action']['{L_FMOD_FORUM_'.$k.'}'] = $v;
					}
				}
				elseif($action == 'POST_EDIT')
				{
					$values['log_type'] = '{L_ACTION_FORUM_POST_EDIT}';
					$values['log_action']['header'] = '{L_ACTION_FORUM_POST_EDIT}';
					$values['log_action']['{L_FMOD_POST_newPostText}']	= $ad['post_text'];
					foreach($this->cache['posts'][$id] as $k => $v)
					{
						$values['log_action']['{L_FMOD_POST_'.$k.'}'] = $v;
					}
					foreach($this->cache['topics'][$this->cache['posts'][$id]['topic_id']] as $k => $v)
					{
						$values['log_action']['{L_FMOD_TOPIC_'.$k.'}'] = $v;
					}
					foreach($this->cache['forums'][$this->cache['topics'][$this->cache['posts'][$id]['topic_id']]['forum_id']] as $k => $v)
					{
						$values['log_action']['{L_FMOD_FORUM_'.$k.'}'] = $v;
					}
				}
				elseif($action == 'TOPIC_MOVE')
				{
					$values['log_type'] = '{L_ACTION_FORUM_TOPIC_MOVE}';
					$values['log_action']['header'] = '{L_ACTION_FORUM_TOPIC_MOVE}';
					foreach($this->cache['topics'][$id] as $k => $v)
					{
						$values['log_action']['{L_FMOD_TOPIC_'.$k.'}'] = $v;
					}
					foreach($this->cache['forums'][$this->cache['topics'][$id]['forum_id']] as $k => $v)
					{
						$values['log_action']['{L_FMOD_FORUM_'.$k.'}'] = $v;
					}
					foreach($this->cache['forums'][$ad['forum_id']] as $k => $v)
					{
						$values['log_action']['{L_FMOD_NEW_FORUM_'.$k.'}'] = $v;
					}
				}
				else
				{
					return false;
				}

				// If they set the value, we use theirs, otherwise we use the default
				foreach ( $log_fields as $field )
				{
					$values[$field] = ( isset($values[$field]) ) ? $values[$field] : $log_values[$field];

					if ( $field == 'log_action' )
					{
						$values[$field] = $this->make_log_action($values[$field]);
					}
				}

				$query = $db->build_query('INSERT', $values);
				$sql = 'INSERT INTO ' . LOGS_TABLE . $query;

				$db->query($sql);

				return true;
			}
			return false;
		}
//ANSICHT von allen FOREN
		function show_all_main()
		{
			global $user, $tpl;
			
			$colspan = 3;
			$topic_count = 0;
			$forum_count = 0;
			//RECHTE
			if ( $user->check_auth('a_forum_show_hidden_forum') )
				$a_forum_show_hidden_forum = true;
			if ( $user->check_auth('a_forum_hide_forum') )
				$a_forum_hide_forum = true;
			if ( $user->check_auth('a_forum_close_forum') )
				$a_forum_close_forum = true;
			if ( $user->check_auth('a_forum_delete_forum') )
				$a_forum_delete_forum = true;
			if ( $user->check_auth('a_forum_show_hidden_topic') )
				$show_hidden_topic = true;
			if ( $user->check_auth('a_forum_hide_topics') )
				$a_forum_hide_topics = true;
			if ( $user->check_auth('a_forum_delete_topic') )
				$a_forum_delete_topic = true;
			if ( $user->check_auth('a_forum_close_topic') )
				$a_forum_close_topic = true;

			// Foren abrufen
			$hidden = ($a_forum_show_hidden_forum) ? '' : 'N';
			$forum_request = $this->get_forums($hidden, '', '', 'N');
			$move_option = '';
			foreach($forum_request as $forum_option)
			{
				if($forum_option != $forum_request['config'])
				$move_option .= '<option value="'.$forum_option['forum_id'].'">'.$forum_option['forum_name'].'</option>';
			}
			
			$tpl->assign_vars(array(
				'TABLE_COLSPAN' => $colspan,
				'LINK_FORUM_MAIN' => './index.php',
				'LANG_FORUM' => 'Forum &gt;',
				'LANG_HEADLINE_TITEL' => 'Thema',
				'LANG_HEADLINE_LASTCHANGE' => 'Letzte Änderung',
				'LANG_FROM' => 'von: ',
				'LANG_POSTS' => 'Beiträge',
				'LANG_CLOSE' => 'Schliesen',
				'LANG_MOVE' => 'Verschieben',
				'LANG_DELETE' => 'Löschen',
				'LANG_HIDE' => 'Verstecken',
				'LANG_RENAME' => 'Umbenennen',
				'CAN_MOVE_TOPIC' => $user->check_auth('a_forum_move_topic'),
				'MOVE_OPTION' => $move_option
			));
			
			foreach($forum_request as $forum)
			{
				if($forum != $forum_request['config'])
				{
					// EDIT 10/03/19 - Symbole
					$tmp_links = '';
					$tmp_sublink = ($forum['forum_closed'] == 'Y') ? '<img align="right" title="Geschlossen" src="./images/close.png" alt="[ Geschlossen ]" style="margin-top:-2px;" />' : '<img align="right" title="Offen" src="./images/open.png" alt="[ Offen ]" style="margin-top:-2px;" />';
	//				$tmp_links .= ($a_forum_close_forum) ? '<a>'.$tmp_sublink.'</a>' : $tmp_sublink;
					
					$tmp_links .= ($a_forum_delete_forum) ? '<a href="./index.php?forum='.$forum['forum_id'].'&cmd=execute&set=delete_forum"><img alt="[ Löschen ]" title="Forum löschen" src="./images/delete.png" align="right" style="margin-right: 4px; width:15px; height:15px;" /></a>' : '';

					if($a_forum_show_hidden_forum)
					{
						$tmp_sublink = ($forum['forum_hidden'] == 'N') ? ($a_forum_hide_forum) ? '<img align="right" alt="[ Sichtbar ]" src="./images/green.gif" title="Sichtbar" style="width: 11px; height: 11px; margin-right:3px; margin-top:2px;">': '' : '<img align="right" title="Versteckt" src="./images/red.gif" alt="[ Versteckt ]" style="width: 11px; height: 11px; margin-right:3px; margin-top:2px;" />';
						$tmp_links .= ($a_forum_hide_forum) ? '<a href="./index.php?forum='.$forum['forum_id'].'&cmd=execute&set=hide_forum">'.$tmp_sublink.'</a>' : $tmp_sublink;
					}
					
					// HTML: Zeile für Forumsüberschrift
					$tpl->assign_block_vars('main_forum_row', array(
						'LINK_FORUM_ID' => $forum['forum_id'],
						'LINK_FORUM_TITLE' => $forum['forum_name'],
						'LINK_FORUM_ADDITIONS' => '<span align="right" nowrap="nowrap" style="text-align:right;">'.$tmp_links.'</span>'
					));
					
					// Topics abrufen
					$hidden = ($show_hidden_topic) ? '' : 'N';
					$topic_request = $this->get_topics($forum['forum_id'], $hidden, '', '', 'N');
					foreach($topic_request as $topic)
					{
						if($topic != $topic_request['config'])
						{
							if(($topic['topic_hidden'] == 'N') || ($show_hidden_topic))
							{
								
								// HTML: Topic-Zeile Anfang
								$LINK_TOPIC_ADDITIONS = '';
								// Symbole
								$tmp_img = ($topic['topic_closed'] == 'N') ?  '<img title="Offen" src="./images/open.png" alt="Offen" style="margin-right: 4px; width:15px; height:15px;" />' : '<img title="Geschlossen" src="./images/close.png" alt="Geschlossen" style="margin-right: 4px; width:15px; height:15px;" />';
								$LINK_TOPIC_ADDITIONS .= ($a_forum_close_topic) ? '<a href="./index.php?forum='.$forum['forum_id'].'&topic='.$topic['topic_id'].'&cmd=execute&set=close_topic">'.$tmp_img.'</a>' : $tmp_img;

								if ( $a_forum_delete_topic )
									$LINK_TOPIC_ADDITIONS .= '<a href="./index.php?forum='.$forum['forum_id'].'&topic='.$topic['topic_id'].'&cmd=execute&set=delete_topic"><img alt="Delete Topic" title="Thema löschen" src="./images/delete.png" style="margin-right: 4px; width:15px; height:15px;" /></a>';
								if ( $show_hidden_topic )
								{
									$tmp_sublink = ($topic['topic_hidden'] == 'N') ? ($a_forum_hide_topics) ? '<img alt="[ Sichtbar ]" src="./images/green.gif" title="Sichtbar" style="width: 11px; height: 11px; margin-right:3px; margin-top:2px;">': '' : '<img title="Versteckt" src="./images/red.gif" alt="[ Versteckt ]" style="width: 11px; height: 11px; margin-right:3px; margin-top:2px;" />';
									$LINK_TOPIC_ADDITIONS .= ($a_forum_hide_topics) ? '<a href="./index.php?forum='.$forum['forum_id'].'&topic='.$topic['topic_id'].'&cmd=execute&set=hide_topic">'.$tmp_sublink.'</a>' : $tmp_sublink;
								}

								$tpl->assign_block_vars('main_forum_row.topic_row', array(
									'ROWCLASS' => $row,
									'TOPIC_ID' => $topic['topic_id'],
									'TOPIC_TITLE' => $topic['topic_title'],
									'USER_FIRST_POSTER' => $topic['first_post']['post_username'],
									'COUNT_POSTS' => $topic['posts_count'],
									'LINK_TOPIC_ADDITIONS' => $LINK_TOPIC_ADDITIONS,
									'TIMESTAMP_CHANGE' => date("H:i", $topic['topic_edit_timestamp']).' - '.date("d.m.y", $topic['topic_edit_timestamp']),
									'USER_LAST_POSTER' => $topic['last_post']['post_username']
								));
								$topic_count ++;
							}
							
						}
					}
				}
			}
			// return $output;
			
			$tpl->assign_vars(array('TABLE_FOOTER_STATUS' => 'Es wurden '.$topic_count.' Themen in '.$forum_request['config']['count'].' Foren gefunden.'));
		}
//ANSICHT von einem FORUM
		function show_forum_main($forum_id, $forum_arr_name)
		{
			if($this->check_forum_id($forum_id))
			{
				global $user, $tpl;
				$output = '';
				$colspan = 3;
				$topic_count = 0;

				//Rechte
				if ( $user->check_auth('a_forum_show_hidden_topic') )
					$show_hidden_topic = true;
				if ( $user->check_auth('a_forum_hide_topics') )
					$a_forum_hide_topic = true;
				if ( $user->check_auth('a_forum_delete_topic') )
					$a_forum_delete_topic = true;
				if ( $user->check_auth('a_forum_close_topic') )
					$a_forum_close_topic = true;

				$hidden = ($show_hidden_topic) ? '' : 'N';
				$topic_request = $this->get_topics($forum_id, $hidden, '', '', 'N');
				$tpl->assign_vars(array(
					'TABLE_COLSPAN' => $colspan,
					'LINK_FORUM_MAIN' => './index.php',
					'LANG_FORUM' => 'Forum',
					'LINK_FORUM_ID' => $forum_id,
					'LINK_FORUM_TITLE' => $topic_request['config']['forum']['forum_name'],
					'LANG_HEADLINE_TITEL' => 'Thema',
					'LANG_HEADLINE_LASTCHANGE' => 'Letzte Änderung',
					'LANG_FROM' => 'von: ',
					'LANG_POSTS' => 'Beiträge',
					'LANG_NEW_TOPIC' => ' Neues Thema erstellen',
					'LANG_SUBMIT' => '&gt; Erstellen &gt;',
					'SCRIPT_WYSIWYG' => $jqueryp->wysiwyg('bbcode'),
					'LANG_FORUM_CLOSED' => 'Das Forum wurde geschlossen.'
				));
				if( ($user->check_auth('u_forum_create_topic')) && ($topic_request['config']['forum']['forum_closed'] == 'N') )
					$tpl->assign_vars(array('CAN_CREATE_TOPIC' => true, 'CANT_CREATE_TOPIC' => false));
				else
					$tpl->assign_vars(array('CANT_CREATE_TOPIC' => true, 'CAN_CREATE_TOPIC' => false));
				foreach($topic_request as $topic)
				{
					if((($topic['topic_hidden'] == 'N') || ($show_hidden_topic)) && $topic != $topic_request['config'])
					{
						$tmp_img = ($topic['topic_closed'] == 'N') ?  '<img title="Offen" src="./images/open.png" align="right" alt="Offen: " />' : '<img title="Geschlossen" src="./images/close.png" align="right" alt="Geschlossen: " />';
						$LINK_FORUM_ADDITIONS = ($a_forum_close_topic) ? '<a href="./index.php?forum='.$forum_id.'&topic='.$topic['topic_id'].'&cmd=execute&set=close_topic">'.$tmp_img.'</a>' : $tmp_img;

						/*if ( $a_forum_delete_topic )
							$LINK_FORUM_ADDITIONS .= '<a href="./index.php?forum='.$forum_id.'&topic='.$topic['topic_id'].'&cmd=execute&set=delete_topic"><img alt="Delete Topic" title="Thema löschen" src="./images/delete.png" align="right" style="margin-right: 4px; width:15px; height:15px;" /></a>';;
						if ( $show_hidden_topic )
						{
							$tmp_sublink = ($topic['topic_hidden'] == 'N') ? ($a_forum_hide_topic) ? '<img align="right" alt="[ Sichtbar ]" src="./images/green.gif" title="Sichtbar" style="width: 11px; height: 11px; margin-right:3px; margin-top:2px;">': '' : '<img align="right" title="Versteckt" src="./images/red.gif" alt="[ Versteckt ]" style="width: 11px; height: 11px; margin-right:3px; margin-top:2px;" />';
							$LINK_FORUM_ADDITIONS .= ($a_forum_hide_topic) ? '<a href="./index.php?forum='.$forum_id.'&topic='.$topic['topic_id'].'&cmd=execute&set=hide_topic">'.$tmp_sublink.'</a>' : '';
						}*/

						$tpl->assign_block_vars('topic_row', array(
							'TOPIC_ID' => $topic['topic_id'],
							'TOPIC_TITLE' => $topic['topic_title'],
							'USER_FIRST_POSTER' => $topic['first_post']['post_username'],
							'COUNT_POSTS' => $topic['posts_count'],
							'LINK_FORUM_ADDITIONS' => $LINK_FORUM_ADDITIONS,
							'TIMESTAMP_CHANGE' => date("H:i", $topic['topic_edit_timestamp']).' - '.date("d.m.y", $topic['topic_edit_timestamp']),
							'USER_LAST_POSTER' => $topic['last_post']['post_username']
						));
						$topic_count ++;
					}
				}
				$tpl->assign_vars(array('TABLE_FOOTER_STATUS' => 'Es wurden '.$topic_request['config']['post_count'].' Beiträge in '.$topic_request['config']['count'].' Themen gefunden.'));
				$tpl->assign('title', $config->get('title').' - Forum - '.$topic_request['config']['forum']['forum_name']);
			}
		}
//ANSICHT Thema
		function show_topic_main($topic_id)
		{
			global $db, $user, $tpl, $usernames, $config;
			$tsql="SELECT * FROM ".T_TOPIC." t WHERE t.topic_id = '".$topic_id."' AND t.topic_delete = '0'";
			$tquery=$db->query($tsql);
			$topic=$db->fetch_record($tquery);
			if($topic['topic_id']!=$topic_id)
				die();
				//ToDo: Fehlermeldung: Dieses Thema existiert nicht!
			$fsql="SELECT * FROM ".T_FORUM." f WHERE f.forum_id = '".$topic['forum_id']."' AND f.forum_delete = '0'";
			$fquery=$db->query($fsql);
			$forum=$db->fetch_record($fquery);
			
			$tpl->assign('forum_info', array(
				'forum_id' => $forum['forum_id'],
				'forum_name' => $forum['forum_name'],
				'forum_desc' => $forum['forum_desc'],
				'topic_title' => $topic['topic_title'],
				'TABLE_FOOTER_STATUS' => 'Es wurden '.$topic['post_count'].' Beiträge gefunden.'
			));

			$psql="SELECT p.*, MD5(u.user_email) as hash, u.user_icon FROM ".T_POST." p, ".T_USER." u WHERE p.post_user_id = u.user_id AND p.topic_id = '".$topic_id."' AND p.post_delete = '0' ORDER BY p.post_timestamp";
			$pquery=$db->query($psql);
			while($post=$db->fetch_record($pquery))
			{
					$tpl->append('forum_posts', array(
						'POST_ID' => $post['post_id'],
						'POST_AUTOR' => $post['post_user_name'],
						'POST_ICON' => ($post['user_icon'])?$post['user_icon']:"http://www.gravatar.com/avatar/".$post['hash']."?d=identicon",
						'POST_DATE' => ' am '.date("d.m.y", $post['post_timestamp']).' um '.date("H:i", $post['post_timestamp']),
						'POST_EDIT_STATUS' => ($post['post_edit_count'] > 0) ? ' - Bearbeitet von '.$post['post_edit_user_name'] : '',
						'POST_TEXT' => nl2br(stripslashes($post['post_text'])),
						'DELETE_POST' => ($user->check_auth('rank_rm_post'))?true:false,
						'EDIT_POST' => ( ($user->get_auth('rank_edit_post') >= $user->get_auth('rank_edit_post',$post['post_user_id'])) && ($forum['forum_closed'] == '0') && ($topic['topic_closed'] == '0') )?true:false
					));
			}
			if($post_request['config']['forum']['forum_closed'] == 'N' && $post_request['config']['topic']['topic_closed'] == 'N' && $user->check_auth('u_forum_create_post'))
				$tpl->assign(array('CAN_CREATE_POST' => true));
			else
				$tpl->assign(array('CANT_CREATE_POST' => true));
			$tpl->assign('title', $config->get('title').' - Forum - '.$topic['topic_title']);
		
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
				$tsql="SELECT topic_id as id, topic_title as title, forum_id as forum, topic_sticky as sticky, topic_closed as closed, topic_last_poster as last_poster, topic_edit_timestamp as timestamp FROM ".T_TOPIC." WHERE forum_id = '".$forum['id']."' AND topic_hidden <= '".$rank_read_forum."' ORDER BY topic_edit_timestamp ASC";
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
		function create_forum($forum_name)
		{
			$sql = "INSERT INTO `eqdkp_fmod_forums` (forum_name) VALUES('Intern');";
		}

		function create_post($forum_id, $topic_id, $text, $sticky)
		{
			global $db, $user;
			if ( $user->check_auth('u_forum_create_post') )
			{
				if(($this->check_topic_id($forum_id, $topic_id) == true) && is_numeric($forum_id) && is_numeric($topic_id))
				{
					$sql	= "SELECT topic_closed FROM eqdkp_fmod_topics WHERE topic_id = '".$db->sql_escape($topic_id)."'";
					$topic_query = $db->query($sql);
					$topic	= $db->fetch_record($topic_query);
					if($topic['topic_closed'] == 'N')
					{
						$timestamp = time();
						$sql = "INSERT INTO `eqdkp_fmod_posts` (`topic_id`, `post_text`, `post_user_id`, `post_sticky`, `post_timestamp`) VALUES ('".$topic_id."', '".htmlentities($db->sql_escape($text))."', '".$user->data['user_id']."', '".htmlentities($db->sql_escape($sticky))."', '".$timestamp."')";
						$db->query($sql);
						$sql = "UPDATE `eqdkp_fmod_topics` SET `topic_edit_timestamp`='".$timestamp."' WHERE (`topic_id`='".$topic_id."') LIMIT 1";
						$db->query($sql);
						$this->log_insert('POST_CREATE', $topic_id, array(
							'text'		=> htmlentities($db->sql_escape($text)),
							'sticky'	=> htmlentities($db->sql_escape($sticky))
						));
						return true;
					}
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
