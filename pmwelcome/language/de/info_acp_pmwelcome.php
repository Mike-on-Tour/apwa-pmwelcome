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
	'ACP_PMWELCOME'					=> 'Begr&uuml;&szlig;ungsnachricht',
	'ACP_PMWELCOME_EXPLAIN'			=> 'Du kannst hier den Text der PN eingeben, die einem neuen Nutzer nach der Registrierung zugesandt wird.',
	'ACP_PMWELCOME_SETTINGS'		=> 'Einstellungen f&uuml;r die Begr&uuml;&szlig;ungsnachricht.',
	'ACP_PMWELCOME_USER'			=> 'Absender',
	'ACP_PMWELCOME_USER_EXPLAIN'	=> 'UserID desjenigen, in dessen Name die PN versandt wird.',
	'ACP_PMWELCOME_SUBJECT'			=> 'Betreff',
	'ACP_PMWELCOME_TEXT'			=> 'Text der Begr&uuml;&szlig;ungsnachricht',
	'ACP_PMWELCOME_TEXT_EXPLAIN'	=> 'Du kannst bbcode und smilies, sowie den Platzhalter {USERNAME} als Ersatz f&uuml;r den Namen des Empf&auml;ngers der PN benutzen.',
));
