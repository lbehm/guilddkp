<?php
if ( !defined('intern') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

class Session
{
    var $sid          = 0;                      // Session ID       @var sid
    var $data         = array();                // Data array       @var data
    var $browser      = '';                     // User agent       @var browser
    var $ip_address   = 0;                      // User IP          @var ip_address
    var $current_page = '';                     // Last Page        @var current_page

    function start()
    {
        global $db, $config, $tpl;

        $current_time = time();

        $this->ip_address   = ( !empty($_SERVER['REMOTE_ADDR']) )     ? $_SERVER['REMOTE_ADDR']     : $REMOTE_ADDR;
        $this->browser      = ( !empty($_SERVER['HTTP_USER_AGENT']) ) ? $_SERVER['HTTP_USER_AGENT'] : $_ENV['HTTP_USER_AGENT'];
        $this->current_page = ( !empty($_SERVER['REQUEST_URI']) )     ? $_SERVER['REQUEST_URI']     : $_SERVER['SCRIPT_NAME'] . (( isset($_SERVER['QUERY_STRING']) ) ? '?' . $_SERVER['QUERY_STRING'] : '');
        $this->current_page = preg_replace('#^.*?/?.*?/?([a-z\_\-]+?)\.php\?' . URI_SESSION . '=.*?(&.*)?$#', '\1\2', $this->current_page);

        // Check for cookie'd session data
        $cookie_data         = array();
        $cookie_data['sid']  = $this->get_cookie('sid');

		if(isset($cookie_data['sid']))
            $this->sid = $cookie_data['sid'];
        else
            $this->sid = '';

        if(!empty($this->sid))
        {
			$sql = "SELECT u.*, s.* FROM " . T_SESSIONS . " s, " . T_USER . " u WHERE s.session_id = '".$db->sql_escape($this->sid)."' AND u.user_id = s.session_user_id";
            $result = $db->query($sql);
            $this->data = $db->fetch_record($result);
            $db->free_result($result);

			// Did the session exist in the DB?
            if ( isset($this->data['user_id']) )
            {
                // Validate IP length
                $s_ip = implode('.', array_slice(explode('.', $this->data['session_ip']), 0, 4));
                $u_ip = implode('.', array_slice(explode('.', $this->ip_address), 0, 4));

                if ( $u_ip == $s_ip )
                {
                    // Only update session DB a minute or so after last update or if page changes
                    if ( ($current_time - $this->data['session_current'] > 60) || (($this->data['session_page'] != $this->current_page)&&(!defined('api'))))
                    {
                        $sql = "UPDATE ".T_SESSIONS." SET session_last_visit = '".$this->data['session_current']."', session_current = '".$current_time."'".((!defined('api'))?", session_page = '".$db->escape($this->current_page)."'":"")." WHERE session_id = '".$this->sid."'";
                        $db->query($sql);
						$this->set_cookie('sid', $this->sid, $current_time + $config->get('session_length'));
                    }
					if(!defined('api') && $this->data['user_id']!=ANONYMOUS)
						$tpl->assign('LOGIN', true);
                    return true;
                }
            }
        }
        // If we reach here then no (valid) session exists.  So we'll create a new one,
        // using the cookie user_id if available to pull basic user prefs.
        // Prevent security vulnerability
        return $this->create(ANONYMOUS);
    }

    function create($user_id)
    {
		global $db, $config;

		$current_time = time();

		// Remove old sessions and update user information if necessary.
		if ( $current_time - $config->get('session_cleanup') > $config->get('session_last_cleanup') )
		{
			$this->cleanup($current_time);
		}
		if(!empty($this->sid))
        {
			$this->destroy($this->sid);
		}

		// Create or update the session
		$this->sid = md5(uniqid(mt_rand(), true));
		$query = $db->build_query('INSERT', array(
			'session_id'         => $this->sid,
			'session_user_id'    => $user_id,
			'session_last_visit' => $current_time,
			'session_start'      => $current_time,
			'session_current'    => $current_time,
			'session_page'       => $db->escape($this->current_page),
			'session_ip'         => $this->ip_address
		));
		
		// Grab user data
		$db->query('INSERT INTO ' . T_SESSIONS . $query);
		$sql = "SELECT u.*, s.* FROM " . T_SESSIONS . " s, " . T_USER . " u WHERE s.session_id = '".$this->sid."' AND u.user_id = s.session_user_id";
		$result = $db->query($sql);
		$this->data = $db->fetch_record($result);
		$db->free_result($result);

		$this->set_cookie('sid', $this->sid, $current_time + $config->get('session_length'));
		return true;
    }

    function destroy($sid = false)
    {
        global $db;
        $current_time = time();
		$sid = ($sid)?$sid:$this->sid;

        // Update User
		$db->query("UPDATE ".T_USER." SET user_lastvisit='".intval($this->data['session_current'])."' WHERE user_id=(SELECT session_user_id FROM ".T_SESSIONS." WHERE session_id='".$sid."')");
        // Delete existing session
        $db->query("DELETE FROM ".T_SESSIONS." WHERE session_id='".$sid."'");

        $this->sid = '';
        return true;
    }

	function cleanup(&$current_time)
	{
		global $db, $config;

		// Get expired sessions, only most recent for each user
		$sql = 'SELECT session_user_id, session_page, MAX(session_current) AS recent_time
				FROM ' . T_SESSIONS . '
				WHERE session_current < ' . ($current_time - $config->get('session_length')) . '
				GROUP BY session_user_id, session_page';
		$result = $db->query($sql);

		$del_user_id  = '';
		$del_sessions = 0;
		if ( $row = $db->fetch_record($result) )
		{
			do
			{
				$sql = 'UPDATE ' . T_USER . " SET user_lastvisit='" . $row['recent_time'] . "', user_lastpage='" . $db->escape($row['session_page']) . "' WHERE user_id = '" . $row['session_user_id'] . "'";
				$db->query($sql);
				$del_user_id .= ( ($del_user_id != '') ? ', ' : '') . " '" . $row['session_user_id'] . "'";
				$del_sessions++;
			}
			while ( $row = $db->fetch_record($result) );
		}

		if($del_user_id != '')
		{
			// Delete expired sessions
			$sql = 'DELETE FROM ' . T_SESSIONS . "
					WHERE session_user_id IN ($del_user_id)
					AND session_current < " . ($current_time - $config->get('session_length'));
			$db->query($sql);
		}

		if($del_sessions<5)
		{
			// Less than 5 sessions, update gc timer
			// Otherwise we want cleanup called again to delete other sessions
			$config->put('session_last_cleanup', $current_time);
		}
	}

    function get_cookie($name)
    {
        global $config;
		$cookie_name = $config->get('cookie_name') . '_' . $name;
		return ( isset($_COOKIE[$cookie_name]) ) ? $_COOKIE[$cookie_name] : '';
    }

    function set_cookie($name, $cookie_data, $cookie_time)
    {
        global $config;
		setcookie($config->get('cookie_name') . '_' . $name, $cookie_data, $cookie_time, $config->get('cookie_path'), $config->get('cookie_domain'));
    }
}

/**
* User Class
*
* Stores user/global preferences
* and language data
*/

class User extends Session
{
    var $lang      = array();               // Loaded language pack     @var lang
    var $lang_name = '';                    // Pack name (ie 'English') @var lang_name
    var $lang_path = '';                    // Language path            @var lang_path
    var $style     = array();               // Style data               @var style

    /**
    * Sets up user language and style settings
    *
    * @param $lang_set Language to set
    * @param $style Style ID to set
    */
    function setup($lang_set = false, $style = false)
    {
        global $db, $tpl, $config, $root_dir;

        $lang_root_dir = $root_dir.'/includes/lang/';
		// Set up language array
        if( (isset($this->data['user_id'])) && ($this->data['user_id'] != ANONYMOUS) && (!empty($this->data['user_lang'])) )
            $this->lang_name = (file_exists($lang_root_dir.$this->data['user_lang'].'lang_main.php') && is_dir($lang_root_dir.$this->data['user_lang']) ) ? $this->data['user_lang'] : $config->get('default_lang');
        else
            $this->lang_name = $config->get('default_lang');
        $this->lang_path = $lang_root_dir.$this->lang_name.'/';

        include($this->lang_path . 'lang_main.php');
        if(defined('IN_ADMIN'))
            include($this->lang_path . 'lang_admin.php');
        
        $this->lang = &$lang; // Import der $lang-files

        if ($this->lang_name=='de_de')
        {
        	setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');
        	$this->style['date_notime_long']	= ($this->lang['style_date_notime_long']) ? $this->lang['style_date_notime_long'] : 'j F, Y';
        	$this->style['date_notime_short']	= ($this->lang['style_date_notime_short']) ? $this->lang['style_date_notime_short'] : 'd.m.y';
        	$this->style['date_time']			= ($this->lang['style_date_time']) ? $this->lang['style_date_time'] : 'd.m.y H:i';
        	$this->style['time']				= ($this->lang['style_time']) ? $this->lang['style_time'] : 'H:i';
        	$this->style['strtime_date']		= ($this->lang['style_strtime_date']) ? $this->lang['style_strtime_date'] : '%A %d.%B %Y';
        	$this->style['strtime_date_short']  = ($this->lang['style_strtime_date_short']) ? $this->lang['style_strtime_date_short'] : '%a %d.%m %H:%M';

        }else {
        	$this->style['date_notime_long']	= ($this->lang['style_date_notime_long']) ? $this->lang['style_date_notime_long'] : 'F j, Y' ;
        	$this->style['date_notime_short']	= ($this->lang['style_date_notime_short']) ? $this->lang['style_date_notime_short'] : 'm/d/y' ;
        	$this->style['date_time']			= ($this->lang['style_date_time']) ? $this->lang['style_date_time'] : 'd.m.y h:ia T' ;
        	$this->style['time']				= ($this->lang['style_time']) ? $this->lang['style_time'] : 'h:ia';
        	$this->style['strtime_date']		= ($this->lang['style_strtime_date']) ? $this->lang['style_strtime_date'] : '%A %B %d %Y';
        	$this->style['strtime_date_short']  = ($this->lang['style_strtime_date_short']) ? $this->lang['style_strtime_date_short'] : '%a %m.%d %I:%M %p';
        }

        //$tpl->setTpl(($this->style['template_path'] && (($this->style['template_path'])!=''))?($this->style['template_path']):$config->get('default_template'));

        //
        // Permissions
        //
        $this->data['auth'] = array();
        if ( $this->data['user_id'] == ANONYMOUS )
        {
            // Get the default permissions if they're not logged in
            $sql = "SELECT * FROM " . T_RANKS_RIGHTS . " WHERE rank_id=(SELECT rank_id FROM ".T_RANKS." WHERE rank_name='Guest');";
        }
        else
        {
            $sql = "SELECT * FROM " . T_RANKS_RIGHTS . " WHERE rank_id = (SELECT user_rank FROM ".T_USER." WHERE user_id= ".$this->data['user_id'].")";
        }
        if ( !($result = $db->query($sql)) )
        {
            die('Could not obtain permission data');
        }
        if( $row = $db->fetch_record($result) )
        {
            foreach($row as $right=>$value)
				$this->data['auth'][$right] = $value;
        }
		$db->free_result($result);
        return;
    }

    /**
    * Checks if a user has permission ($p_need) to do ($right_option)
    *
    * @param $right_option Permission we want to check
    * @param $p_need Needed power to return true
	* @param $user_id If set, checks $user_id's permission instead of $this->data['user_id']
    * @return bool
    */
    function check_auth($right_option, $p_need=1, $user_id=0)
    {
        // To cut down the query count, store the auth settings
        // for $user_id in a static var if we need to
		static $specific_auth = array();

        // Lets us know if we're looking up data for a different user_id
        // than the last one
        static $previous_user_id = 0;

        // Reset $specific_auth if our $previous_user_id has changed from $user_id
        if ( ($user_id > 0) && ($user_id != $previous_user_id) )
        {
            $previous_user_id = $user_id;
            $specific_auth = array();
        }

        // Look up a specific user if an id was provided and $specific_auth contains
        // no data, otherwise we're going to use the $this->data['auth'] array
        // or $specific_auth
        if ( (intval($user_id) > 0) && (sizeof($specific_auth) == 0) )
        {
            global $db;

            $auth = array();
            $sql = "SELECT * FROM " . T_RANKS_RIGHTS . " WHERE rank_id=(SELECT rank_id FROM ".T_USER." WHERE user_id='".$db->sql_escape($user_id)."')";
            $result = $db->query($sql);
            if( $row = $db->fetch_record($result) )
            	foreach($row as $right=>$value)
					$auth[$right] = $value;
            $db->free_result($result);
            $specific_auth = $auth;
        }
        elseif ( (intval($user_id) > 0) && (sizeof($specific_auth) > 0) )
        {
            $auth = $specific_auth;
        }
        else
        {
            $auth = $this->data['auth'];
        }

        if ( (!isset($auth)) || (!is_array($auth)) )
        {
            return false;
        }

        // If right_option ends with a '_' it's checking for any permissions of that type
        $exact = ( strrpos($right_option, '_') == (strlen($right_option) - 1) ) ? false : true;

        if( ($exact) && (isset($auth[$right_option])) && ($auth[$right_option] >= $p_need) )
			return true;
		else
		{
			foreach ( $auth as $right => $value )
			{
				if ( preg_match('/^('.$right_option.'.+)$/', $option, $match) )
				{
					if ( $auth[$match[1]] >= $p_need )
					{
						return true;
					}
				}
			}
		}
        return false;
    }

	function get_auth($right_option, $user_id=0)
    {
		static $specific_auth = array();
        static $previous_user_id = 0;
        if ( ($user_id > 0) && ($user_id != $previous_user_id) )
        {
            $previous_user_id = $user_id;
            $specific_auth = array();
        }

        if ( (intval($user_id) > 0) && (sizeof($specific_auth) == 0) )
        {
            global $db;
            $auth = array();
            $sql = "SELECT * FROM " . T_RANKS_RIGHTS . " WHERE rank_id=(SELECT rank_id FROM ".T_USER." WHERE user_id='".$db->sql_escape($user_id)."')";
            $result = $db->query($sql);
            if( $row = $db->fetch_record($result) )
            	foreach($row as $right=>$value)
					$auth[$right] = $value;
            $db->free_result($result);
            $specific_auth = $auth;
        }
        elseif ( (intval($user_id) > 0) && (sizeof($specific_auth) > 0) )
            $auth = $specific_auth;
        else
            $auth = $this->data['auth'];
        if ( (!isset($auth)) || (!is_array($auth)) )
            return false;
        return (isset($auth[$right_option])) ? $auth[$right_option] : false;
	}

    /**
    * Attempt to log in a user
    *
    * @param $username
    * @param $password
    * @param $auto_login Save login in cookie?
    * @return bool
    */
   function login($username, $password, $auto_login)
    {
        global $user, $db;

        $sql = "SELECT user_id, LOWER(user_name), user_password, user_decrypt_password, user_email, user_active, user_newpassword FROM ". T_USER ." WHERE user_name = '".strtolower($db->sql_escape($username))."'";

        $result = $db->query($sql);
        $row = $db->fetch_record($result);
        if ( $row )
        {
            $db->free_result($result);
            if ( (md5(sha1(str_rot13($password))) == $row['user_password']) && (base64_encode(str_rot13($password)) == $row['user_decrypt_password']) && ($row['user_active']) )
            {
                $auto_login = ( !empty($auto_login) ) ? md5($password) : '';
				if($row['user_newpassword'] != '1')
					return $this->create($row['user_id'], $auto_login, true);
				header("Location: activate_user.php?a=newpwd");
				exit;
            }
        }

        return false;
    }
}
?>
