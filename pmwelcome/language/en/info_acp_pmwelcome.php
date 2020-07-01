<?php

/**
*
* @package PM Welcome [English]
* @copyright BB3.MOBi (c) 2015 Anvar http://apwa.ru
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_PMWELCOME'					=> 'Welcome message',
	'ACP_PMWELCOME_EXPLAIN'			=> 'You can specify the text of the personal message that will be sent to the user upon activation or registration.',
	'ACP_PMWELCOME_SETTINGS'		=> 'Welcome message settings',
	'ACP_PMWELCOME_USER'			=> 'Sender',
	'ACP_PMWELCOME_USER_EXPLAIN'	=> 'User ID of the member on whose\'s behalf the message will be sent.',
	'ACP_PMWELCOME_SUBJECT'			=> 'Post subject',
	'ACP_PMWELCOME_TEXT'			=> 'Text of the welcome message',
	'ACP_PMWELCOME_TEXT_EXPLAIN'	=> 'You can use bbcode, smilies and the following tokens:<br>
										{USERNAME} to replace the name of the user who is to receive the private message,
										{SITE_NAME} to display your boards name (URL),
										{SITE_DESC} to display your boards description,
										{USER_REGDATE} users date of registering,
										{USER_EMAIL} e-mail address of the addressee,
										{USER_TZ} for the users time zone,
										{USER_LANG_LOCAL} for the user choosen language,
										{BOARD_CONTACT} your boards contact e-mail address',
));
