<?php
/**
*
* @package PM Welcome
* @copyright (c) bb3.mobi 2014 Anvar
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace apwa\pmwelcome\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use phpbb\language\language;
use phpbb\language\language_file_loader;

class listener implements EventSubscriberInterface
{
	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string phpbb_root_path */
	protected $phpbb_root_path;

	/** @var string phpEx */
	protected $php_ext;

	public function __construct(\phpbb\user $user, \phpbb\config\config $config, \phpbb\config\db_text $config_text, \phpbb\db\driver\driver_interface $db, $phpbb_root_path, $php_ext)
	{
		$this->user = $user;
		$this->config = $config;
		$this->text = $config_text;
		$this->db = $db;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.user_active_flip_after'		=> 'pm_activate_welcome',		// Event after activation by admin or by email
			'core.ucp_register_register_after'	=> 'pm_register_welcome',		// Event after registration, used to process user data for the Usermap if no activation after registration is needed
		);
	}

	/**
	* Called after a user got activated/deactivated
	*
	* @params:	activated, deactivated, mode, reason, sql_statements, user_id_ary
	*/
	public function pm_activate_welcome($event)
	{
		$user_id_ary = $event['user_id_ary'];
		foreach ($user_id_ary as $value)
		{
			$query = 'SELECT *
					FROM ' . USERS_TABLE . '
					WHERE user_id = ' . (int) $value;
			$result = $this->db->sql_query($query);
			$user_data = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			if ($event['activated'] == 1 && $user_data['user_new'] == 1)	// only new users receive the welcome PM during activation by admin or by email
			{
				$this->user_welcome($user_data);
			}
		}
	}

	/*
	* Called after a user has finished registration. Three possible scenarios:
	* 1. w/o activation: Data for the Usermap must be obtained here
	* 2. and 3.: Activation by eMail confirmation or ba an administrator: Data for the Usermap will be processed within the function 'pm_activate_welcome'
	* -> selection for registration w/o later activation must be done here!
	*
	* @param:	cp_data, data, message, server_url, user_actkey, user_id, user_row
	*/
	function pm_register_welcome($event)
	{
		$user_row = $event['user_row'];
		if ($user_row['user_actkey'] == '' && $user_row['user_inactive_reason'] == 0 && $user_row['user_inactive_time'] == 0)	// conditions if user registration w/o activation
		{
			$query = 'SELECT *
					FROM ' . USERS_TABLE . '
					WHERE user_id = ' . (int) $event['user_id'];
			$result = $this->db->sql_query($query);
			$user_data = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
			$this->user_welcome($user_data);
		}
	}

	/** User PM welcome message */
	private function user_welcome($user_to)
	{
		$pwm_user = $this->config['pmwelcome_user'];
		$pwm_subject = $this->config['pmwelcome_subject'];
		$pwm_text = $this->text->get('pmwelcome_post_text');
		if ($pwm_user && $pwm_subject && $pwm_text)
		{
			$m_flags = 3; // 1 is bbcode, 2 is smiles, 4 is urls (add together to turn on more than one)
			$uid = $bitfield = '';
			$allow_bbcode = $allow_urls = $allow_smilies = true;

			$pwm_text = str_replace('{USERNAME}', $user_to['username'], $pwm_text);
			$pwm_text = str_replace('{SITE_NAME}', $this->config['sitename'], $pwm_text);
			$pwm_text = str_replace('{SITE_DESC}', $this->config['site_desc'], $pwm_text);
			$user_regdate = $this->format_date_time($user_to['user_lang'], $user_to['user_timezone'], $user_to['user_dateformat'], $user_to['user_regdate']);
			$pwm_text = str_replace('{USER_REGDATE}', $user_regdate, $pwm_text);
			$pwm_text = str_replace('{USER_EMAIL}', $user_to['user_email'], $pwm_text);
			$pwm_text = str_replace('{USER_TZ}', $user_to['user_timezone'], $pwm_text);
			$pwm_text = str_replace('{USER_LANG_LOCAL}', $user_to['user_lang'], $pwm_text);
			$pwm_text = str_replace('{BOARD_CONTACT}', $this->config['board_contact'], $pwm_text);

			generate_text_for_storage($pwm_text, $uid, $bitfield, $m_flags, $allow_bbcode, $allow_urls, $allow_smilies);

			include_once($this->phpbb_root_path . 'includes/functions_privmsgs.' . $this->php_ext);

			$pm_data = array(
				'address_list'		=> array('u' => array($user_to['user_id'] => 'to')),
				'from_user_id'		=> $pwm_user,
				'from_user_ip'		=> $this->user->ip,
				'enable_sig'		=> false,
				'enable_bbcode'		=> $allow_bbcode,
				'enable_smilies'	=> $allow_smilies,
				'enable_urls'		=> $allow_urls,
				'icon_id'			=> 0,
				'bbcode_bitfield'	=> $bitfield,
				'bbcode_uid'		=> $uid,
				'message'			=> utf8_normalize_nfc($pwm_text),
			);

			submit_pm('post', utf8_normalize_nfc($pwm_subject), $pm_data, false);
		}
	}

	/*
	* @param string	$user_lang			addressed user's language
	* @param string	$user_timezone		addressed user's time zone
	* @param string	$user_dateformat	addressed user's date/time format
	* @param int	$user_timestamp		addressed user's php timestamp (registration date, last login, reminder mails as UNIX timestamp from users table)
	*
	* @return string	the timestamp in user's choosen date/time format and time zone as DateTime string
	*/
	private function format_date_time($user_lang, $user_timezone, $user_dateformat, $user_timestamp)
	{
		$default_tz = date_default_timezone_get();
		$date = new \DateTime('now', new \DateTimeZone($default_tz));
		$date->setTimestamp($user_timestamp);
		$date->setTimezone(new \DateTimeZone($user_timezone));
		$time = $date->format($user_dateformat);

		// Instantiate a new language class (with its own loader), set the user's chosen language and translate the date/time string
		$lang = new language(new language_file_loader($this->phpbb_root_path, $this->php_ext));
		$lang->set_user_language($user_lang);

		// Find all words in date/time string and replace them with the translations from user's language
		preg_match_all("/[a-zA-Z]+/", $time, $matches, PREG_PATTERN_ORDER);
		if (sizeof ($matches[0]) > 0)
		{
			foreach ($matches[0] as $value)
			{
				$time = preg_replace("/".$value."/", $lang->lang(array('datetime', $value)), $time);
			}
		}

		// return the formatted and translated time in users timezone
		return $time;
	}

}
