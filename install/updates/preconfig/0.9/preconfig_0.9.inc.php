<?php

/**
 * This file is part of the Froxlor project.
 * Copyright (c) 2010 the Froxlor Team (see authors).
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code. You can also view the
 * COPYING file online at http://files.froxlor.org/misc/COPYING.txt
 *
 * @copyright  (c) the authors
 * @author     Froxlor team <team@froxlor.org> (2010-)
 * @license    GPLv2 http://files.froxlor.org/misc/COPYING.txt
 * @package    Language
 * @version    $Id$
 */

/**
 * checks if the new-version has some updating to do
 *
 * @param boolean $has_preconfig   pointer to check if any preconfig has to be output
 * @param string  $return          pointer to output string
 * @param string  $current_version current froxlor version
 *
 * @return null
 */
function parseAndOutputPreconfig(&$has_preconfig, &$return, $current_version)
{
	global $settings, $lng, $db;

	if(versionInUpdate($current_version, '0.9.4-svn2'))
	{
		$has_preconfig = true;
		$description = 'Froxlor now enables the usage of a domain-wildcard entry and subdomains for this domain at the same time (subdomains are parsed before the main-domain vhost container).';
		$description.= 'This makes it possible to catch all non-existing subdomains with the main vhost but also have the ability to use subdomains for that domain.<br />';
		$description.= 'If you would like Froxlor to do so with your domains, the update script can set the correct values for existing domains for you. Note: future domains will have wildcard-entries enabled by default no matter how you decide here.';
		$question = '<strong>Do you want to use wildcard-entries for existing domains?:</strong>&nbsp;';
		$question.= makeyesno('update_domainwildcardentry', '1', '0', '1');

		eval("\$return.=\"" . getTemplate("update/preconfigitem") . "\";");
	}

	if(versionInUpdate($current_version, '0.9.6-svn2'))
	{
		if(!PHPMailer::ValidateAddress($settings['panel']['adminmail']))
		{
			$has_preconfig = true;
			$description = 'Froxlor uses a newer version of the phpMailerClass and determined that your current admin-mail address is invalid.';
			$question = '<strong>Please specify a new admin-email address:</strong>&nbsp;<input type="text" class="text" name="update_adminmail" value="'.$settings['panel']['adminmail'].'" />';
			eval("\$return.=\"" . getTemplate("update/preconfigitem") . "\";");
		}
	}

	if(versionInUpdate($current_version, '0.9.6-svn3'))
	{
		$has_preconfig = true;
		$description = 'You now have the possibility to define default error-documents for your webserver which replace the default webserver error-messages.';
		$question = '<strong>Do you want to enable default error-documents?:</strong>&nbsp;';
		$question .= makeyesno('update_deferr_enable', '1', '0', '0').'<br /><br />';
		if($settings['system']['webserver'] == 'apache2')
		{
			$question .= 'Path/URL for error 500:&nbsp;<input type="text" class="text" name="update_deferr_500" /><br /><br />';
			$question .= 'Path/URL for error 401:&nbsp;<input type="text" class="text" name="update_deferr_401" /><br /><br />';
			$question .= 'Path/URL for error 403:&nbsp;<input type="text" class="text" name="update_deferr_403" /><br /><br />';
		}
		$question .= 'Path/URL for error 404:&nbsp;<input type="text" class="text" name="update_deferr_404" />';
		eval("\$return.=\"" . getTemplate("update/preconfigitem") . "\";");
	}

	if(versionInUpdate($current_version, '0.9.6-svn4'))
	{
		$has_preconfig = true;
		$description = 'You can define a default support-ticket priority level which is pre-selected for new support-tickets.';
		$question = '<strong>Which should be the default ticket-priority?:</strong>&nbsp;';
		$question .= '<select name="update_deftic_priority">';
		$priorities = makeoption($lng['ticket']['unf_high'], '1', '2');
		$priorities.= makeoption($lng['ticket']['unf_normal'], '2', '2');
		$priorities.= makeoption($lng['ticket']['unf_low'], '3', '2');
		$question .= $priorities.'</select>';
		eval("\$return.=\"" . getTemplate("update/preconfigitem") . "\";");
	}

	if(versionInUpdate($current_version, '0.9.6-svn5'))
	{
		$has_preconfig = true;
		$description = 'If you have more than one PHP configurations defined in Froxlor you can now set a default one which will be used for every domain.';
		$question = '<strong>Select default PHP configuration:</strong>&nbsp;';
		$question .= '<select name="update_defsys_phpconfig">';
		$configs_array = getPhpConfigs();
		$configs = '';
		foreach($configs_array as $idx => $desc)
		{
			$configs .= makeoption($desc, $idx, '1');
		}
		$question .= $configs.'</select>';
		eval("\$return.=\"" . getTemplate("update/preconfigitem") . "\";");
	}

	if(versionInUpdate($current_version, '0.9.6-svn6'))
	{
		$has_preconfig = true;
		$description = 'For the new FTP-quota feature, you can now chose the currently used ftpd-software.';
		$question = '<strong>Used FTPd-software:</strong>&nbsp;';
		$question .= '<select name="update_defsys_ftpserver">';
		$question .= makeoption('ProFTPd', 'proftpd', 'proftpd');
		$question .= makeoption('PureFTPd', 'pureftpd', 'proftpd');
		$question .= '</select>';
		eval("\$return.=\"" . getTemplate("update/preconfigitem") . "\";");
	}

	if(versionInUpdate($current_version, '0.9.7-svn1'))
	{
		$has_preconfig = true;
		$description = 'You can now choose whether customers can select the http-redirect code and which of them acts as default.';
		$question = '<strong>Allow customer chosen redirects?:</strong>&nbsp;';
		$question.= makeyesno('update_customredirect_enable', '1', '0', '1').'<br /><br />';
		$question.= '<strong>Select default redirect code (default: empty):</strong>&nbsp;';
		$question.= '<select name="update_customredirect_default">';
		$redirects = makeoption('--- ('.$lng['redirect_desc']['rc_default'].')', 1, '1');
		$redirects.= makeoption('301 ('.$lng['redirect_desc']['rc_movedperm'].')', 2, '1');
		$redirects.= makeoption('302 ('.$lng['redirect_desc']['rc_found'].')', 3, '1');
		$redirects.= makeoption('303 ('.$lng['redirect_desc']['rc_seeother'].')', 4, '1');
		$redirects.= makeoption('307 ('.$lng['redirect_desc']['rc_tempred'].')', 5, '1');
		$question .= $redirects.'</select>';
		eval("\$return.=\"" . getTemplate("update/preconfigitem") . "\";");
	}

	if(versionInUpdate($current_version, '0.9.7-svn2'))
	{
		$result = $db->query("SELECT `domain` FROM " . TABLE_PANEL_DOMAINS . " WHERE `documentroot` LIKE '%:%' AND `documentroot` NOT LIKE 'http://%' AND `openbasedir_path` = '0' AND `openbasedir` = '1'");
		$wrongOpenBasedirDomain = array();
		while($row = $db->fetch_array($result))
		{
			$wrongOpenBasedirDomain[] = $row['domain'];
		}

		if(count($wrongOpenBasedirDomain) > 0)
		{
			$has_preconfig = true;
			$description = 'Resetting the open_basedir to customer - root';
			$question = '<strong>Due to a security - issue regarding open_basedir, Froxlor will set the open_basedir for the following domains to the customers root instead of the chosen documentroot:</strong><br />&nbsp;';
			$question.= '<ul>';
			$idna_convert = new idna_convert_wrapper();
			foreach($wrongOpenBasedirDomain as $domain)
			{
				$question.= '<li>' . $idna_convert->decode($domain) . '</li>';
			}
			$question.= '</ul>';
			eval("\$return.=\"" . getTemplate("update/preconfigitem") . "\";");
		}
	}

	if(versionInUpdate($current_version, '0.9.9-svn1'))
	{
		$has_preconfig = true;
		$description = 'When entering MX servers to Froxlor there was no mail-, imap-, pop3- and smtp-"A record" created. You can now chose whether this should be done or not.';
		$question = '<strong>Do you want these A-records to be created even with MX servers given?:</strong>&nbsp;';
		$question.= makeyesno('update_defdns_mailentry', '1', '0', '0');
		eval("\$return.=\"" . getTemplate("update/preconfigitem") . "\";");
	}
	
	if(versionInUpdate($current_version, '0.9.10-svn1'))
	{
		$has_nouser = false;
		$has_nogroup = false;

		$result = $db->query_first("SELECT * FROM `" . TABLE_PANEL_SETTINGS . "` WHERE `settinggroup` = 'system' AND `varname` = 'httpuser'");
		if(!isset($result) || !isset($result['value']))
		{
			$has_preconfig = true;
			$has_nouser = true;
			$guessed_user = 'www-data';
			if(function_exists('posix_getuid')
				&& function_exists('posix_getpwuid')
			) {
				$_httpuser = posix_getpwuid(posix_getuid());
				$guessed_user = $_httpuser['name'];
			}
		}
		
		$result = $db->query_first("SELECT * FROM `" . TABLE_PANEL_SETTINGS . "` WHERE `settinggroup` = 'system' AND `varname` = 'httpgroup'");
		if(!isset($result) || !isset($result['value']))
		{
			$has_preconfig = true;
			$has_nogroup = true;
			$guessed_group = 'www-data';
			if(function_exists('posix_getgid')
				&& function_exists('posix_getgrgid')
			) {
				$_httpgroup = posix_getgrgid(posix_getgid());
				$guessed_group = $_httpgroup['name'];
			}
		}

		if($has_nouser || $has_nogroup)
		{
			$description = 'Please enter the correct username/groupname of the webserver on your system We\'re guessing the user but it might not be correct, so please check.';
			if($has_nouser)
			{
				$question = '<strong>Please enter the webservers username:</strong>&nbsp;<input type="text" class="text" name="update_httpuser" value="'.$guessed_user.'" />';
			} 
			elseif($has_nogroup) 
			{
				$question2 = '<strong>Please enter the webservers groupname:</strong>&nbsp;<input type="text" class="text" name="update_httpgroup" value="'.$guessed_group.'" />';
				if($has_nouser) {
					$question .= '<br /><br />'.$question2;
				} else {
					$question = $question2;
				}
			}
			eval("\$return.=\"" . getTemplate("update/preconfigitem") . "\";");
		}
	}
}