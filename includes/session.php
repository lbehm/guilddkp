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
    var $current_page = '';                     // EQdkp Page       @var current_page

    function start()
    {
        global $SID, $db, $eqdkp;

        $current_time = time();

        $this->ip_address   = ( !empty($_SERVER['REMOTE_ADDR']) )     ? $_SERVER['REMOTE_ADDR']     : $REMOTE_ADDR;
        $this->browser      = ( !empty($_SERVER['HTTP_USER_AGENT']) ) ? $_SERVER['HTTP_USER_AGENT'] : $_ENV['HTTP_USER_AGENT'];
        $this->current_page = ( !empty($_SERVER['REQUEST_URI']) )     ? $_SERVER['REQUEST_URI']     : $_SERVER['SCRIPT_NAME'] . (( isset($_SERVER['QUERY_STRING']) ) ? '?' . $_SERVER['QUERY_STRING'] : '');
        $this->current_page = preg_replace('#^.*?/?.*?/?([a-z\_\-]+?)\.php\?' . URI_SESSION . '=.*?(&.*)?$#', '\1\2', $this->current_page);

        // Check for cookie'd session data
        $cookie_data         = array();
        $cookie_data['sid']  = $this->get_cookie('sid');
        $cookie_data['data'] = $this->get_cookie('data');
        $cookie_data['data'] = ( !empty($cookie_data['data']) ) ? unserialize(stripslashes($cookie_data['data'])) : $cookie_data['data'];

        if ( (isset($cookie_data['sid'])) && (isset($cookie_data['data'])) )
        {
            $session_data = ( isset($cookie_data['data']) ) ? $cookie_data['data'] : '';
            $this->sid    = ( isset($cookie_data['sid']) ) ? $cookie_data['sid'] : '';
            $SID = '?' . URI_SESSION . '=';
        }
        else
        {
            $session_data = array();
            $this->sid    = ( isset($_GET[URI_SESSION]) ) ? $_GET[URI_SESSION] : '';
            $SID = '?' . URI_SESSION . '=' . $this->sid;
        }

        if ( (!empty($this->sid)) || ((isset($_GET[URI_SESSION])) && ($this->sid == $_GET[URI_SESSION])) )
        {
            $sql = 'SELECT u.*, s.*
                    FROM ' . SESSIONS_TABLE . ' s, ' . USERS_TABLE . " u
                    WHERE s.session_id = '".$db->sql_escape($this->sid)."'
                    AND u.user_id = s.session_user_id";
            $result = $db->query($sql);

            $this->data = $db->fetch_record($result);
            $db->free_result($result);

            // Did the session exist in the DB?
            if ( isset($this->data['user_id']) )
            {
                // Validate IP length
                $s_ip = implode('.', array_slice(explode('.', $this->data['session_ip']), 0, 4));
                $u_ip = implode('.', array_slice(explode('.', $this->ip_address),         0, 4));

                if ( $u_ip == $s_ip )
                {
                    // Only update session DB a minute or so after last update or if page changes
                    if ( ($current_time - $this->data['session_current'] > 60) || ($this->data['session_page'] != $this->current_page) )
                    {
                        $sql = 'UPDATE ' . SESSIONS_TABLE . "
                                SET session_current = '" . $current_time . "',
                                    session_page = '" . $db->escape($this->current_page) . "'
                                WHERE session_id = '" . $this->sid . "'";
                        $db->query($sql);
                    }
                    return true;
                }
            }
        }

        // If we reach here then no (valid) session exists.  So we'll create a new one,
        // using the cookie user_id if available to pull basic user prefs.
        // Prevent security vulnerability
        if ( (isset($session_data['auto_login_id'])) && (is_bool($session_data['auto_login_id'])) )
        {
           die('Invalid session data.');
        }

        $auto_login = ( @isset($session_data['auto_login_id']) ) ? $session_data['auto_login_id'] : '';
        $user_id    = ( @isset($session_data['user_id']) )       ? intval($session_data['user_id']) : ANONYMOUS;

        return $this->create($user_id, $auto_login);
    }

    function create(&$user_id, &$auto_login, $set_auto_login = false)
    {
        global $SID, $db, $eqdkp;

        $session_data = array();
        $current_time = time();

        // Remove old sessions and update user information if necessary.
        if ( $current_time - $config['session_cleanup'] > $config['session_last_cleanup'] )
        {
            $this->cleanup($current_time);
        }

        // Grab user data
         $sql = "SELECT u.*, s.session_current
                FROM (`".$db->dbname."`." . USERS_TABLE . " u
                LEFT JOIN `".$db->dbname."`." . SESSIONS_TABLE . " s
                ON s.session_user_id = u.user_id)
                WHERE u.user_id = '".$db->sql_escape($user_id)."'
                ORDER BY s.session_current DESC";


        $result = $db->query($sql);
        $this->data = $db->fetch_record($result);
        $db->free_result($result);
        // Check auto login request to see if it's valid
        if ( empty($this->data) || ($this->data['user_password'] !== $auto_login && !$set_auto_login) || !$this->data['user_active'])
        {
            $auto_login = '';
            $this->data['user_id'] = $user_id = ANONYMOUS;
        }

        // Grab the last visit if there's an existing session
        $this->data['session_last_visit'] = ( !empty($this->data['session_current']) ) ? $this->data['session_current'] : (( !empty($this->data['user_lastvisit']) ) ? $this->data['user_lastvisit'] : time());

        // Create or update the session
        $query = $db->build_query('UPDATE', array(
            'session_user_id'    => $user_id,
            'session_last_visit' => $this->data['session_last_visit'],
            'session_start'      => $current_time,
            'session_current'    => $current_time,
            'session_page'       => $db->escape($this->current_page))
        );
        $sql = 'UPDATE ' . SESSIONS_TABLE . ' SET ' . $query . " WHERE session_id='" . $this->sid . "'";
        if ( ($this->sid == '') || (!$db->query($sql)) || (!$db->affected_rows()) )
        {
            $this->sid = md5(uniqid($this->ip_address));

            $query = $db->build_query('INSERT', array(
                'session_id'         => $this->sid,
                'session_user_id'    => $user_id,
                'session_last_visit' => $this->data['session_last_visit'],
                'session_start'      => $current_time,
                'session_current'    => $current_time,
                'session_ip'         => $this->ip_address,
                'session_page'       => $db->escape($this->current_page))
            );
            $db->query('INSERT INTO `'.$db->dbname.'`.' . SESSIONS_TABLE . $query);
        }

        $this->data['session_id'] = $this->sid;

        $session_data['auto_login_id'] = ( ($auto_login) && ($user_id != ANONYMOUS) )? $auto_login : '';
        $session_data['user_id'] = $user_id;

        $this->set_cookie('data', serialize($session_data), $current_time + 31536000);
        $this->set_cookie('sid', $this->sid, 0);
        $SID = '?' . URI_SESSION . '=' . (( !isset($_COOKIE['sid']) ) ? $this->sid : '');
        return true;
    }

    function destroy()
    {
        global $SID, $db, $eqdkp;

        $current_time = time();

        $this->set_cookie('data', '0', -1);
        $this->set_cookie('sid',  '0', -1);
        $SID = '?' . URI_SESSION . '=';

        // Delete existing session
        $sql = 'UPDATE ' . USERS_TABLE . "
                SET user_lastvisit='" . intval($this->data['session_current']) . "'
                WHERE user_id='" . $this->data['user_id'] . "'";
        $db->query($sql);

        $sql = 'DELETE FROM ' . SESSIONS_TABLE . "
                WHERE session_id='" . $this->sid . "'
                AND session_user_id='" . $this->data['user_id'] . "'";
        $db->query($sql);

        $this->sid = '';

        return true;
    }

    function cleanup(&$current_time)
    {
        global $db, $eqdkp;

        // Get expired sessions, only most recent for each user
        $sql = 'SELECT session_user_id, session_page, MAX(session_current) AS recent_time
                FROM ' . SESSIONS_TABLE . '
                WHERE session_current < ' . ($current_time - $config['session_length']) . '
                GROUP BY session_user_id, session_page';
        $result = $db->query($sql);

        $del_user_id  = '';
        $del_sessions = 0;
        if ( $row = $db->fetch_record($result) )
        {
            do
            {
                if ( intval($row['session_user_id']) != ANONYMOUS )
                {
                    $sql = 'UPDATE ' . USERS_TABLE . "
                            SET user_lastvisit='" . $row['recent_time'] . "', user_lastpage='" . $db->escape($row['session_page']) . "'
                            WHERE user_id = '" . $row['session_user_id'] . "'";
                    $db->query($sql);
                }
                $del_user_id .= ( ($del_user_id != '') ? ', ' : '') . " '" . $row['session_user_id'] . "'";
                $del_sessions++;
            }
            while ( $row = $db->fetch_record($result) );
        }

        if ( $del_user_id != '' )
        {
            // Delete expired sessions
            $sql = 'DELETE FROM ' . SESSIONS_TABLE . "
                    WHERE session_user_id IN ($del_user_id)
                    AND session_current < " . ($current_time - $config['session_length']);
            $db->query($sql);
        }

        if ( $del_sessions < 5 )
        {
            // Less than 5 sessions, update gc timer
            // Otherwise we want cleanup called again to delete other sessions
            $sql = 'UPDATE ' . CONFIG_TABLE . "
                    SET config_value='".$current_time."'
                    WHERE config_name='session_last_cleanup'";
            $db->query($sql);
        }
    }

    function get_cookie($name)
    {
        global $eqdkp;

        $cookie_name = $config['cookie_name'] . '_' . $name;

        return ( isset($_COOKIE[$cookie_name]) ) ? $_COOKIE[$cookie_name] : '';
    }

    function set_cookie($name, $cookie_data, $cookie_time)
    {
        global $eqdkp;

        setcookie($config['cookie_name'] . '_' . $name, $cookie_data, $cookie_time, $config['cookie_path'], $config['cookie_domain']);
    }
}

