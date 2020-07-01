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
	'ACP_PMWELCOME'					=> 'Begrüßungsnachricht',
	'ACP_PMWELCOME_EXPLAIN'			=> 'Sie können hier den Text der PN eingeben, die einem neuen Nutzer nach der Aktivierung bzw. Registrierung zugesandt wird.',
	'ACP_PMWELCOME_SETTINGS'		=> 'Einstellungen für die Begrüßungsnachricht',
	'ACP_PMWELCOME_USER'			=> 'Absender',
	'ACP_PMWELCOME_USER_EXPLAIN'	=> 'UserID desjenigen, in dessen Name die PN versandt wird.',
	'ACP_PMWELCOME_SUBJECT'			=> 'Betreff',
	'ACP_PMWELCOME_TEXT'			=> 'Text der Begrüßungsnachricht',
	'ACP_PMWELCOME_TEXT_EXPLAIN'	=> 'Sie können BBCode und Smilies, sowie folgende Platzhalter verwenden:<br>
										{USERNAME} als Ersatz für den Namen des Empfängers der PN,
										{SITE_NAME} für den Namen Ihres Boards,
										{SITE_DESC} als Beschreibung Ihres Boards,
										{USER_REGDATE} für das Registrierungsdatum des Adressaten,
										{USER_EMAIL} für die eMail-Adresse des Adressaten,
										{USER_TZ} für die Zeitzone des Adressaten,
										{USER_LANG_LOCAL} für die Sprache des Adressaten,
										{BOARD_CONTACT} als eMail-Adresse des Absenders',
));
