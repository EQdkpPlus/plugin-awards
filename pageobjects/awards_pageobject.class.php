<?php
/*	Project:	EQdkp-Plus
 *	Package:	Awards  Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/*+----------------------------------------------------------------------------
  | awards_pageobject
  +--------------------------------------------------------------------------*/
class awards_pageobject extends pageobject
{
	/**
	  * Constructor
	  */
	public function __construct(){
		// plugin installed?
		if (!$this->pm->check('awards', PLUGIN_INSTALLED))
		  message_die($this->user->lang('aw_plugin_not_installed'));

		$this->user->check_auth('u_awards_view');

		$handler = array(
			#'get_table'		=> array('process' => 'set_cookie', 'check' => 'u_awards_view'),
		);
		parent::__construct(false, $handler);
		$this->process();
	}
	
/*	// Dont use it now, we use a hardcoded variable while the development and testing
	private function set_cookie(){
		//dont set cookies if we dont have a cookie-name or cookie-path
		$cname = register('config')->get('cookie_name');
		$cpath = register('config')->get('cookie_path');
		if(empty($cname) || empty($cpath)) return;
		setcookie( $cname . '_awards', 1, 604800, $cpath, register('config')->get('cookie_domain'));
	}
*/

	/**
	  * Display
	  * display all achievements
	  */
	public function display(){






		$this->tpl->assign_vars(array(
			'AW_TITLE'			=> '',
			'AW_COUNT'			=> '',
		));

		// -- EQDKP ---------------------------------------------------------------
		$this->core->set_vars(array(
			'page_title'    => $this->user->lang('awards'),
			'template_path' => $this->pm->get_data('awards', 'template_path'),
			'template_file' => 'awards.html',
			'display'       => true
		));

	}
}
?>