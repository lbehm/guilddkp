<?php
/*
 *BBCode 2 HTML
 */
include_once('wow_convert.php');
function bbDeCode($string) {
	$preg = array(
		'/(?<!\\\\)\[color(?::\w+)?=(.*?)\](.*?)\[\/color(?::\w+)?\]/si'   => "<span style=\"color:\\1\">\\2</span>",
		'/(?<!\\\\)\[size(?::\w+)?=(.*?)\](.*?)\[\/size(?::\w+)?\]/si'     => "<span style=\"font-size:\\1\">\\2</span>",
		'/(?<!\\\\)\[font(?::\w+)?=(.*?)\](.*?)\[\/font(?::\w+)?\]/si'     => "<span style=\"font-family:\\1\">\\2</span>",
		'/(?<!\\\\)\[align(?::\w+)?=(.*?)\](.*?)\[\/align(?::\w+)?\]/si'   => "<div style=\"text-align:\\1\">\\2</div>",
		'/(?<!\\\\)\[b(?::\w+)?\](.*?)\[\/b(?::\w+)?\]/si'                 => "<span style=\"font-weight:bold\">\\1</span>",
		'/(?<!\\\\)\[i(?::\w+)?\](.*?)\[\/i(?::\w+)?\]/si'                 => "<span style=\"font-style:italic\">\\1</span>",
		'/(?<!\\\\)\[u(?::\w+)?\](.*?)\[\/u(?::\w+)?\]/si'                 => "<span style=\"text-decoration:underline\">\\1</span>",
		'/(?<!\\\\)\[center(?::\w+)?\](.*?)\[\/center(?::\w+)?\]/si'       => "<div style=\"text-align:center\">\\1</div>",
		// [code] & [php]
		'/(?<!\\\\)\[code(?::\w+)?\](.*?)\[\/code(?::\w+)?\]/si'           => "<div class=\"bb-code\">\\1</div>",
		'/(?<!\\\\)\[php(?::\w+)?\](.*?)\[\/php(?::\w+)?\]/si'             => "<div class=\"bb-php\">\\1</div>",
		// [email]
		'/(?<!\\\\)\[email(?::\w+)?\](.*?)\[\/email(?::\w+)?\]/si'         => "<a href=\"mailto:\\1\" class=\"bb-email\">\\1</a>",
		'/(?<!\\\\)\[email(?::\w+)?=(.*?)\](.*?)\[\/email(?::\w+)?\]/si'   => "<a href=\"mailto:\\1\" class=\"bb-email\">\\2</a>",
		// [url]
		'/(?<!\\\\)\[url(?::\w+)?\]www\.(.*?)\[\/url(?::\w+)?\]/si'        => "<a href=\"http://www.\\1\" target=\"_blank\" class=\"bb-url\">\\1</a>",
		'/(?<!\\\\)\[url(?::\w+)?\](.*?)\[\/url(?::\w+)?\]/si'             => "<a href=\"\\1\" target=\"_blank\" class=\"bb-url\">\\1</a>",
		'/(?<!\\\\)\[url(?::\w+)?=(.*?)?\](.*?)\[\/url(?::\w+)?\]/si'      => "<a href=\"\\1\" target=\"_blank\" class=\"bb-url\">\\2</a>",
		// [img]
		'/(?<!\\\\)\[img(?::\w+)?\](.*?)\[\/img(?::\w+)?\]/si'             => "<img src=\"\\1\" alt=\"\\1\" class=\"bb-image\" />",
		'/(?<!\\\\)\[img(?::\w+)?=(.*?)x(.*?)\](.*?)\[\/img(?::\w+)?\]/si' => "<img width=\"\\1\" height=\"\\2\" src=\"\\3\" alt=\"\\3\" class=\"bb-image\" />",
		// [quote]
		'/(?<!\\\\)\[quote(?::\w+)?\](.*?)\[\/quote(?::\w+)?\]/si'         => "<div>Quote:<div class=\"bb-quote\">\\1</div></div>",
		'/(?<!\\\\)\[quote(?::\w+)?=(?:&quot;|"|\')?(.*?)["\']?(?:&quot;|"|\')?\](.*?)\[\/quote\]/si'   => "<div>Quote \\1:<div class=\"bb-quote\">\\2</div></div>",
		// [list]
		'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[\*(?::\w+)?\](.*?)(?=(?:\s*<br\s*\/?>\s*)?\[\*|(?:\s*<br\s*\/?>\s*)?\[\/?list)/si' => "\n<li class=\"bb-listitem\">\\1</li>",
		'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[\/list(:(?!u|o)\w+)?\](?:<br\s*\/?>)?/si'    => "\n</ul>",
		'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[\/list:u(:\w+)?\](?:<br\s*\/?>)?/si'         => "\n</ul>",
		'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[\/list:o(:\w+)?\](?:<br\s*\/?>)?/si'         => "\n</ol>",
		'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(:(?!u|o)\w+)?\]\s*(?:<br\s*\/?>)?/si'   => "\n<ul class=\"bb-list-unordered\">",
		'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list:u(:\w+)?\]\s*(?:<br\s*\/?>)?/si'        => "\n<ul class=\"bb-list-unordered\">",
		'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list:o(:\w+)?\]\s*(?:<br\s*\/?>)?/si'        => "\n<ol class=\"bb-list-ordered\">",
		'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(?::o)?(:\w+)?=1\]\s*(?:<br\s*\/?>)?/si' => "\n<ol class=\"bb-list-ordered,bb-list-ordered-d\">",
		'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(?::o)?(:\w+)?=i\]\s*(?:<br\s*\/?>)?/s'  => "\n<ol class=\"bb-list-ordered,bb-list-ordered-lr\">",
		'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(?::o)?(:\w+)?=I\]\s*(?:<br\s*\/?>)?/s'  => "\n<ol class=\"bb-list-ordered,bb-list-ordered-ur\">",
		'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(?::o)?(:\w+)?=a\]\s*(?:<br\s*\/?>)?/s'  => "\n<ol class=\"bb-list-ordered,bb-list-ordered-la\">",
		'/(?<!\\\\)(?:\s*<br\s*\/?>\s*)?\[list(?::o)?(:\w+)?=A\]\s*(?:<br\s*\/?>)?/s'  => "\n<ol class=\"bb-list-ordered,bb-list-ordered-ua\">",
		// escaped tags like \[b], \[color], \[url], ...
		'/\\\\(\[\/?\w+(?::\w+)*\])/'                                      => "\\1"
	);
	$string = preg_replace(array_keys($preg), array_values($preg), $string);

	// [user]
	$string = preg_replace_callback('/\[user=(.*?)\]/msi', function($id=false){
		global $db, $config;
		$id = mb_strtolower($id[1], 'UTF-8');
		if($id)
		{
			$query = $db->query("SELECT user_displayname, MD5(user_email) as hash, user_icon FROM ".T_USER." WHERE user_name = '".$db->sql_escape($id)."';");
			if($user = $db->fetch_record($query))
				return '<a href="user-'.$id.'" class="bb_user" style="background-image: url(\''.(($user['user_icon'] != '')? $user['user_icon']:"http://www.gravatar.com/avatar/".$user['hash']."?d=identicon").'\');">'.$user['user_displayname'].'</a>';
			else
				return '<a href="user-Unbekannt" class="bb_user" style="background-image: url(\''.$config->get('icon_repo_tiny').'inv_misc_questionmark.gif\');">Unbekannt</a>';
		}
	}, $string);
	
	// [char]
	$string = preg_replace_callback('/\[char=(.*?)\]/msi', function($id=false){
		global $db, $config, $classes;
		$id = $id[1];//mb_strtolower($id[1], 'UTF-8');
		if($id)
		{
			$query = $db->query("SELECT char_level, char_race_id, char_class_id FROM ".T_CHAR." WHERE char_name = '".$db->sql_escape($id)."';");
			if($char = $db->fetch_record($query))
				return '<a href="char-'.$id.'" class="bb_char" style="background-image: url(\''.$config->get('icon_repo_tiny').$classes[$char['char_class_id']]['icon'].'.gif\');">'.$id.'</a>';
			else
				return '<a href="chars" class="bb_char" style="background-image: url(\''.$config->get('icon_repo_tiny').'inv_misc_questionmark.gif\');">Unbekannt</a>';
		}
	}, $string);
	
	// [item]
	$string = preg_replace_callback('/(?<!\\\\)\[item=(.*?)\]/msi', function($id=false){
		global $db, $config;
		$id = mb_strtolower($id[1], 'UTF-8');
		if($id){
			$query = $db->query("SELECT name, quality, icon FROM ".T_ITEMS." WHERE id = '".$db->sql_escape($id)."';");
			if($item = $db->fetch_record($query))
				return '<a href="item='.$id.'" class="bb_item item_quali_'.$item['quality'].'" style="background-image: url(\''.$config->get('icon_repo_tiny').$item['icon'].'.gif\');">'.$item['name'].'</a>';
			else
				return '<a href="items" class="bb_item item_quali_0" style="background-image: url(\''.$config->get('icon_repo_tiny').'inv_misc_questionmark.gif\');">Unbekannter Gegenstand</a>';
		}
	}, $string);
	
	// [race]
	$string = preg_replace_callback('/(?<!\\\\)\[race=(.*?)\]/msi', function($id=false){
		global $db, $config, $races;
		$id = mb_strtolower($id[1], 'UTF-8');
		if($races[$id])
			return '<a href="race='.$id.'" class="bb_race" style="background-image:url(\''.$config->get('icon_repo_tiny').$races[$id]['icon_m'].'.gif\');">'.$races[$id]['lang'].'</a>';
	}, $string);
	
	// [class]
	$string = preg_replace_callback('/(?<!\\\\)\[class=(.*?)\]/msi', function($id=false){
		global $db, $config, $classes;
		$id = mb_strtolower($id[1], 'UTF-8');
		if($classes[$id])
			return '<a href="race='.$id.'" class="bb_class" style="background-image:url(\''.$config->get('icon_repo_tiny').$classes[$id]['icon'].'.gif\');">'.$classes[$id]['name'].'</a>';
	}, $string);
	
	// [skill]
	$string = preg_replace_callback('/(?<!\\\\)\[skill=(.*?)\]/msi', function($id=false)
		{
			global $db, $config, $skill;$id = mb_strtolower($id[1], 'UTF-8');
			if($skill[$id])
				return '<a href="http://de.wowhead.com/skill='.$id.'" target="_blank" class="bb_skill" style="background-image:url(\''.$config->get('icon_repo_tiny').$skill[$id]['icon'].'.gif\');">'.$skill[$id]['lang'].'</a>';
		},$string);
	
	return $string;
}
?>