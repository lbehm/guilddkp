<?php
/*CONFIG-FILE

only an array with config-variables
NO DB-Connection-Informations!!
*/
if(!defined('intern'))
{
    die('Do not access this file directly.');
}
$config = array(
	'main_page'	=>	'viewnews.php',
	'title'	=>	'Guild-DKP',
	'online'	=>	true,
	'offline_msg'	=>	'Die Seite ist zur Zeit nicht verfügbar! Bitte versuchen Sie es später wieder. Danke!',
	'default_lang'	=>	'de_de',
	'default_template'	=>	'clean'
);
$ids_logs = array(
	'Database' => false,
	'Email' => false,
	'File' => true
);
?>
