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

	/** @var string table prefix */
	protected $table_prefix;

	public function __construct(\phpbb\user $user, \phpbb\config\config $config, \phpbb\config\db_text $config_text, \phpbb\db\driver\driver_interface $db, $phpbb_root_path, $php_ext, $table_prefix)
	{
		$this->user = $user;
		$this->config = $config;
		$this->text = $config_text;
		$this->db = $db;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->table_prefix = $table_prefix;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.ucp_activate_after'		=> 'pm_test',
			'core.user_add_after'			=> 'pm_welcome',
			'core.user_active_flip_after'	=> 'pm_activate_welcome',
		);
	}

	/*
	* function should be called after user account's activation, but this isn't the case with all 3 activation methods (by admin, by e-mail and activation durin registration)
	*/
	public function pm_test($event)
	{
/*		echo 'core.ucp_activate_after:<br>';
		print_r($event['user_row']);
		echo '<br>';
		echo $event['message'];*/
	}

	/*
	* function is called after registration of a new user
	*
	*/
	public function pm_welcome($event)
	{
		echo 'core.user_add_after:<br>';
		$user_row = $event['user_row'];
		if ($user_row['user_type'] == USER_NORMAL) // since a newly registrated user isn't a "normal" user, the following code is not processed
		{
			$pwm_user = $this->config['pmwelcome_user'];
			$pwm_subject = $this->config['pmwelcome_subject'];
			$pwm_text = $this->text->get('pmwelcome_post_text');
			if ($pwm_user && $pwm_subject && $pwm_text)
			{
				$user_to = array(
					'user_id'	=> $event['user_id'],
					'username'	=> $user_row['username'],
				);

				$this->user_welcome($user_to, $pwm_user, $pwm_subject, $pwm_text);
			}
		}
	}

	public function pm_activate_welcome($event)
	{
		$user_id_ary = $event['user_id_ary'];
		$query = 'SELECT *
				FROM ' . USERS_TABLE . '
				WHERE user_id = ' . (int) $user_id_ary[0];
		$result = $this->db->sql_query($query);
		$user_row = $result->fetch_array(MYSQLI_ASSOC);
		if ($event['activated'] == 1)
		{
			$pwm_user = $this->config['pmwelcome_user'];
			$pwm_subject = $this->config['pmwelcome_subject'];
			$pwm_text = $this->text->get('pmwelcome_post_text');
			if ($pwm_user && $pwm_subject && $pwm_text)
			{
				$user_to = array(
					'user_id'		=> $user_row['user_id'],
					'username'		=> $user_row['username'],
					'site_name'		=> $this->config['sitename'],
					'site_desc'		=> $this->config['site_desc'],
					'regdate'		=> date('d\.m\.Y, H\:i', $user_row['user_regdate']),
					'email'			=> $user_row['user_email'],
					'timezone'		=> $user_row['user_timezone'],
					'language'		=> $user_row['user_lang'],
					'board_contact'	=> $this->config['board_contact'],
				);

				$this->user_welcome($user_to, $pwm_user, $pwm_subject, $pwm_text);
			}
		}
	}

	/** User PM welcome message */
	private function user_welcome($user_to, $user_id, $subject, $text)
	{
		$m_flags = 3; // 1 is bbcode, 2 is smiles, 4 is urls (add together to turn on more than one)
		$uid = $bitfield = '';
		$allow_bbcode = $allow_urls = $allow_smilies = true;

		$text = str_replace('{USERNAME}', $user_to['username'], $text);
		$text = str_replace('{SITE_NAME}', $user_to['site_name'], $text);
		$text = str_replace('{SITE_DESC}', $user_to['site_desc'], $text);
		$text = str_replace('{USER_REGDATE}', $user_to['regdate'], $text);
		$text = str_replace('{USER_EMAIL}', $user_to['email'], $text);
		$text = str_replace('{USER_TZ}', $user_to['timezone'], $text);
		$text = str_replace('{USER_LANG_LOCAL}', $user_to['language'], $text);
		$text = str_replace('{BOARD_CONTACT}', $user_to['board_contact'], $text);

		generate_text_for_storage($text, $uid, $bitfield, $m_flags, $allow_bbcode, $allow_urls, $allow_smilies);

		include_once($this->phpbb_root_path . 'includes/functions_privmsgs.' . $this->php_ext);

		$pm_data = array(
			'address_list'		=> array('u' => array($user_to['user_id'] => 'to')),
			'from_user_id'		=> $user_id,
			'from_user_ip'		=> $this->user->ip,
			'enable_sig'		=> false,
			'enable_bbcode'		=> $allow_bbcode,
			'enable_smilies'	=> $allow_smilies,
			'enable_urls'		=> $allow_urls,
			'icon_id'			=> 0,
			'bbcode_bitfield'	=> $bitfield,
			'bbcode_uid'		=> $uid,
			'message'			=> utf8_normalize_nfc($text),
		);

		submit_pm('post', utf8_normalize_nfc($subject), $pm_data, false);
	}
}