/**
* User Class
*
* Stores user/global preferences
* and language data
*/

class UserSkel extends Session
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
            $this->lang_name = (file_exists($lang_root_dir.$this->data['user_lang'].'lang_main.php') && is_dir($lang_root_dir.$this->data['user_lang']) ) ? $this->data['user_lang'] : $config['default_lang'];
        else
            $this->lang_name = $config['default_lang'];
        $this->lang_path = $lang_root_dir.$this->lang_name.'/';

        include($this->lang_path . 'lang_main.php');
        if(defined('IN_ADMIN'))
            include($this->lang_path . 'lang_admin.php');
        
        $this->lang = &$lang;

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

        $tpl->set_template($this->style['template_path']);

        //
        // Permissions
        //
        $this->data['auth'] = array();
        if ( $this->data['user_id'] == ANONYMOUS )
        {
            // Get the default permissions if they're not logged in
            $sql = 'SELECT auth_option, auth_default AS auth_value
                    FROM ' . T_AUTH_OPTIONS;
        }
        else
        {
            $sql = "SELECT r.auth_value, o.auth_option
                    FROM " . T_AUTH_RANKS . " r, " . T_AUTH_OPTIONS . " o
                    WHERE (r.auth_id = o.auth_id)
                    AND (r.rank_id = (SELECT user_rank FROM ".T_USER." WHERE user_id = '".$this->data['user_id']."'))";
        }
        if ( !($result = $db->query($sql)) )
        {
            die('Could not obtain permission data');
        }
        while ( $row = $db->fetch_record($result) )
        {
            $this->data['auth'][$row['auth_option']] = $row['auth_value'];
        }
        $db->free_result($row);
        return;
    }

    /**
    * Checks if a user has permission to do ($auth_value)
    *
    * @param $auth_value Permission we want to check
    * @param $die If they don't have permission, exit with message_die or just return false?
    * @param $user_id If set, checks $user_id's permission instead of $this->data['user_id']
    * @return bool
    */
    function check_auth($auth_value, $die = true, $user_id = 0)
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
            $sql = "SELECT r.auth_value, o.auth_option
                    FROM " . T_AUTH_RANKS . " r, " . T_AUTH_OPTIONS . " o
                    WHERE (r.auth_id = o.auth_id) AND (r.rank_id = (SELECT user_rank FROM ".T_USER." WHERE user_id = '".$db->sql_escape($user_id)."'))";
            $result = $db->query($sql);
            while ( $row = $db->fetch_record($result) )
            {
                $auth[$row['auth_option']] = $row['auth_value'];
            }
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
            return ( $die ) ? message_die($this->lang['noauth_default_title'], $this->lang['noauth_default_title']) : false;
        }

        // If auth_value ends with a '_' it's checking for any permissions of that type
        $exact = ( strrpos($auth_value, '_') == (strlen($auth_value) - 1) ) ? false : true;

        foreach ( $auth as $option => $value )
        {
            if ( $exact )
            {
                if ( ($option == $auth_value) && ($value == 'Y') )
                {
                    return true;
                }
            }
            else
            {
                if ( preg_match('/^('.$auth_value.'.+)$/', $option, $match) )
                {
                    if ( $auth[$match[1]] == 'Y' )
                    {
                        return true;
                    }
                }
            }
        }

        $index = ( $exact ) ? (( isset($this->lang['noauth_'.$auth_value]) ) ? 'noauth_'.$auth_value : 'noauth_default_title') : 'noauth_default_title';

        return ( $die ) ? message_die($this->lang[$index], $this->lang['noauth_default_title']) : false;
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

        $sql = 'SELECT user_id, user_name, user_password, user_decrypt_password, user_email, user_active, user_newpassword
                FROM `'.$db->dbname.'`.'. USERS_TABLE . "
                WHERE user_name='".$db->sql_escape($username)."'";

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

    /**
     * Static function to abstract password encryption
     *
     * @param string $string String to encrypt
     * @param string $salt Salt value; not yet in use
     * @return string
     * @static
     */
    function Encrypt($string, $salt = '')
    {
        return md5($salt . $string);
    }
}
?>
